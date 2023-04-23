<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\EventList;
use App\Entity\EventProperty;
use App\Entity\EventType;
use App\Entity\Property;
use App\Form\EventListType;
use App\Repository\ChecklistRepository;
use App\Repository\ClientRepository;
use App\Service\FileUploader;
use App\Repository\EventListRepository;
use App\Repository\EventPropertyRepository;
use App\Repository\ExpenseRepository;
use App\Repository\GuestRepository;
use App\Repository\PictureRepository;
use App\Repository\PropertyRepository;
use App\Repository\TabletabRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user/eventlist')]
class UserEventlistController extends AbstractController
{
    #[Route('/', name: 'app_user_eventlist_index', methods: ['GET'])]
    public function index(EventListRepository $eventListRepository, ClientRepository $clientRepo): Response
    {
        $user = $this->getUser();
        $client = $clientRepo->findOneBy(['user' => $user]);
        $event_lists = $eventListRepository->findBy(['client' => $client]);

        return $this->render('user_eventlist/index.html.twig', [
            'user' => $user,
            'event_lists' => $event_lists,
        ]);
    }
    
    #[Route('/create/{slug}', name: 'app_user_eventlist_form', methods: ['GET', 'POST'])]
    public function createEvent(EventType $eventType, Request $request, EventPropertyRepository $eventPropertyRepo, ClientRepository $clientRepo, PropertyRepository $propertyRepository, EventListRepository $eventListRepo, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {

        $user = $this->getUser();
        $eventList = new EventList();
        $form = $this->createForm(EventListType::class, $eventList);
        $form->handleRequest($request);
        
        $user = $this->getUser();
        $client = $clientRepo->findOneBy(['user' => $user]);

        $properties = $propertyRepository->findBy(["eventType" => $eventType]);
        // dd($properties);
        
        
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();


            $params = $request->request->all();
            $eventDelete = array_shift($params);
            
            if($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile, 'eventImage');
                $eventList->setImage($imageFileName);
            }
            $eventList->setEventSlug($slugger->slug($eventList->getEventName()));
            $eventList->setEventType($eventType);
            $eventList->setClient($client);
            
        
            $eventListRepo->save($eventList, true);
            
             foreach($params as $key=>$value){
                // dd($params);
                $id = (int)explode('_', $key)[1];
                $eventProp = new EventProperty();
                $eventProp->setValue($value)->setProperty($propertyRepository->find($id))->setEventList($eventList);
                $eventPropertyRepo->save($eventProp,true);
            }

            return $this->redirectToRoute('app_user_eventdashboard', ['eventSlug' => $eventList->getEventSlug()], Response::HTTP_SEE_OTHER);
        }
        
        return $this->renderForm('user_eventlist/new.html.twig', [
            'user' => $user,
            'eventProperties' => $properties,
            'eventType' => $eventType->getName(),
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_eventlist_delete', methods: ['POST'])]
    public function delete(Request $request, EventList $eventList, EventListRepository $eventListRepository, ExpenseRepository $expenseRepository, ChecklistRepository $checklistRepository, GuestRepository $guestRepository, EventPropertyRepository $eventPropertyRepository, TabletabRepository $tabletabRepository, PictureRepository $pictureRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eventList->getId(), $request->request->get('_token'))) {
            // $expenses = $eventList->getExpenses();
            // dd($expenses);
            $expensesToDelete = $expenseRepository->findBy(['eventList' => $eventList->getId()]);
            $checklistsToDelete = $checklistRepository->findBy(['eventList' => $eventList->getId()]);
            $guestsToDelete = $guestRepository->findBy(['eventList' => $eventList->getId()]);
            $eventPropertiesToDelete = $eventPropertyRepository->findBy(['eventList' => $eventList->getId()]);
            $tabletabsToDelete = $tabletabRepository->findBy(['eventList' => $eventList->getId()]);
            $picturesToDelete = $pictureRepository->findBy(['eventList' => $eventList->getId()]);

            foreach($expensesToDelete as $expense) {
                $expenseRepository->remove($expense, true);
            }
            foreach($checklistsToDelete as $checklist) {
                $checklistRepository->remove($checklist, true);
            }
            foreach($guestsToDelete as $guest) {
                $guestRepository->remove($guest, true);
            }
            foreach($eventPropertiesToDelete as $eventProperty) {
                $eventPropertyRepository->remove($eventProperty, true);
            }
            foreach($tabletabsToDelete as $tabletab) {
                $tabletabRepository->remove($tabletab, true);
            }
            foreach($picturesToDelete as $picture) {
                $pictureRepository->remove($picture, true);
            }

            $eventListRepository->remove($eventList, true);
        }

        return $this->redirectToRoute('app_user_eventlist_index', [], Response::HTTP_SEE_OTHER);
    }
}
