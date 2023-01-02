<?php

namespace App\Controller;

use App\Entity\Tabletab;
use App\Form\TabletabType;
use App\Repository\TabletabRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/user/tabletab')]
class UserTabletabController extends AbstractController
{
    #[Route('/', name: 'app_user_tabletab_index', methods: ['GET'])]
    public function index(TabletabRepository $tabletabRepository, ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Tabletab::class);
        $tableCount = $repository->tableCount();
        // dd($tableCount[0]);
        return $this->render('user_tabletab/index.html.twig', [
            'tabletabs' => $tabletabRepository->findAll(),
            'tableCount' => $tableCount[0]
        ]);
    }
    #[Route('/{id}/edit', name: 'app_user_tabletab_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_user_tabletab_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Tabletab $tabletab = null, TabletabRepository $tabletabRepository): Response
    {

        if (!$tabletab) {
            $tabletab = new Tabletab();
        }


        $form = $this->createForm(TabletabType::class, $tabletab);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tabletabRepository->save($tabletab, true);

            return $this->redirectToRoute('app_user_tabletab_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_tabletab/new.html.twig', [
            'tabletab' => $tabletab,
            'edit' => $tabletab->getId(),
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_user_tabletab_show', methods: ['GET'])]
    public function show(Tabletab $tabletab): Response
    {
        return $this->render('user_tabletab/show.html.twig', [
            'tabletab' => $tabletab,
        ]);
    }

    // #[Route('/{id}/edit', name: 'app_user_tabletab_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Tabletab $tabletab, TabletabRepository $tabletabRepository): Response
    // {
    //     $form = $this->createForm(TabletabType::class, $tabletab);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $tabletabRepository->save($tabletab, true);

    //         return $this->redirectToRoute('app_user_tabletab_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('user_tabletab/edit.html.twig', [
    //         'tabletab' => $tabletab,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}', name: 'app_user_tabletab_delete', methods: ['POST'])]
    public function delete(Request $request, Tabletab $tabletab, TabletabRepository $tabletabRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tabletab->getId(), $request->request->get('_token'))) {
            $tabletabRepository->remove($tabletab, true);
        }

        return $this->redirectToRoute('app_user_tabletab_index', [], Response::HTTP_SEE_OTHER);
    }
}
