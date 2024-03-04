<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TricksRepository;

class HomeController extends AbstractController
{
    private const NUMBER_OF_TRICKS_PER_PAGE = 15;

    #[Route('/', name: 'app_home')]
    public function index(TricksRepository $tricksRepository): Response
    {
        $tricks = $tricksRepository->findTricksByPage(1, $this::NUMBER_OF_TRICKS_PER_PAGE);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'tricksList' => $tricks, 
            'pageNumber' => 1
        ]);
    }

    #[Route('/home/{page}', name: 'app_home_load_more')]
    public function loadMoreTricks($page, Request $request, TricksRepository $tricksRepository) 
    {
        if ($request->isXmlHttpRequest() == true) {
            $tricks = $tricksRepository->findTricksByPage($page, 15);
            return $this->render('_partials/_tricks.html.twig', [
                'tricksList' => $tricks,
                'pageNumber' => $page
            ]);
        }

        throw $this->createNotFoundException('Erreur lors du chargement des tricks');
    }
}
