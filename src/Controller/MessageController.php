<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MessageRepository;

class MessageController extends AbstractController
{
    private const NUMBER_OF_MESSAGES_PER_PAGE = 5;

    #[Route('/message/{tricksId}/{page}', name: 'app_message_load_more')]
    public function loadMoreMessages($tricksId, $page, Request $request, MessageRepository $messageRepository): Response
    {
        if ($request->isXmlHttpRequest() == true) {
            $messages = $messageRepository->findMessageByPage($tricksId, $page, $this::NUMBER_OF_MESSAGES_PER_PAGE);
            return $this->render('_partials/_messages.html.twig', [
                'messages' => $messages
            ]);
        }

        throw $this->createNotFoundException('Erreur lors du chargement des messages');
    }
}
