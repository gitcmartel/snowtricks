<?php

namespace App\Controller;

use App\Form\ResetPasswordRequestFormType;
use App\Form\ResetPasswordFormType;
use App\Service\SendMailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


class SecurityController extends AbstractController
{
    /**
     * Sends an email to the user with a reset password link
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param TokenGeneratorInterface $tokenGenerator
     * @param SendMailService $mail
     * @return Response
     */
    #[Route('/forgotten-password', name: 'app_forgotten_password')]
    public function forgottenPassword(Request $resquest, EntityManagerInterface $entityManager, 
    UserRepository $usersRepository, TokenGeneratorInterface $tokenGenerator, SendMailService $mail): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($resquest);

        if (($form->isSubmitted() && $form->isValid()) === false) {
            return $this->render('security/forgotten-password.html.twig', [
                'controller_name' => 'SecurityController',
                'formResetPasswordRequest' => $form->createView()
            ]);
        }

        $user = $usersRepository->findOneByUsername($form->get('username')->getData());

        if ($user === null) {
            $this->addFlash('danger', 'Un problème est survenu lors de la tentative de renouvellement du mot de passe');
            return $this->redirectToRoute('app_login');
        }

        $token = $tokenGenerator->generateToken();
        $user->setPasswordToken($token);
        $entityManager->persist($user);
        $entityManager->flush();

        $url = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

        $context = compact('url', 'user');

        $mail->send(
            'mailer@snowtricks.devcm.fr', 
            $user->getEmail(), 
            'Réinitialisation du mot de passe', 
            'emails/forgotten_password_email.html.twig', 
            $context
        );

        $this->addFlash('success', "Email envoyé avec succès");
        return $this->redirectToRoute('app_login');
    }

    /**
     * Allow the user to submit a new password
     * @param string $token
     * @param Request $resquest
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(string $token, Request $resquest, UserRepository $userRepository, 
    EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher) : Response
    {
        $user = $userRepository->findOneBy(['password_token' => $token]);

        if ($user == null) {
            $this->addFlash('danger', 'Ce lien est invalide');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ResetPasswordFormType::class);

        $form->handleRequest($resquest);
        
        if (($form->isSubmitted() && $form->isValid()) === false) {
            return $this->render('security/reset-password.html.twig', [
                'resetPasswordForm' => $form->createView()
            ]);
        }

        $user->setPasswordToken('');

        $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Mot de passe réinitialisé avec succès');
        return $this->redirectToRoute('app_login');
    }
}
