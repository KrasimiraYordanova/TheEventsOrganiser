<?php

namespace App\Controller;

use App\Entity\Guest;
use App\Form\GuestType;
use App\Repository\GuestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/user/guest')]
class UserGuestController extends AbstractController
{
    #[Route('/', name: 'app_user_guest_index', methods: ['GET'])]
    public function index(GuestRepository $guestRepository, ManagerRegistry $doctrine): Response
    {
        
        $repository = $doctrine->getRepository(Guest::class);
        $attendings = $repository->guestsCount("attending");
        $declines = $repository->guestsCount("declined");
        // $awaitings = $repository->guestsCount();
        // dd($attendings, $declines);

        $vegans = $repository->dietCount('vegan');
        $vegetarians = $repository->dietCount('vegetarian');
        $omnivores = $repository->dietCount('omnivore');
        // dd($vegans, $vegetarians, $omnivores);
        $allGuestNumber = $repository->allGuestCount();

        return $this->render('user_guest/index.html.twig', [
            'guests' => $guestRepository->findAll(),
            'guestNumber' => $allGuestNumber[0],
            'attendings' => $attendings[0],
            'declines' => $declines[0],
            'vegans' => $vegans[0],
            'vegetarians' => $vegetarians[0],
            'omnivores' => $omnivores[0]
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_guest_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_user_guest_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Guest $guest = null, GuestRepository $guestRepository): Response
    {
        if(!$guest) {
            $guest = new Guest();
        }
        
        $form = $this->createForm(GuestType::class, $guest);
        $form->remove('token');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $guestRepository->save($guest, true);

            return $this->redirectToRoute('app_user_guest_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_guest/new.html.twig', [
            'guest' => $guest,
            'edit' => $guest->getId(),
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_guest_show', methods: ['GET'])]
    public function show(Guest $guest): Response
    {
        return $this->render('user_guest/show.html.twig', [
            'guest' => $guest,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_guest_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Guest $guest, GuestRepository $guestRepository): Response
    {
        $form = $this->createForm(GuestType::class, $guest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $guestRepository->save($guest, true);

            return $this->redirectToRoute('app_user_guest_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_guest/edit.html.twig', [
            'guest' => $guest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_guest_delete', methods: ['POST'])]
    public function delete(Request $request, Guest $guest, GuestRepository $guestRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$guest->getId(), $request->request->get('_token'))) {
            $guestRepository->remove($guest, true);
        }

        return $this->redirectToRoute('app_user_guest_index', [], Response::HTTP_SEE_OTHER);
    }
}
