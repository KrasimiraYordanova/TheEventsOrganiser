<?php

namespace App\Controller;

use App\Repository\EventTypeRepository;
use App\Repository\EventListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\EventType;
use App\Repository\ClientRepository;
use Symfony\Component\HttpFoundation\Request;

#[Route('/user/maindashboard')]
class UserMaindashboardController extends AbstractController
{
    // main dashboard - event type to create the event (event form), last three current events, past three events
    #[Route('/', name: 'app_maindashboard')]
    public function index(EventTypeRepository $eventTypeRepo, ClientRepository $clientRepository, Request $request, EventListRepository $eventListRepository): Response
    {

        $user = $this->getUser();
        $client = $clientRepository->findOneBy(['user' => $user]);
        $eventLists = $eventListRepository->findBy(['client' => $client]);

        // $eventLists = $eventListRepository->lastThreeEvents();
        return $this->render('user_maindashboard/index.html.twig', [
            'eventTypes' => $eventTypeRepo->findAll(),
            'eventLists' => $eventListRepository->lastThreeEvents($client->getId())
        ]);
    }
}