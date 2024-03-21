<?php

namespace App\Controller;

use App\Form\TricksFormType;
use App\Form\MessageFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\TricksRepository;
use App\Repository\MediaRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\MediaService;
use App\Service\MimeService;
use App\Service\ToastService;
use App\Entity\Tricks;
use App\Entity\Message;

class TricksController extends AbstractController
{
    private $security;
    private $entityManager;
    private $tricksRepository;
    private $mediaRepository;
    private $messageRepository;
    private $toastService;
    private $mediaService;
    private $mimeService;
    private $slugger;

    function __construct(Security $security, EntityManagerInterface $entityManager, TricksRepository $tricksRepository, 
    MediaRepository $mediaRepository, MessageRepository $messageRepository, ToastService $toastService, MediaService $mediaService, 
    MimeService $mimeService, SluggerInterface $slugger) {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->tricksRepository = $tricksRepository;
        $this->mediaRepository = $mediaRepository;
        $this->messageRepository = $messageRepository;
        $this->toastService = $toastService;
        $this->mediaService = $mediaService;
        $this->mimeService = $mimeService;
        $this->slugger = $slugger;
    }

    #[Route('/tricks/{slug}', name: 'app_tricks')]
    public function index(Tricks $tricks, Request $request): Response
    {
        //Fetching the tricks, messages, and media data
        $beginMessageAnchor = '';

        $medias = $this->mediaRepository->findByTricks($tricks->getId());
        $messages = $this->messageRepository->findMessageByPage($tricks->getId(), 1, 5);
        $message = new Message();

        if ($tricks == null) {
            return $this->redirectToRoute('app_not_found');
        }

        $form = $this->createForm(MessageFormType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('content') !== "") {
                $message->setCreationDate(new \DateTime());
                $message->setUser($this->security->getUser());
                $message->setTricks($tricks);

                $this->entityManager->persist($message);
                $this->entityManager->flush();

                //Refreshing messages data
                $messages = $this->messageRepository->findMessageByPage($tricks->getId(), 1, 5);

                //Creating a new form
                $message = new Message();
                $form = $this->createForm(MessageFormType::class, $message);
                $beginMessageAnchor = 'beginMessagesAnchor';
                $this->toastService->setMessage('Message submitted !', 'success');
            }
        }

        return $this->render('tricks/index.html.twig', [
            'controller_name' => 'TricksController',
            'tricks' => $tricks, 
            'medias' => $medias, 
            'messages' => $messages, 
            'scrollTo' => $beginMessageAnchor, 
            'formMessage' => $form->createView()
        ]);
    }

    #[Route('/tricks-create', name: 'app_tricks_create')]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request) 
    {
        $tricks = new Tricks;
        $tricks->setImage('images/hero_1.jpg');       

        $form = $this->createForm(TricksFormType::class, $tricks);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $tricks->setSlug($this->slugger->slug($tricks->getName()));  

            $this->handleNewHeroImage($form, $tricks);

            $this->handleMedias($form, $tricks);

            //Saving data
            $this->save($tricks);
            return $this->redirectToRoute('app_home');
        }

        return $this->render('tricks/tricks_create.html.twig', [
            'controller_name' => 'tricksController', 
            'formTricks' => $form->createView()
        ]);
    }

    #[Route('/tricks-edit/{slug}', name: 'app_tricks_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Tricks $tricks, Request $request) 
    {
        //Fetching data

        $originalMedias = new ArrayCollection();

        foreach($tricks->getMedias() as $media) {
            $originalMedias->add($media);
        }

        $form = $this->createForm(TricksFormType::class, $tricks);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tricks->setSlug($this->slugger->slug($tricks->getName())); 
            $tricks->setModificationDate(new \DateTime()); 

            $this->handleHeroImageDeletion($form, $tricks);
            $this->handleNewHeroImage($form, $tricks);
            $this->handleMediaDeletions($originalMedias, $tricks);
            $this->handleMedias($form, $tricks);


            //Saving data
            $this->save($tricks);
            return $this->redirectToRoute('app_home');
        }

        return $this->render('tricks/tricks_edit.html.twig', [
            'controller_name' => 'tricksController', 
            'formTricks' => $form->createView()
        ]);
    }

    #[Route('/tricks/{slug}/delete', name: 'app_tricks_delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(Tricks $tricks, Request $request) {

        //Deletes all the tricks related medias
        foreach ($tricks->getMedias() as $media) {
            $this->mediaService->deleteMedia($media->getPath());
        }

        //Deletes the tricks main image
        $this->mediaService->deleteMedia($tricks->getImage());

        //Deletes the tricks
        $this->entityManager->remove($tricks);
        $this->entityManager->flush();

        return $this->json(['message' => 'Le tricks a été supprimé avec succès']);
    }

    private function handleHeroImageDeletion($form, Tricks $tricks): void {
        //region Hero Image
        $isHeroDeleted = $form->get('isHeroImageDeleted')->getData();

        if($isHeroDeleted === true) {
            $this->mediaService->deleteMedia($tricks->getImage());
            $tricks->setImage('images/hero_1.jpg');
        }
    }

    private function handleNewHeroImage($form, Tricks $tricks): void {
        $uploadedFile = $form->get('image')->getData();

        //Move the uploaded image 
        if ($uploadedFile == true) {
            $newTricksImageFileName = $this->mediaService->moveUploadedFile($uploadedFile);
            $this->mediaService->deleteMedia($tricks->getImage());
            $tricks->setImage('medias/' . $newTricksImageFileName);
        }
    }

    private function handleMediaDeletions($originalMedias, Tricks $tricks): void {
        foreach ($originalMedias as $media) {
            if (false === $tricks->getMedias()->contains($media)) {
                //We delete the video or image
                $this->mediaService->deleteMedia($media->getPath());

                //Then we delete the media from the database
                $tricks->removeMedia($media);
                $this->entityManager->remove($media); 
            }
        }
    }

    private function handleMedias($form, $tricks): void {
        foreach ($form->get('medias') as $formMedia) {
            $uploadedFile = $formMedia->get('path')->getData();
            $media = $formMedia->getData(); 
            
            if ($uploadedFile !== null) {
                $type = $this->mimeService->getType($uploadedFile);
                $newMediaFileName = $this->mediaService->moveUploadedFile($uploadedFile);
                if ($media->getId() !== null) {
                    $actualMedia = $this->mediaRepository->findOneById((int)$media->getId());
                    // If there is an id we update the existing media
                    $this->mediaService->deleteMedia($actualMedia->getPath());
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
    }

    private function save($tricks) {
        $this->entityManager->persist($tricks);
        $this->entityManager->flush();

        $this->toastService->setMessage('Success !', 'success');
    }
}
