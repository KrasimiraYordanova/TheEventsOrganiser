<?php

namespace App\Controller;

use App\Entity\Rdv;
use App\Form\RdvType;
use App\Repository\RdvRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user/rdv')]
class UserRdvController extends AbstractController
{
    #[Route('/', name: 'app_user_rdv_index', methods: ['GET'])]
    public function index(RdvRepository $rdvRepository): Response
    {
        return $this->render('user_rdv/index.html.twig', [
            'rdvs' => $rdvRepository->findAll(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_rdv_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_user_rdv_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Rdv $rdv = null, RdvRepository $rdvRepository, SluggerInterface $slugger): Response
    {
        if(!$rdv) {
        $rdv = new Rdv();
        }

        $form = $this->createForm(RdvType::class, $rdv);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shortDescSlug = substr($rdv->getDescription(), 0, 10);
            $rdv->setSlug($slugger->slug($shortDescSlug)->lower());
            $rdvRepository->save($rdv, true);

            return $this->redirectToRoute('app_user_rdv_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_rdv/new.html.twig', [
            'rdv' => $rdv,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_rdv_show', methods: ['GET'])]
    public function show(Rdv $rdv): Response
    {
        return $this->render('user_rdv/show.html.twig', [
            'rdv' => $rdv,
        ]);
    }

    // #[Route('/{id}/edit', name: 'app_user_rdv_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Rdv $rdv, RdvRepository $rdvRepository): Response
    // {
    //     $form = $this->createForm(RdvType::class, $rdv);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $rdvRepository->save($rdv, true);

    //         return $this->redirectToRoute('app_user_rdv_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('user_rdv/edit.html.twig', [
    //         'rdv' => $rdv,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}', name: 'app_user_rdv_delete', methods: ['POST'])]
    public function delete(Request $request, Rdv $rdv, RdvRepository $rdvRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rdv->getId(), $request->request->get('_token'))) {
            $rdvRepository->remove($rdv, true);
        }

        return $this->redirectToRoute('app_user_rdv_index', [], Response::HTTP_SEE_OTHER);
    }
}
