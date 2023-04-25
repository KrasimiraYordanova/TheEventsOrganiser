<?php

namespace App\Controller;

use App\Entity\Guest;
use App\Entity\Picture;
use App\Form\GuestType;
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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


// bride + groom name - values (eventProperties for the selected event)
// event date for the selected event (eventList )

class UserWebsiteController extends AbstractController
{

    #[Route('/website/{eventSlug}/{token}', defaults: ["token" => null], name: 'app_user_website_index', methods: ['GET', 'POST'])]
    public function website(EventList $eventList, string $token = null, Request $request, GuestRepository $guestRepository, EventListRepository $eventListRepo): Response
    {
        $user = $this->getUser();
        // récupérer le guest via son token
        if (!$token)
            throw new AccessDeniedException('Accès non autorisé !');

        // getting the guests from the event
        $guest = $guestRepository->findOneBy(['eventList' => $eventList->getId(), 'token' => $token]);

        if (empty($guest))
            throw new AccessDeniedException('Accès non autorisé !');

        $form = $this->createForm(GuestType::class, $guest);
        $form->remove('name');
        $form->remove('address');
        $form->remove('email');
        $form->remove('phone');
        $form->remove('tabletab');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $guestRepository->save($guest, true);

            return $this->redirectToRoute('front_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->renderForm('user_website/index.html.twig', [
            'eventList' => $eventList,
            'guest' => $guest,
            'form' => $form,
            'user' => $user,
        ]);
    }
}
