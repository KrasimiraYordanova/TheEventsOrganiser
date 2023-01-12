<?php

namespace App\Controller;


use App\Entity\EventList;
use App\Repository\EventListRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/user/eventdashboard')]
class UserEventdashboardController extends AbstractController
{
    // main dashboard - event type to create the event (event form), last three current events, past three events
    #[Route('/{id}', name: 'app_user_eventdashboard')]
    public function index(EventList $eventList, EventListRepository $eventListRepo, Request $request): Response
    {
        
        dd($eventList);

        return $this->render('user_eventdashboard/index.html.twig', [
             'id' => $eventList,
        ]);
    }
    
    // main dashboard - event type to create the event (event form), last three current events, past three events
    #[Route('/website', name: 'app_user_eventdashboard_website')]
    public function website(): Response
    {
        return $this->render('user_eventdashboard/website.html.twig', );
    }
}