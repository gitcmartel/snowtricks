<?php

namespace App\Controller;

use App\Form\ResetPasswordRequestFormType;
use App\Service\SendMailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


class SecurityController extends AbstractController
{
    #[Route('/forgotten-password', name: 'app_forgotten_password')]
    public function forgottenPassword(Request $resquest, EntityManagerInterface $entityManager, 
    UserRepository $usersRepository, TokenGeneratorInterface $tokenGenerator, SendMailService $mail): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($resquest);

        if ($form->isSubmitted() && $form->isValid()) {
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
                'password_reset', 
                $context
            );

            $this->addFlash('success', "Email envoyé avec succès");
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/forgotten-password.html.twig', [
            'controller_name' => 'SecurityController',
            'formResetPasswordRequest' => $form->createView()
        ]);
    }
}
