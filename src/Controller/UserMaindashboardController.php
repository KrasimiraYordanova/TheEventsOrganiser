<?php

namespace App\Controller;

use App\Repository\EventTypeRepository;
use App\Repository\EventListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\EventType;

#[Route('/user/maindashboard')]
class UserMaindashboardController extends AbstractController
{
    // main dashboard - event type to create the event (event form), last three current events, past three events
    #[Route('/', name: 'app_maindashboard')]
    public function index(EventTypeRepository $eventTypeRepo, EventListRepository $eventList): Response
    {
        return $this->render('user_maindashboard/index.html.twig', [
            'eventTypes' => $eventTypeRepo->findAll(),
            // 'pastThreeEvents' => $eventListRepo->findBy(['createdAt' => 'createdAt'], ['createdAt' => 'DESC'], 3)
        ]);
    }
}