<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\EventTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class FrontController extends AbstractController
{
    // index page
    #[Route('/', name: 'front_index', methods: ['GET'])]
    public function index(EventTypeRepository $eventTypeRepo): Response
    {
        return $this->render('front/index.html.twig', [
            'eventTypeList' => $eventTypeRepo->findAll(),
        ]);
    }

    // contact page
    #[Route('/contact', name: 'front_contact', methods: ['GET'])]
    public function contact(): Response
    {
        return $this->render('front/contact.html.twig');
    }

    #[Route('/{id}/edit', name: 'app_front_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $this->getUser();
        dd($user);

        $form = $this->createForm(UserType::class, $user);
        // if ($user->getId() !== null)
        //     $form->remove('plainPassword');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_eventdashboard', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('front/edituser.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
