<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/eventdashboard')]
class UserEventdashboardController extends AbstractController
{
    // main dashboard - event type to create the event (event form), last three current events, past three events
    #[Route('/', name: 'app_user_eventdashboard')]
    public function index(): Response
    {
        return $this->render('user_eventdashboard/index.html.twig', );
    }
}