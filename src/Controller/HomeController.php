<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TricksRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class HomeController extends AbstractController
{
    private const NUMBER_OF_TRICKS_PER_PAGE = 15;

    #[Route('/', name: 'app_home')]
    public function index(TricksRepository $tricksRepository): Response
    {
        $tricks = $tricksRepository->findTricksByPage(1, $this::NUMBER_OF_TRICKS_PER_PAGE);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'tricksList' => $tricks
        ]);
    }

    #[Route('/home/{page}', name: 'app_home_load_more')]
    public function loadMoreTricks(Request $request, TricksRepository $tricksRepository, SerializerInterface $serializer) 
    {
        if ($request->isXmlHttpRequest()) {
            $page = $request->query->getInt('page', 1);

            $tricks = $tricksRepository->findTricksByPage($page, 15);

            $data = $serializer->serialize($tricks, 'json');

            return new JsonResponse($data, 200, [], true);
        }

        throw $this->createNotFoundException('Erreur lors du chargement des tricks');
    }
}
