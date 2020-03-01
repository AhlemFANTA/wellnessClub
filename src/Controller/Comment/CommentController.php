<?php

namespace App\Controller\Comment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\CommentType;
use App\Entity\Comment;
use App\Service\CommentSubmitter;

class CommentController extends AbstractController
{
  /**
   * @Route("/article/{id}", name= "get_comments")
   */
  public function getComments(string $id, Request $request, CommentSubmitter $commentSubmitter): Response
  {
    $comment = new Comment();
    $form = $this->createForm(CommentType::class, $comment);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      // submit form data to database
      $commentData = $form->getData();

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($commentData);
      $entityManager->flush();
    };
    // get all comments
    $comments = $this->getDoctrine()
    ->getRepository(Comment::class)
    ->findAll();

    return $this->render('comment/commentBlock.html.twig',
    array('id'=>$id, 'form'=>$form->createView(), 'comments'=>$comments));
  }

  /**
   * @Route("/article/{id}/comment/{comment_id}/like")
   */
   public function likeComment(string $id, string $comment_id)
   {
     $entityManager = $this->getDoctrine()->getManager();
     $comment = $entityManager->getRepository(Comment::class)->find($comment_id);
     $likes = $comment->getLikes();
     $comment->setLikes($likes+1);
     $entityManager->flush();
     return $this->redirectToRoute('get_comments', ['id'=>$id]);
   }
}
