<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Form\PictureType;
use App\Repository\PictureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/user/picture')]
class UserPictureController extends AbstractController
{
    #[Route('/', name: 'app_user_picture_index', methods: ['GET'])]
    public function index(PictureRepository $pictureRepository): Response
    {
        return $this->render('user_picture/index.html.twig', [
            'pictures' => $pictureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_picture_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PictureRepository $pictureRepository,  FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            // dd($form);

            if($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile, 'Pre-Event-Photos');
                $picture->setNamePath($imageFileName);
            }
            $picture->setSlug($slugger->slug($picture->getNamePath()));
            $picture->setAlbum('Pre-Event');
            $pictureRepository->save($picture, true);

            return $this->redirectToRoute('app_user_picture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_picture/new.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_picture_show', methods: ['GET'])]
    public function show(Picture $picture): Response
    {
        return $this->render('user_picture/show.html.twig', [
            'picture' => $picture,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_picture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Picture $picture, PictureRepository $pictureRepository): Response
    {
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureRepository->save($picture, true);

            return $this->redirectToRoute('app_user_picture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_picture/edit.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_picture_delete', methods: ['POST'])]
    public function delete(Request $request, Picture $picture, PictureRepository $pictureRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$picture->getId(), $request->request->get('_token'))) {
            $pictureRepository->remove($picture, true);
        }

        return $this->redirectToRoute('app_user_picture_index', [], Response::HTTP_SEE_OTHER);
    }
}
