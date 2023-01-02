<?php

namespace App\Controller;

use App\Entity\EventType;
use App\Form\EventTypeType;
use App\Repository\EventTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/eventtype')]
class AdminEventtypeController extends AbstractController
{
    #[Route('/', name: 'app_admin_eventtype_index', methods: ['GET'])]
    public function index(EventTypeRepository $eventTypeRepository): Response
    {
        return $this->render('admin_eventtype/index.html.twig', [
            'event_types' => $eventTypeRepository->findAll(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_eventtype_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_admin_eventtype_new', methods: ['GET', 'POST'])]
    public function new(EventType $eventType = null, Request $request, EventTypeRepository $eventTypeRepository, SluggerInterface $slugger): Response
    {

        // $isNew = false;
        if(!$eventType) {
            $eventType = new EventType();
            // $isNew = true;
        }

        $form = $this->createForm(EventTypeType::class, $eventType);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventType->setSlug($slugger->slug($eventType->getName())->lower());
            $eventTypeRepository->save($eventType, true);

            // if($isNew) {
            //     $message = " was succesfully added";
            // } else {
            //     $message = " was succesfully modified";
            // }

            return $this->redirectToRoute('app_admin_eventtype_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_eventtype/new.html.twig', [
            'event_type' => $eventType,
            'edit' => $eventType->getId(),
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_eventtype_show', methods: ['GET'])]
    public function show(EventType $eventType): Response
    {
        return $this->render('admin_eventtype/show.html.twig', [
            'event_type' => $eventType,
        ]);
    }

    // #[Route('/{id}/edit', name: 'app_admin_eventtype_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, EventType $eventType, EventTypeRepository $eventTypeRepository): Response
    // {
    //     $form = $this->createForm(EventTypeType::class, $eventType);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $eventTypeRepository->save($eventType, true);

    //         return $this->redirectToRoute('app_admin_eventtype_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('admin_eventtype/edit.html.twig', [
    //         'event_type' => $eventType,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}', name: 'app_admin_eventtype_delete', methods: ['POST'])]
    public function delete(Request $request, EventType $eventType, EventTypeRepository $eventTypeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eventType->getId(), $request->request->get('_token'))) {
            $eventTypeRepository->remove($eventType, true);
        }

        return $this->redirectToRoute('app_admin_eventtype_index', [], Response::HTTP_SEE_OTHER);
    }
}
