<?php

namespace App\Controller;

use App\Entity\Guest;
use App\Entity\Picture;
use App\Entity\EventList;
use App\Service\FileUploader;
use App\Repository\GuestRepository;

use App\Repository\EventListRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


// bride + groom name - values (eventProperties for the selected event)
// event date for the selected event (eventList )

class UserWebsiteController extends AbstractController
{

    #[Route('/website/{id}', name: 'app_user_website_index')]
    public function website(EventList $eventList, Request $request, GuestRepository $guestRepo, EventListRepository $eventListRepo): Response
    {

        dd($eventList);
        $guest = $guestRepo->findBy(['eventList' => $eventList->getId()]);

        $guests = $eventList->getGuests();
        return $this->render('user_website/index.html.twig', [
            'eventList' => $eventList,
        ]);
    }
}
