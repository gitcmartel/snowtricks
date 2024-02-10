<?php

namespace App\Controller;

use App\Form\TricksFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Media;
use App\Repository\TricksRepository;
use App\Repository\MediaRepository;
use App\Repository\TricksGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ImageService;
use App\Service\MimeService;

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
    public function edit($tricksId, TricksRepository $tricksRepository, TricksGroupRepository $tricksGroupRepository, 
        Request $request, EntityManagerInterface $entityManager, MediaRepository $mediaRepository, ImageService $imageService, MimeService $mimeService) 
    {

        //Fetching data
        $tricks = $tricksRepository->findOneById($tricksId);
        $groups = $tricksGroupRepository->findAll();
        $originalMedias = new ArrayCollection();

        foreach($tricks->getMedias() as $media) {
            $originalMedias->add($media);
        }

        $form = $this->createForm(TricksFormType::class, $tricks);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //region Hero Image
            $isHeroDeleted = $form->get('isHeroImageDeleted')->getData();

            if($isHeroDeleted === true) {
                $imageService->deleteImage($tricks->getImage());
                $tricks->setImage('images/hero_1.jpg');
            }

            $uploadedFile = $form->get('image')->getData();

            //Move the uploaded image 
            if ($uploadedFile == true) {
                $newTricksImageFileName = $imageService->moveUploadedFile($uploadedFile);
                $imageService->deleteImage($tricks->getImage());
                $tricks->setImage('images/tricks/' . $newTricksImageFileName);
            }
            //endregion

            foreach ($originalMedias as $media) {
                if (false === $tricks->getMedias()->contains($media)) {
                    $tricks->removeMedia($media);
                    //$entityManager->remove($media);  A voir si besoin de supprimer l'enregistrement en base de données
                }
            }

            foreach ($form->get('medias') as $media) {
                $mediaId = $media->get('id')->getData();
                $uploadedFile = $media->get('path')->getData();
                if ($uploadedFile !== null) {
                    $type = $mimeService->getType($uploadedFile);
                    $newMediaImageFileName = $imageService->moveUploadedFile($uploadedFile);
                    if ($mediaId !== null) {
                        $actualMedia = $mediaRepository->findOneById((int)$mediaId);
                        // If there is an id we update the existing media
                        $imageService->deleteImage($actualMedia->getPath());
                        $actualMedia->setPath('images/tricks/' . $newMediaImageFileName);
                    } else {
                        // If the id is null we create a new media
                        $newMedia = new Media();
                        $newMedia->setPath($type . '/tricks/' . $newMediaImageFileName);
                        $newMedia->setType($type);
                        $newMedia->setTricks($tricks);
                        $tricks->addMedia($newMedia);
                    }
                }
            }
            dd($tricks);
            exit;
            //Saving data
            $entityManager->persist($tricks);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('tricks/tricks.html.twig', [
            'controller_name' => 'tricksController', 
            'groups' => $groups,
            'formTricks' => $form->createView(),
        ]);
    }
}
