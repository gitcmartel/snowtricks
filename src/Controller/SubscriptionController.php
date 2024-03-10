<?php

namespace App\Controller;

use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Service\PhotoService;
use App\Service\ToastService;

class SubscriptionController extends AbstractController
{
    #[Route('/subscription', name: 'app_subscription')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, 
    PhotoService $photoService, ToastService $toastService): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //region password
            $hashedPassword = $passwordHasher->hashPassword(
                $user, 
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);
            //endregion

            //region photo
            $uploadedFile = $form->get('photo')->getData();

            //Move the uploaded image 
            if ($uploadedFile == true) {
                $newPhotoImageFileName = $photoService->moveUploadedFile($uploadedFile);
                $user->setPhoto('photos/' . $newPhotoImageFileName);
            }
            //endregion

            $entityManager->persist($user);
            $entityManager->flush();

            $toastService->setMessage('Subscription success !');
            
            return $this->redirectToRoute('app_home');
        }

        return $this->render('subscription/index.html.twig', [
            'controller_name' => 'SubscriptionController',
            'formUser' => $form->createView()
        ]);
    }
}
