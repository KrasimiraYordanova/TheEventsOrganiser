<?php

namespace App\Controller;


use App\Repository\EventTypeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class FrontController extends AbstractController
{
    // index page
    #[Route('/', name: 'front_index', methods: ['GET'])]
    public function index(EventTypeRepository $eventTypeRepo): Response
    {
        $user = $this->getUser();
        return $this->render('front/index.html.twig', [
            'user' => $user,
            'eventTypeList' => $eventTypeRepo->findAll(),
        ]);
    }

    // contact page
    #[Route('/contact', name: 'front_contact', methods: ['GET'])]
    public function contact(): Response
    {
        return $this->render('front/contact.html.twig');
    }
}
