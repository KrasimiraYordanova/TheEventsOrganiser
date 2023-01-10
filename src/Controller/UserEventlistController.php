<?php

namespace App\Controller;

use App\Entity\EventList;
use App\Entity\EventType;
use App\Form\EventListType;
use App\Service\FileUploader;
use App\Repository\EventListRepository;
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

    #[Route('/{id}/edit', name: 'app_user_eventlist_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_user_eventlist_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EventList $eventList = null, EventListRepository $eventListRepository, SluggerInterface $slugger, FileUploader $fileUploader): Response
    {
        if(!$eventList) {
            $eventList = new EventList();
        }
       
        $form = $this->createForm(EventListType::class, $eventList);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $eventList->getImage();
            $imageFile = $form->get('image')->getData();

            if($imageFile) {
                if($image) {
                    $fileUploader->delete($image, 'eventImage');
                }
                $imageFileName = $fileUploader->upload($imageFile, 'eventImage');
                $eventList->setImage($imageFileName);
            } else {
                $eventList->setImage($eventList->getImage());
            }
            $eventList->setEventSlug($slugger->slug($eventList->getEventName()));
            $eventListRepository->save($eventList, true);
            
            return $this->redirectToRoute('app_user_eventlist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_eventlist/new.html.twig', [
            'event_list' => $eventList,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_eventlist_show', methods: ['GET'])]
    public function show(EventList $eventList): Response
    {
        return $this->render('user_eventlist/show.html.twig', [
            'event_list' => $eventList,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_eventlist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EventList $eventList, EventListRepository $eventListRepository): Response
    {
        $form = $this->createForm(EventListType::class, $eventList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventListRepository->save($eventList, true);

            return $this->redirectToRoute('app_user_eventlist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_eventlist/edit.html.twig', [
            'event_list' => $eventList,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_eventlist_delete', methods: ['POST'])]
    public function delete(Request $request, EventList $eventList, EventListRepository $eventListRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eventList->getId(), $request->request->get('_token'))) {
            $eventListRepository->remove($eventList, true);
        }

        return $this->redirectToRoute('app_user_eventlist_index', [], Response::HTTP_SEE_OTHER);
    }
}
