<?php

namespace App\Controller;

use App\Form\TricksFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TricksRepository;
use App\Repository\MediaRepository;
use App\Repository\TricksGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ImageService;

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
    public function edit($tricksId, TricksRepository $tricksRepository, MediaRepository $mediaRepository, TricksGroupRepository $tricksGroupRepository, 
        Request $request, EntityManagerInterface $entityManager) 
    {
        //Fetching data
        $tricks = $tricksRepository->findOneById($tricksId);
        $groups = $tricksGroupRepository->findAll();

        $form = $this->createForm(TricksFormType::class, $tricks);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('tricksImage')->getData();

            //Move the uploaded image 
            if ($uploadedFile == true) {
                $imageService = new ImageService($this->getParameter('kernel.project_dir') . '/assets/images/tricks');
                $newTricksImageFileName = $imageService->moveUploadedFile($uploadedFile);
                $tricks->setImage('images/tricks/' . $newTricksImageFileName);
            }

            //Saving data
            $entityManager->persist($tricks);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('tricks/tricks.html.twig', [
            'controller_name' => 'tricksController', 
            'tricks' => $tricks, 
            'groups' => $groups, 
            'formTricks' => $form->createView(),
        ]);
    }
}
