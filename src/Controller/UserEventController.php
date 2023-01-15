<?php

namespace App\Controller;

use App\Entity\Guest;
use App\Entity\Expense;
use App\Entity\Picture;
use App\Entity\Tabletab;
use App\Entity\Checklist;
use App\Entity\EventList;
use App\Entity\EventType;
use App\Form\ExpenseType;
use App\Form\ChecklistType;
use App\Entity\EventProperty;
use App\Service\FileUploader;
use App\Repository\GuestRepository;
use App\Repository\ClientRepository;
use App\Repository\ExpenseRepository;
use App\Repository\PictureRepository;
use App\Repository\PropertyRepository;
use App\Repository\TabletabRepository;
use App\Repository\ChecklistRepository;
use App\Repository\EventListRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\EventPropertyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user/event/{id}')]
class UserEventController extends AbstractController {

    // DASHBOARD ROUT WITH EVENT ID
    #[Route('/', name: 'app_user_eventdashboard')]
    public function index(EventList $eventList): Response
    {
        // dd($eventList);

        return $this->render('user_eventdashboard/index.html.twig', [
             'eventList' => $eventList,
        ]);
    }

    // CHECKLIST ROUT WITH EVENT ID - DISPLAY, EDIT, CREATE
    #[Route('/checklist', name: 'app_user_checklist_index', methods: ['GET', 'POST'])]
    #[Route('/{checklist_id}/edit', name: 'app_user_checklist_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_user_checklist_new', methods: ['GET', 'POST'])]
    public function indexChecklist(EventList $eventList, Request $request, Checklist $checklist = null, ChecklistRepository $checklistRepository, SluggerInterface $slugger): Response
    {
       
        // dd($eventList);
        if(!$checklist) {
            $checklist = new Checklist();
            }
    
            $form = $this->createForm(ChecklistType::class, $checklist);
            $form->handleRequest($request);
            // dd($form);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $shortDescSlug = substr($checklist->getDescription(), 0, 30);
                $checklist->setSlug($slugger->slug($shortDescSlug)->lower());
                $checklist->setEventList($eventList);
                $checklistRepository->save($checklist, true);
    
                return $this->redirectToRoute('app_user_checklist_index', [], Response::HTTP_SEE_OTHER);
            }

            
        return $this->renderForm('user_checklist/index.html.twig', [
            'checklists' => $checklistRepository->findBy(['eventList' => $eventList]),
            'checklist' => $checklist,
            'edit' => $checklist->getId(),
            'form' => $form,
            'eventList' => $eventList,
        ]);
    }

    // CHECKLIST ROUT WITH EVENT ID - SHOW
    #[Route('/checklist/{checklist_id}', name: 'app_user_checklist_show', methods: ['GET'])]
    #[Entity('eventList', expr: 'repository.find(eventList_id)')]
    public function showChecklist(EventLIst $eventList, Checklist $checklist): Response
    {
        return $this->render('user_checklist/show.html.twig', [
            'checklist' => $checklist,
            'eventList' => $eventList,
        ]);
    }

    // CHECKLIST ROUT WITH EVENT ID - DELETE
    #[Route('/checklist/{checklist_id}', name: 'app_user_checklist_delete', methods: ['POST'])]
    public function deleteChecklist(Request $request, Checklist $checklist, ChecklistRepository $checklistRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$checklist->getId(), $request->request->get('_token'))) {
            $checklistRepository->remove($checklist, true);
        }

        return $this->redirectToRoute('app_user_checklist_index', [], Response::HTTP_SEE_OTHER);
    }
    
    // EXPENSE ROUT WITH EVENT ID - DISPLAY, EDIT, CREATE
    #[Route('/budget', name: 'app_user_expense_index', methods: ['GET'])]
    // #[Route('/{id}/edit', name: 'app_user_expense_edit', methods: ['GET', 'POST'])]
    // #[Route('/new', name: 'app_user_expense_new', methods: ['GET', 'POST'])]
    public function indexExpense(EventList $eventList, Request $request, Expense $expense = null, ExpenseRepository $expenseRepository, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $repository = $doctrine->getRepository(Expense::class);
        $expenses = $repository->expensesRemaining();
        $totalPaid = $repository->sumPaidExpenses();

        if(!$expense){
            $expense = new Expense();
            }
    
            $form = $this->createForm(ExpenseType::class, $expense);
            $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
                $expense->setSlug($slugger->slug($expense->getName())->lower());
                $expenseRepository->save($expense, true);
    
                return $this->redirectToRoute('app_user_expense_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_expense/index.html.twig', [
            'expenses' => $expenses,
            'totalPaid' =>$totalPaid[0],
            'expense' => $expense,
            'edit' => $expense->getId(),
            'expenseForm' => $form,
            'eventList' => $eventList,
        ]);
    }

    // EXPENSE ROUT WITH EVENT ID - SHOW
    #[Route('/budget/{budget_id}', name: 'app_user_expense_show', methods: ['GET'])]
    public function showExpense(Expense $expense): Response
    {
        return $this->render('user_expense/show.html.twig', [
            'expense' => $expense,
            'eventList' => $eventList,
        ]);
    }
    
    // EXPENSE ROUT WITH EVENT ID - DELETE
    #[Route('/budget/{budget_id}', name: 'app_user_expense_delete', methods: ['POST'])]
    public function deleteExpense(Request $request, Expense $expense, ExpenseRepository $expenseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$expense->getId(), $request->request->get('_token'))) {
            $expenseRepository->remove($expense, true);
        }

        return $this->redirectToRoute('app_user_expense_index', [], Response::HTTP_SEE_OTHER);
    }
    

    // GUEST ROUT WITH EVENT ID - DISPLAY, EDIT, CREATE
    #[Route('/guest', name: 'app_user_guest_index', methods: ['GET'])]
    // #[Route('/{id}/edit', name: 'app_user_guest_edit', methods: ['GET', 'POST'])]
    // #[Route('/new', name: 'app_user_guest_new', methods: ['GET', 'POST'])]
    public function indexGuest(EventList $eventList, Request $request, Guest $guest = null, GuestRepository $guestRepository, ManagerRegistry $doctrine): Response
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

        return $this->renderForm('user_guest/index.html.twig', [
            'guests' => $guestRepository->findAll(),
            'guestNumber' => $allGuestNumber[0],
            'attendings' => $attendings[0],
            'declines' => $declines[0],
            'vegans' => $vegans[0],
            'vegetarians' => $vegetarians[0],
            'omnivores' => $omnivores[0],

            'guest' => $guest,
            'edit' => $guest->getId(),
            'form' => $form,
            'eventList' => $eventList,
        ]);
    }

    // GUEST ROUT WITH EVENT ID - SHOW
    #[Route('/guest/{guest_id}', name: 'app_user_guest_show', methods: ['GET'])]
    public function showGuest(Guest $guest): Response
    {
        return $this->render('user_guest/show.html.twig', [
            'guest' => $guest,
            'eventList' => $eventList,
        ]);
    }
    
    // GUEST ROUT WITH EVENT ID - DELETE
    #[Route('/guest/{guest_id}', name: 'app_user_guest_delete', methods: ['POST'])]
    public function deleteGuest(Request $request, Guest $guest, GuestRepository $guestRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$guest->getId(), $request->request->get('_token'))) {
            $guestRepository->remove($guest, true);
        }

        return $this->redirectToRoute('app_user_guest_index', [], Response::HTTP_SEE_OTHER);
    }


    // TABLE ROUT WITH EVENT ID - DISPLAY, EDIT, CREATE
    #[Route('/table', name: 'app_user_tabletab_index', methods: ['GET'])]
    // #[Route('/{id}/edit', name: 'app_user_tabletab_edit', methods: ['GET', 'POST'])]
    // #[Route('/new', name: 'app_user_tabletab_new', methods: ['GET', 'POST'])]
    public function indexTable(EventList $eventList, Request $request, Tabletab $tabletab = null, TabletabRepository $tabletabRepository, ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Tabletab::class);
        $tableCount = $repository->tableCount();
        // dd($tableCount[0]);

        if (!$tabletab) {
            $tabletab = new Tabletab();
        }


        $form = $this->createForm(TabletabType::class, $tabletab);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tabletabRepository->save($tabletab, true);

            return $this->redirectToRoute('app_user_tabletab_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_tabletab/index.html.twig', [
            'tabletabs' => $tabletabRepository->findAll(),
            'tableCount' => $tableCount[0],

            'tabletab' => $tabletab,
            'edit' => $tabletab->getId(),
            'form' => $form,
            'eventList' => $eventList,
        ]);
    }

    // TABLE ROUT WITH EVENT ID - SHOW
    #[Route('/table/{table_id}', name: 'app_user_tabletab_show', methods: ['GET'])]
    public function showTable(Tabletab $tabletab): Response
    {
        return $this->render('user_tabletab/show.html.twig', [
            'tabletab' => $tabletab,
            'eventList' => $eventList,
        ]);
    }

    // TABLE ROUT WITH EVENT ID - DELETE
    #[Route('/table/table_id}', name: 'app_user_tabletab_delete', methods: ['POST'])]
    public function deleteTable(Request $request, Tabletab $tabletab, TabletabRepository $tabletabRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'. $tabletab->getId(), $request->request->get('_token'))) {
            $tabletabRepository->remove($tabletab, true);
        }

        return $this->redirectToRoute('app_user_tabletab_index', [], Response::HTTP_SEE_OTHER);
    }

    // WEBSITE
    #[Route('/website', name: 'app_user_eventdashboard_website')]
    public function website(EventList $eventList): Response
    {
        return $this->render('user_eventdashboard/website.html.twig', [
            'eventList' => $eventList,
        ]);
    }

    // PRE EVENT PHOTOS

    // PRE-EVENT PHOTOS DISPLAY
    #[Route('/pre_event_photos', name: 'app_user_picture_index', methods: ['GET'])]
    public function indexPreevent(EventList $eventList, PictureRepository $pictureRepository): Response
    {
        return $this->render('user_picture/index.html.twig', [
            'pictures' => $pictureRepository->findBy(['album' => 'Pre Event Photos']),
            'eventList' => $eventList,
        ]);
    }

    // PRE-EVENT PHOTOS CREATE
    #[Route('/pre_event_photos/new', name: 'app_user_picture_new', methods: ['GET', 'POST'])]
    public function newPreevent(EventList $eventList, Request $request, PictureRepository $pictureRepository,  FileUploader $fileUploader, SluggerInterface $slugger): Response
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
            'eventList' => $eventList,
        ]);
    }


    // PRE-EVENT PHOTOS SHOW
    #[Route('/pre_event_photos/preEventPhoto_id}', name: 'app_user_wedding_picture_show', methods: ['GET'])]
    public function showPreevent(Picture $picture): Response
    {
        return $this->render('user_picture/show.html.twig', [
            'picture' => $picture,
            'eventList' => $eventList,
        ]);
    }

    // PRE-EVENT PHOTOS DELETE
    #[Route('/pre_event_photos/{preEventPhoto_id}', name: 'app_user_wedding_picture_delete', methods: ['POST'])]
    public function deletePreevent(Request $request, Picture $picture, PictureRepository $pictureRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$picture->getId(), $request->request->get('_token'))) {
            $pictureRepository->remove($picture, true);
        }

        return $this->redirectToRoute('app_user_picture_index', [], Response::HTTP_SEE_OTHER);
    }




    // WEDDING PHOTOS DISPLAY
    #[Route('/wedding_photos', name: 'app_user_wedding_picture_index', methods: ['GET'])]
    public function indexWedding(EventList $eventList, PictureRepository $pictureRepository): Response
    {
        return $this->render('user_picture/index.html.twig', [
            'pictures' => $pictureRepository->findBy(['album' => 'Event Photos']),
            'eventList' => $eventList,
        ]);
    }

    // WEDDING PHOTOS CREATE
    #[Route('/wedding_photos/new', name: 'app_user_wedding_picture_new', methods: ['GET', 'POST'])]
    public function newWedding(Request $request, PictureRepository $pictureRepository,  FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            // dd($form);

            if($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile, 'Event Photos');
                $picture->setNamePath($imageFileName);
            }
            $picture->setSlug($slugger->slug($picture->getNamePath()));
            $picture->setAlbum('Event Photos');
            $pictureRepository->save($picture, true);

            return $this->redirectToRoute('app_user_picture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_picture/new.html.twig', [
            'picture' => $picture,
            'form' => $form,
            'eventList' => $eventList,
        ]);
    }


    // WEDDING PHOTOS SHOW
    #[Route('/wedding_photos/{wedding_id}', name: 'app_user_wedding_picture_show', methods: ['GET'])]
    public function showWedding(Picture $picture): Response
    {
        return $this->render('user_picture/show.html.twig', [
            'picture' => $picture,
            'eventList' => $eventList,
        ]);
    }

    // WEDDING PHOTOS DELETE
    #[Route('/wedding_photos/{wedding_id}', name: 'app_user_wedding_picture_delete', methods: ['POST'])]
    public function deleteWedding(Request $request, Picture $picture, PictureRepository $pictureRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$picture->getId(), $request->request->get('_token'))) {
            $pictureRepository->remove($picture, true);
        }

        return $this->redirectToRoute('app_user_picture_index', [], Response::HTTP_SEE_OTHER);
    }

    // EVENT PROPERTY
    #[Route('/edit/{id}', name: 'app_user_eventlist_form', methods: ['GET', 'POST'])]
    public function editEvent(EventType $eventType, Request $request, EventPropertyRepository $eventPropertyRepo, ClientRepository $clientRepo, PropertyRepository $propertyRepository, EventListRepository $eventListRepo, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {

        
        $form = $this->createForm(EventListType::class, $eventList);
        $form->handleRequest($request);
        
        $user = $this->getUser();
        $client = $clientRepo->findOneBy(['user' => $user]);

        $properties = $propertyRepository->findBy(["eventType" => $eventType]);
        $eventProp = $eventPropertyRepo->findOneBy(['']);
        // dd($properties);
        
        
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();


            $params = $request->request->all();
            $eventDelete = array_shift($params);
            $image = $eventList->getImage();
            

            if($imageFile) {
                if($image) {
                    $fileUploader->delete($image, 'eventImages');
                }
                $imageFileName = $fileUploader->upload($imageFile, 'eventImage');
                $eventList->setImage($imageFileName);
            } else {
                $eventList->setImage($eventList->getImage());
            }

            $eventList->setEventSlug($slugger->slug($eventList->getEventName()));
            $eventList->setEventType($eventType);
            $eventList->setClient($client);
            
        
            $eventListRepo->save($eventList, true);
            
             foreach($params as $key=>$value){
                $id = (int)explode('_', $key)[1];
                $eventProp = new EventProperty();
                $eventProp->setValue($value)->setProperty($propertyRepository->find($id))->setEventList($eventList);
                $eventPropertyRepo->save($eventProp,true);
            }

            return $this->redirectToRoute('app_user_eventdashboard', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
        }
        
        return $this->renderForm('user_eventlist/new.html.twig', [
            'eventProperties' => $properties,
            'eventType' => $eventType->getName(),
            'form' => $form,
        ]);
    }
}