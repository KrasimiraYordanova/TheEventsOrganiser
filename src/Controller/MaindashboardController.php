<?php

namespace App\Controller;

use App\Repository\EventTypeRepository;
use App\Repository\EventListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MaindashboardController extends AbstractController
{
    #[Route('/maindashboard', name: 'app_maindashboard')]
    public function index(EventTypeRepository $eventTypeRepo, EventListRepository $eventListRepo): Response
    {
        return $this->render('maindashboard/index.html.twig', [
            'eventTypeNames' => $eventTypeRepo->findAll(),
            'pastThreeEvents' => $eventListRepo->findBy(['createdAt' => 'createdAt'], ['createdAt' => 'DESC'], 3)
        ]);
    }
}
