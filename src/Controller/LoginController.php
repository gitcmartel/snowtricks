<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils, Security $security): Response
    {
        // logout the user in on the current firewall
        if ($security->getUser()) {
            $security->logout();
        }

        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername, 
            'errorMessage' => $error == null ? '' : $error->getMessage()
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
