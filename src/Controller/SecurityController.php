<?php

namespace App\Controller;

use App\Form\ResetPasswordRequestFormType;
use App\Form\ResetPasswordFormType;
use App\Service\SendMailService;
use App\Service\ToastService;
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
    UserRepository $usersRepository, TokenGeneratorInterface $tokenGenerator, SendMailService $mail, 
    ToastService $toastService): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($resquest);

        if (($form->isSubmitted() && $form->isValid()) === false) {
            return $this->render('security/forgotten-password.html.twig', [
                'controller_name' => 'SecurityController',
                'formResetPasswordRequest' => $form->createView()
            ]);
        }

        //Fetch the user from the database
        $user = $usersRepository->findOneByUsername($form->get('username')->getData());

        //If the user is not found we diplay a warning message
        if ($user === null) {
            $toastService->setMessage('Invalid Username !', 'error');
            return $this->redirectToRoute('app_forgotten_password');
        }

        //Creation of the token and storing it into the database
        $token = $tokenGenerator->generateToken();
        $user->setPasswordToken($token);
        $entityManager->persist($user);
        $entityManager->flush();

        //Creation of the URL that wil be sent to the user by email
        $url = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

        //Creates an array that contains url and user objects
        $context = compact('url', 'user');

        //Send the email to the user address
        $mail->send(
            'mailer@snowtricks.devcm.fr', 
            $user->getEmail(), 
            'Réinitialisation du mot de passe', 
            'emails/forgotten_password_email.html.twig', 
            $context
        );

        $toastService->setMessage('An email has been sent to reset your password !', 'success');
        return $this->redirectToRoute('app_home');
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
    EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ToastService $toastService) : Response
    {
        //Fetch the user from it's token
        $user = $userRepository->findOneBy(['password_token' => $token]);

        //If the user is not found we display a warning
        if ($user == null) {
            $toastService->setMessage('Invalid link !', 'error');
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(ResetPasswordFormType::class);

        $form->handleRequest($resquest);
        
        if (($form->isSubmitted() && $form->isValid()) === false) {
            return $this->render('security/reset-password.html.twig', [
                'resetPasswordForm' => $form->createView()
            ]);
        }

        //Checking if the username submitted corresponds to the active token
        //If the user is not found we display a warning
        if ($user->getUsername() !== $form->get('username')->getData()) {
            $toastService->setMessage('Token does not match username !', 'error');
            return $this->redirectToRoute('app_home');
        }

        //Reseting the token field
        $user->setPasswordToken('');

        //Setting the new user's password
        $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));

        //Saving modifications into the database
        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $e) {
            $toastService->setMessage('An error occured during the password renewal attempt !', 'error');
            return $this->redirectToRoute('app_home');
        }

        $toastService->setMessage('Password renewal success !', 'success');
        return $this->redirectToRoute('app_login');
    }
}
