<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostForm;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends AbstractController
{
    public function getList(
        PostRepository $postRepository
    ): Response
    {
        $posts = $postRepository->findWithCountStats();

        return $this->render('post/list.html.twig', [
            'posts' => $posts,
        ]);
    }

    public function getForm(): Response
    {
        $post = new Post();
        $form = $this->createForm(PostForm::class, $post);

        return $this->render('post/create-form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function post(
        Request                $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $post = new Post();

        $post->setCreatedAtNow();

        $form = $this->createForm(PostForm::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $entityManager->persist($post);
                    $entityManager->flush();

                    if ($post->getId() === null) {
                        $error = 'Ошибка сохранения данных в базу. Попробуйте снова';
                    } else {
                        $url = $post->getUrl();
                    }
                } catch (ORMException $ex) {
                    $error = 'Ошибка сохранения данных в базу. Попробуйте снова';
                }
            } else
                foreach ($form->getErrors(true) as $error) {
                    $error = $error->getMessage();
                    break;
                }
        }

        return $this->render('post/create-form.html.twig', [
            'form' => $form->createView(),
            'url' => $shortUrl ?? null,
            'error' => $error ?? null,
        ]);
    }

    public function get(
        string            $post_id,
        PostRepository    $postRepository,
        CommentRepository $commentRepository,
    ): Response
    {
        if (!filter_var($post_id, FILTER_VALIDATE_INT)) {
            return new Response('Пост не найден', Response::HTTP_BAD_REQUEST);
        }

        $post_id = (int)$post_id;

        $post = $postRepository->findOneWithCountStats($post_id);

        if (!$post) {
            return new Response('Пост не найден', Response::HTTP_BAD_REQUEST);
        }

        $comments = $commentRepository->findByPostId($post_id);

        return $this->render('post/page.html.twig', [
            "post" => $post,
            "comments" => $comments,
        ]);
    }

    public function postLike(
        string                 $post_id,
        EntityManagerInterface $entityManager): Response
    {

        return $this->redirectToRoute('getPost', ['id' => $post_id]);
    }
}
