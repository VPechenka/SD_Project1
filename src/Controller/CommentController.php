<?php

namespace App\Controller;


use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends AbstractController
{
    public function postPost(
        string                 $post_id,
        Request                $request,
        Security               $security,
        EntityManagerInterface $entityManager,
    ): Response
    {
        return $this->saveComment($post_id, $request, $security, $entityManager);
    }

    public function postComment(
        string                 $post_id,
        string                 $comment_id,
        Request                $request,
        Security               $security,
        EntityManagerInterface $entityManager
    ): Response
    {
        if (!filter_var($comment_id, FILTER_VALIDATE_INT)) {
            return new Response('Неправильные входные данные', Response::HTTP_NOT_FOUND);
        }

        $comment = $entityManager->getRepository(Comment::class)->find((int)$comment_id);
        if (!$comment) {
            return new Response('Комментарий не найден', Response::HTTP_NOT_FOUND);
        }
        return $this->saveComment($post_id, $request, $security, $entityManager, $comment);
    }

    public function postDelete(
        string                 $comment_id,
        request                $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        if  (!filter_var($comment_id, FILTER_VALIDATE_INT)) {
            return new Response("Неправильные входные данные",  Response::HTTP_NOT_FOUND);
        }

        $comment = $entityManager->getRepository(Comment::class)->find((int)$comment_id);

        $comment->setIsDeleted(true);

        $entityManager->flush();

        return $this->redirectToRoute("getPost", [
            "post_id" => $comment->getPost()->getId(),
        ]);
    }

//  << Private methods >>

    private function saveComment(
        string                 $post_id,
        Request                $request,
        Security               $security,
        EntityManagerInterface $entityManager,
        Comment                $parent = null
    ): Response
    {
        if (
            empty($request->request->get('text')) ||
            filter_var($post_id, FILTER_VALIDATE_INT) === false
        )
            return new Response(
                'Неправильные входные данные',
                Response::HTTP_BAD_REQUEST
            );


        $post = $entityManager->getRepository(Post::class)->find((int)$post_id);
        if (!$post) {
            return new Response('Пост не найден', Response::HTTP_NOT_FOUND);
        }

        $user = $security->getUser();
        if (!$user) {
            return new Response('Пользователь не авторизован', Response::HTTP_UNAUTHORIZED);
        }

        $comment = new Comment();
        $comment->setText($request->request->get('text'));
        $comment->setPost($post);
        $comment->setUser($user);
        $comment->setCreatedAtNow();

        if ($parent !== null) {
            $comment->setParent($parent);
        }

        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('getPost', ['post_id' => $post->getId()]);
    }
}
