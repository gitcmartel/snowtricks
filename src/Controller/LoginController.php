<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        //Display an erorr message if there is one, and a success if the authentication is a success
        if ($error) {
            $this->addFlash('danger', 'Échec de l\'authentification. Veuillez réessayer.');
        }

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername
        ]);
    }

    /**
     * Disconnect a logged user
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        throw new \LogicException();
    }
}
