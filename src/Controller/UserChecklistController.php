<?php

namespace App\Controller;

use App\Entity\Checklist;
use App\Form\ChecklistType;
use App\Repository\ChecklistRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user/checklist')]
class UserChecklistController extends AbstractController
{
    #[Route('/', name: 'app_user_checklist_index', methods: ['GET'])]
    public function index(ChecklistRepository $checklistRepository): Response
    {
        return $this->render('user_checklist/index.html.twig', [
            'checklists' => $checklistRepository->findAll(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_checklist_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_user_checklist_new', methods: ['GET', 'POST'])]
    public function new(Request $request,Checklist $checklist = null, ChecklistRepository $checklistRepository, SluggerInterface $slugger): Response
    {
        
        if(!$checklist) {
        $checklist = new Checklist();
        }

        $form = $this->createForm(ChecklistType::class, $checklist);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        $form->remove('eventName');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shortDescSlug = substr($checklist->getDescription(), 0, 30);
            $checklist->setSlug($slugger->slug($shortDescSlug)->lower());
            $checklistRepository->save($checklist, true);

            return $this->redirectToRoute('app_user_checklist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_checklist/new.html.twig', [
            'checklist' => $checklist,
            'edit' => $checklist->getId(),
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_checklist_show', methods: ['GET'])]
    public function show(Checklist $checklist): Response
    {
        return $this->render('user_checklist/show.html.twig', [
            'checklist' => $checklist,
        ]);
    }

    // #[Route('/{id}/edit', name: 'app_user_checklist_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Checklist $checklist, ChecklistRepository $checklistRepository): Response
    // {
    //     $form = $this->createForm(ChecklistType::class, $checklist);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $checklistRepository->save($checklist, true);

    //         return $this->redirectToRoute('app_user_checklist_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('user_checklist/edit.html.twig', [
    //         'checklist' => $checklist,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}', name: 'app_user_checklist_delete', methods: ['POST'])]
    public function delete(Request $request, Checklist $checklist, ChecklistRepository $checklistRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$checklist->getId(), $request->request->get('_token'))) {
            $checklistRepository->remove($checklist, true);
        }

        return $this->redirectToRoute('app_user_checklist_index', [], Response::HTTP_SEE_OTHER);
    }
}



// FIND ALL CHECKLIST WHICH ARE UNCHECKED
    // #[Route('/unchecked', name: 'app_user_checklist_unchecked')]
    // public function findAllUnchecked(ManagerRegistry $doctrine) : Response {
    //     $repository = $doctrine->getRepository(Checklist::class);
    //     $uncheckedChecklists = $repository->findBy(['isChecked' => false]);

    //     return $this->render('user_checklist/unchecked.html.twig', [
    //         'unchecklists' => $uncheckedChecklists,
    //     ]);
    // }

    // FIND ALL CHECKLIST WHICH ARE UNCHECKED COUNT
    // #[Route('/unchecked', name: 'app_user_checklist_unchecked', methods: ['GET'])]
    // public function findAllUncheckedCount(ManagerRegistry $doctrine) : Response {
    //     $repository = $doctrine->getRepository(Checklist::class);
    //     $uncheckedChecklists = $repository->findBy(['isChecked' => false]);

    //     return $this->render('user_checklist/unchecked.html.twig', [
    //         'unchecklists' => $uncheckedChecklists,
    //     ]);
    // }

    // FIND ALL CHECKLIST WHICH ARE CHECKED
    // #[Route('/checked', name: 'app_user_checklist_unchecked', methods: ['GET'])]
    // public function findAllChecked(ManagerRegistry $doctrine) : Response {
    //     $repository = $doctrine->getRepository(Checklist::class);
    //     $checkedChecklists = $repository->findBy(['isChecked' => true]);

    //     return $this->render('user_checklist/checked.html.twig', [
    //         'checklists' => $checkedChecklists,
    //     ]);
    // }
    // FIND ALL CHECKLIST WHICH ARE CHECKED COUNT 
    // #[Route('/checkedlist', name: 'app_user_checklist_unchecked', methods: ['GET'])]
    // public function findAllCheckedCount(ManagerRegistry $doctrine) : Response {
    //     $repository = $doctrine->getRepository(Checklist::class);
    //     $checkedChecklistsCount = $repository->isCheckedCount();

    //     return $this->render('user_checklist/checked.html.twig', [
    //         'checklistsCount' => $checkedChecklistsCount,
    //     ]);
    // }