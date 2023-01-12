<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\EventList;
use App\Entity\EventProperty;
use App\Entity\EventType;
use App\Entity\Property;
use App\Form\EventListType;
use App\Repository\ClientRepository;
use App\Service\FileUploader;
use App\Repository\EventListRepository;
use App\Repository\EventPropertyRepository;
use App\Repository\PropertyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user/eventlist')]
class UserEventlistController extends AbstractController
{
    #[Route('/', name: 'app_user_eventlist_index', methods: ['GET'])]
    public function index(EventListRepository $eventListRepository): Response
    {
        return $this->render('user_eventlist/index.html.twig', [
            'event_lists' => $eventListRepository->findAll(),
        ]);
    }

    // #[Route('/{id}/edit', name: 'app_user_eventlist_edit', methods: ['GET', 'POST'])]
    // #[Route('/new', name: 'app_user_eventlist_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, EventList $eventList = null, EventListRepository $eventListRepository, SluggerInterface $slugger, FileUploader $fileUploader): Response
    // {
    //     if(!$eventList) {
    //         $eventList = new EventList();
    //     }
       
    //     $form = $this->createForm(EventListType::class, $eventList);
    //     $form->remove('createdAt');
    //     $form->remove('updatedAt');
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $image = $eventList->getImage();
    //         $imageFile = $form->get('image')->getData();

    //         if($imageFile) {
    //             if($image) {
    //                 $fileUploader->delete($image, 'eventImage');
    //             }
    //             $imageFileName = $fileUploader->upload($imageFile, 'eventImage');
    //             $eventList->setImage($imageFileName);
    //         } else {
    //             $eventList->setImage($eventList->getImage());
    //         }
    //         $eventList->setEventSlug($slugger->slug($eventList->getEventName()));
    //         $eventListRepository->save($eventList, true);
            
    //         return $this->redirectToRoute('app_user_eventlist_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('user_eventlist/new.html.twig', [
    //         'event_list' => $eventList,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/create/{slug}', name: 'app_user_eventlist_form', methods: ['GET', 'POST'])]
    public function createEvent(EventType $eventType, Request $request, EventPropertyRepository $eventPropertyRepo, ClientRepository $clientRepo, PropertyRepository $propertyRepository, EventListRepository $eventListRepo, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {

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
                $id = (int)explode('_', $key)[1];
                $eventProp = new EventProperty();
                $eventProp->setValue($value)->setProperty($propertyRepository->find($id))->setEventList($eventList);
                $eventPropertyRepo->save($eventProp,true);
            }

            return $this->redirectToRoute('app_user_eventdashboard', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
        }
        
        return $this->renderForm('user_eventlist/new.html.twig', [
            'eventProperties' => $properties,
            'eventType' => $eventType->getName(),
            'form' => $form,
        ]);
    }



    // #[Route('/{id}/edit', name: 'app_user_eventlist_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, EventList $eventList, EventListRepository $eventListRepository): Response
    // {
    //     $form = $this->createForm(EventListType::class, $eventList);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $eventListRepository->save($eventList, true);

    //         return $this->redirectToRoute('app_user_eventlist_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('user_eventlist/edit.html.twig', [
    //         'event_list' => $eventList,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}', name: 'app_user_eventlist_delete', methods: ['POST'])]
    public function delete(Request $request, EventList $eventList, EventListRepository $eventListRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eventList->getId(), $request->request->get('_token'))) {
            $eventListRepository->remove($eventList, true);
        }

        return $this->redirectToRoute('app_user_eventlist_index', [], Response::HTTP_SEE_OTHER);
    }
}
