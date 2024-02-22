<?php

namespace App\Controller;

use App\Form\TricksFormType;
use App\Form\MessageFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TricksRepository;
use App\Repository\MediaRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\MediaService;
use App\Service\MimeService;
use App\Entity\Tricks;
use App\Entity\Message;

class TricksController extends AbstractController
{
    #[Route('/tricks/{tricksId}', name: 'app_tricks')]
    public function index($tricksId, TricksRepository $trickRepository, MediaRepository $mediaRepository, 
    MessageRepository $messageRepository, Request $request, Security $security, EntityManagerInterface $entityManager): Response
    {
        //Fetching the tricks, messages, and media data
        $tricks = $trickRepository->findOneById($tricksId);
        $medias = $mediaRepository->findByTricks($tricksId);
        $messages = $messageRepository->findMessageByPage($tricksId, 1, 5);
        $message = new Message();

        if ($tricks == null) {
            return $this->redirectToRoute('app_not_found');
        }

        $form = $this->createForm(MessageFormType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('content') !== "") {
                $message->setCreationDate(new \DateTime());
                $message->setUser($security->getUser());
                $message->setTricks($tricks);

                $entityManager->persist($message);
                $entityManager->flush();

                //Refreshing messages data
                $messages = $messageRepository->findMessageByPage($tricksId, 1, 5);

                //Creating a new form
                $message = new Message();
                $form = $this->createForm(MessageFormType::class, $message);
            }
        }

        return $this->render('tricks/index.html.twig', [
            'controller_name' => 'TricksController',
            'tricks' => $tricks, 
            'medias' => $medias, 
            'messages' => $messages, 
            'formMessage' => $form->createView()
        ]);
    }

    #[Route('/tricks-edit/{tricksId}', name: 'app_tricks_edit')]
    public function edit($tricksId, TricksRepository $tricksRepository, Request $request, EntityManagerInterface $entityManager, 
        MediaRepository $mediaRepository, MediaService $mediaService, MimeService $mimeService) 
    {

        //Fetching data

        $originalMedias = new ArrayCollection();

        if ($tricksId === '0') {
            $tricks = new Tricks();
            $tricks->setId(0);
            $tricks->setImage('images/hero_1.jpg');           
        } else {
            $tricks = $tricksRepository->findOneById($tricksId);

            foreach($tricks->getMedias() as $media) {
                $originalMedias->add($media);
            }
        }

        $form = $this->createForm(TricksFormType::class, $tricks);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //region Hero Image
            $isHeroDeleted = $form->get('isHeroImageDeleted')->getData();

            if($isHeroDeleted === true) {
                $mediaService->deleteMedia($tricks->getImage());
                $tricks->setImage('images/hero_1.jpg');
            }

            $uploadedFile = $form->get('image')->getData();

            //Move the uploaded image 
            if ($uploadedFile == true) {
                $newTricksImageFileName = $mediaService->moveUploadedFile($uploadedFile);
                $mediaService->deleteMedia($tricks->getImage());
                $tricks->setImage('medias/' . $newTricksImageFileName);
            }
            //endregion

            //region Adding dateTime
            if ($tricksId === 0) {
                $tricks->setCreationDate(new \DateTime());
            } else {
                $tricks->setModificationDate(new \DateTime());
            }
            //endregion

            //region Delete media
            foreach ($originalMedias as $media) {
                if (false === $tricks->getMedias()->contains($media)) {
                    //We delete the video or image
                    $mediaService->deleteMedia($media->getPath());

                    //Then we delete the media from the database
                    $tricks->removeMedia($media);
                    $entityManager->remove($media); 
                }
            }

            //endregion

            //region Add or edit media
            foreach ($form->get('medias') as $formMedia) {
                $uploadedFile = $formMedia->get('path')->getData();
                $media = $formMedia->getData(); 
                
                if ($uploadedFile !== null) {
                    $type = $mimeService->getType($uploadedFile);
                    $newMediaFileName = $mediaService->moveUploadedFile($uploadedFile);
                    if ($media->getId() !== null) {
                        $actualMedia = $mediaRepository->findOneById((int)$media->getId());
                        // If there is an id we update the existing media
                        $mediaService->deleteMedia($actualMedia->getPath());
                        $media->setPath('medias/' . $newMediaFileName);
                    } else {
                        // If the id is null we create a new media
                        $media->setPath('medias/' . $newMediaFileName);
                        $media->setType($type);                    
                    }
                }

                $videoLink = $formMedia->get('link')->getData();
                if ($videoLink !== null) {
                    $media->setPath($videoLink);
                    $media->setType("video");  
                }

                //If the media is empty we remove it
                if ($media->getPath() === null) {
                    $tricks->removeMedia($media);
                }
            }
            //endregion

            //Saving data
            $entityManager->persist($tricks);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('tricks/tricks.html.twig', [
            'controller_name' => 'tricksController', 
            'formTricks' => $form->createView(),
        ]);
    }

    #[Route('/tricks/{id}/delete', name: 'app_tricks_delete')]
    public function delete(Tricks $tricks, TricksRepository $tricksRepository, Request $request, EntityManagerInterface $entityManager, 
        MediaService $mediaService) {

        //Deletes all the tricks related medias
        foreach ($tricks->getMedias() as $media) {
            $mediaService->deleteMedia($media->getPath());
        }

        //Deletes the tricks main image
        $mediaService->deleteMedia($tricks->getImage());

        //Deletes the tricks
        $entityManager->remove($tricks);
        $entityManager->flush();

        return $this->json(['message' => 'Le tricks a été supprimé avec succès']);
    }
}
