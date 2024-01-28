<?php

namespace App\Controller;

use App\Form\TricksFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TricksRepository;
use App\Entity\Tricks;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;

class TricksController extends AbstractController
{
    #[Route('/tricks/{tricksId}', name: 'app_tricks')]
    public function index($tricksId, TricksRepository $trickRepository, MediaRepository $mediaRepository): Response
    {
        //Fetching the tricks and media data
        $tricks = $trickRepository->findOneById($tricksId);
        $medias = $mediaRepository->findByTricks($tricksId);

        if ($tricks == null) {
            return $this->redirectToRoute('app_not_found');
        }

        return $this->render('tricks/index.html.twig', [
            'controller_name' => 'TricksController',
            'tricks' => $tricks, 
            'medias' => $medias
        ]);
    }

    #[Route('/tricks-edit/{tricksId}', name: 'app_tricks_edit')]
    public function edit($tricksId, TricksRepository $tricksRepository, MediaRepository $mediaRepository, Request $request, 
        EntityManagerInterface $entityManager) 
    {
        $tricks = new Tricks;

        $form = $this->createForm(TricksFormType::class, $tricks);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            return $this->redirectToRoute('app_home');
        }

        //Fetching data
        $tricks = $tricksRepository->findOneById($tricksId);

        return $this->render('tricks/tricks.html.twig', [
            'controller_name' => 'tricksController', 
            'tricks' => $tricks
        ]);
    }
}
