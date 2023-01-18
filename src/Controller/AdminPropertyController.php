<?php

namespace App\Controller;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/property')]
class AdminPropertyController extends AbstractController
{
    #[Route('/', name: 'app_admin_property_index', methods: ['GET'])]
    public function index(PropertyRepository $propertyRepository): Response
    {
        
        return $this->render('admin_property/index.html.twig', [
            'properties' => $propertyRepository->findAll(),
        ]);
    }
    #[Route('/{slug}/edit', name: 'app_admin_property_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_admin_property_new', methods: ['GET', 'POST'])]
    public function new(Property $property = null, Request $request, PropertyRepository $propertyRepository,SluggerInterface $slugger): Response
    {
        if(!$property) {
        $property = new Property();
        }
        $form = $this->createForm(PropertyType::class, $property);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $property->setSlug($slugger->slug($property->getName())->lower());
            $propertyRepository->save($property, true);

            return $this->redirectToRoute('app_admin_property_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_property/new.html.twig', [
            'property' => $property,
            'edit' => $property->getId(),
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_admin_property_show', methods: ['GET'])]
    public function show(Property $property): Response
    {
        
        return $this->render('admin_property/show.html.twig', [
            'property' => $property,
        ]);
    }

    // #[Route('/{id}/edit', name: 'app_admin_property_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Property $property, PropertyRepository $propertyRepository): Response
    // {
    //     $form = $this->createForm(PropertyType::class, $property);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $propertyRepository->save($property, true);

    //         return $this->redirectToRoute('app_admin_property_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('admin_property/edit.html.twig', [
    //         'property' => $property,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{slug}', name: 'app_admin_property_delete', methods: ['POST'])]
    public function delete(Request $request, Property $property, PropertyRepository $propertyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$property->getId(), $request->request->get('_token'))) {
            $propertyRepository->remove($property, true);
        }

        return $this->redirectToRoute('app_admin_property_index', [], Response::HTTP_SEE_OTHER);
    }
}
