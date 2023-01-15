<?php

namespace App\Controller;

use App\Entity\EventType;
use App\Entity\Property;
use App\Entity\User;
use App\Form\PropertyType;
use App\Repository\EventTypeRepository;
use App\Repository\PropertyRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/dashboard')]
class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard_index', methods: ['GET'])]
    public function index(PropertyRepository $propertyRepo, EventTypeRepository $eventTypeRepo, UserRepository $userRepo): Response
    {
        
        return $this->render('admin_dashboard/index.html.twig', [
        'properties' => $propertyRepo->findBy([], ['createdAt' => 'DESC'], 3),
            'eventTypes' => $eventTypeRepo->findBy([], ['createdAt' => 'DESC'], 3),
            'users' => $userRepo->findBy([], ['createdAt' => 'DESC'], 3),
        ]);
    }
   
}
