<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\CreatePostForm;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends AbstractController
{
    public function getList(): Response
    {
        return $this->render('post/list.html.twig', [

        ]);
    }

    public function getForm(): Response
    {
        $post = new Post();
        $form = $this->createForm(CreatePostForm::class, $post);

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

        $form = $this->createForm(CreatePostForm::class, $post);

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

    public function get(string $slug): Response
    {
        return $this->render('post/page.html.twig', [

        ]);
    }
}
