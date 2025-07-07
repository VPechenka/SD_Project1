<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationForm;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class UserController extends AbstractController
{
    public function register(
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface      $entityManager
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();

            $user->setPassword($userPasswordHasher->hashPassword($user, $password));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('getUserLogin');
        }

        return $this->render('user/register-form.html.twig', [
            'form' => $form,
        ]);
    }

    public function login(
        AuthenticationUtils $authenticationUtils
    ): Response
    {
        $user = $this->getUser();

        if ($user and $user->isBlocked()) {
            $this->addFlash('error', 'Ваш аккаунт заблокирован.');
            return $this->redirectToRoute('getUserLogin');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login-form.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    public function block(
        string                 $user_id,
        EntityManagerInterface $entityManager
    ): Response
    {
        if (filter_var($user_id, FILTER_VALIDATE_INT) === false)
            return new Response(
                'Неправильные входные данные',
                Response::HTTP_BAD_REQUEST
            );

        $user = $entityManager->getRepository(User::class)->find((int)$user_id);

        $user->setIsBlocked(true);

        $entityManager->flush();

        return $this->redirectToRoute("getPostList");
    }
}
