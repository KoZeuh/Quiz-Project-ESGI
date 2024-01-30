<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ChangeEmailFormType;

use App\Service\PasswordValidatorService;

class UserController extends AbstractController
{
    #[Route('/profil', name: "profil_index", methods: ['GET', 'POST'])]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, PasswordValidatorService $passwordValidatorService): Response
    {
        $user = $this->getUser();

        $changePasswordForm = $this->createForm(ChangePasswordFormType::class);
        $changePasswordForm->handleRequest($request);

        $changeEmailForm = $this->createForm(ChangeEmailFormType::class);
        $changeEmailForm->handleRequest($request);

        if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {
            $oldPassword = $changePasswordForm->get('oldPassword')->getData();
            $newPassword = $changePasswordForm->get('newPassword')->getData();
            $confirmPassword = $changePasswordForm->get('confirmPassword')->getData();

            if ($passwordValidatorService->validate($newPassword) && $userPasswordHasher->isPasswordValid($user, $oldPassword)) {
                if ($newPassword === $confirmPassword) {
                    $user->setPassword($userPasswordHasher->hashPassword($user,$newPassword));
                    $entityManager->persist($user);
                    $entityManager->flush();

                    return $this->redirectToRoute('app_logout');
                }
            }
        } elseif ($changeEmailForm->isSubmitted() && $changeEmailForm->isValid()) {
            $oldEmail = $changeEmailForm->get('oldEmail')->getData();
            $newEmail = $changeEmailForm->get('newEmail')->getData();
            $confirmEmail = $changeEmailForm->get('confirmEmail')->getData();

            if ($user->getEmail() === $oldEmail) {
                if ($newEmail === $confirmEmail) {
                    $user->setEmail($newEmail);
                    $entityManager->persist($user);
                    $entityManager->flush();

                    return $this->redirectToRoute('app_logout');
                }
            }
        }

        return $this->render('user/profil/index.html.twig', [
            'changePasswordForm' => $changePasswordForm->createView(),
            'changeEmailForm' => $changeEmailForm->createView(),
        ]);
    }
}
