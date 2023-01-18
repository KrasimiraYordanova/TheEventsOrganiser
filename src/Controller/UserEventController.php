<?php

namespace App\Controller;

use App\Entity\Guest;
use App\Entity\Expense;
use App\Entity\Picture;
use App\Form\GuestType;
use App\Entity\Tabletab;
use App\Entity\Checklist;
use App\Entity\EventList;
use App\Entity\EventType;
use App\Form\ExpenseType;
use App\Form\PictureType;
use App\Form\TabletabType;
use App\Form\ChecklistType;
use App\Form\EventListType;
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
use App\Repository\EventTypeRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\EventPropertyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user/event/{id}')]
class UserEventController extends AbstractController
{

    // DASHBOARD ROUT WITH EVENT ID
    // NEED CHECKLIST REPOSITORY, EVENTLIST RESPOSITORY (DATE), EVENTLIST VALUES(NAMES), BUDGET REPOSITORY, GUEST REPOSITORY
    #[Route('/', name: 'app_user_eventdashboard')]
    public function index(EventList $eventList, EventPropertyRepository $eventPropertyRepository, GuestRepository $guestRepository, ChecklistRepository $checklistRepository, ExpenseRepository $expenseRepository): Response
    {

        
        // budget calculation
        $totalCost = $expenseRepository->sumTotalCost($eventList->getId());
        $totalPaid = $expenseRepository->sumPaidExpenses($eventList->getId());
        // calculating guests
        $allGuestNumber = $guestRepository->allGuestCount($eventList->getId());
        $attendings = $guestRepository->guestsCount($eventList->getId(),"attending");
        $declines = $guestRepository->guestsCount($eventList->getId(), "declined");
        $awating = abs($allGuestNumber - ($attendings + $declines));

        

        // last three unchecked tasks
        $checklistUnchecked = $checklistRepository->findBy(['eventList' => $eventList->getId(), 'isChecked' => false], ['createdAt' => 'DESC'], 5);

        // taking the values of the event
        $valuesNeeded = $eventPropertyRepository->findBy(['eventList' => $eventList->getId()]); 

        $bride = $valuesNeeded[0];
        $groom = $valuesNeeded[2];

        return $this->render('user_eventdashboard/index.html.twig', [
            'eventList' => $eventList,

            'bride' => $bride,
            'groom' => $groom,

            'attend' => $attendings,
            'awaiting' => $awating,

            'unchecked' => $checklistUnchecked,

            'totalCost' => $totalCost[0],
            'totalPaid' => $totalPaid[0]
        ]);
    }

    // CHECKLIST ROUT WITH EVENT ID - DISPLAY, EDIT, CREATE
    #[Route('/checklist', name: 'app_user_checklist_index', methods: ['GET', 'POST'])]

    public function indexChecklist(EventList $eventList, Request $request, ChecklistRepository $checklistRepository): Response
    {
        $allChecklists = $checklistRepository->findBy(['eventList' => $eventList->getId()]);
        // dd($allChecklists);
        $checked = $checklistRepository->isCheckedCount($eventList->getId());
        $unchecked = $checklistRepository->isUncheckedCount($eventList->getId());
        // dd($unchecked[0]);

        return $this->render('user_checklist/index.html.twig', [
            'checklists' => $allChecklists,
            'checked' => $checked[0],
            'unchecked' => $unchecked[0],

            'eventList' => $eventList,
        ]);
    }

    #[Route('/checklist/new', name: 'app_user_checklist_new', methods: ['GET', 'POST'])]
    public function newChecklist(EventList $eventList, Request $request, ChecklistRepository $checklistRepository, SluggerInterface $slugger): Response
    {


        $checklist = new Checklist();
        $form = $this->createForm(ChecklistType::class, $checklist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shortDescSlug = substr($checklist->getDescription(), 0, 10);
            $checklist->setSlug($slugger->slug($shortDescSlug)->lower());
            $checklist->setEventList($eventList);
            $checklistRepository->save($checklist, true);

            return $this->redirectToRoute('app_user_checklist_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_checklist/new.html.twig', [
            'checklist' => $checklist,
            'form' => $form,
            'eventList' => $eventList,
        ]);
    }

    #[Route('/checklist/{checklist_id}/edit', name: 'app_user_checklist_edit', methods: ['GET', 'POST'])]
    #[Entity('checklist', expr: 'repository.find(checklist_id)')]
    public function editChecklist(EventList $eventList, Checklist $checklist, Request $request, ChecklistRepository $checklistRepository, SluggerInterface $slugger): Response
    {

        $form = $this->createForm(ChecklistType::class, $checklist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shortDescSlug = substr($checklist->getDescription(), 0, 10);
            $checklist->setSlug($slugger->slug($shortDescSlug)->lower());
            $checklist->setEventList($eventList);
            $checklistRepository->save($checklist, true);

            return $this->redirectToRoute('app_user_checklist_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_checklist/new.html.twig', [
            'checklist' => $checklist,
            'edit' => $checklist->getId(),
            'form' => $form,
            'eventList' => $eventList,
        ]);
    }

    // CHECKLIST ROUT WITH EVENT ID - SHOW
    // #[Route('/checklist/{checklist_id}', name: 'app_user_checklist_show', methods: ['GET'])]
    // public function showChecklist(EventLIst $eventList, Checklist $checklist): Response
    // {
    //     return $this->render('user_checklist/show.html.twig', [
    //         'checklist' => $checklist,
    //         'eventList' => $eventList,
    //     ]);
    // }

    // CHECKLIST ROUT WITH EVENT ID - DELETE
    #[Route('/checklist/{checklist_id}', name: 'app_user_checklist_delete', methods: ['POST'])]
    #[Entity('checklist', expr: 'repository.find(checklist_id)')]
    public function deleteChecklist(EventList $eventList, Checklist $checklist, Request $request, ChecklistRepository $checklistRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $checklist->getId(), $request->request->get('_token'))) {
            $checklistRepository->remove($checklist, true);
        }

        return $this->redirectToRoute('app_user_checklist_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
    }



    // EXPENSE ROUT WITH EVENT ID - DISPLAY, EDIT, CREATE
    #[Route('/budget', name: 'app_user_expense_index', methods: ['GET'])]
    public function indexExpense(EventList $eventList, ExpenseRepository $expenseRepository, ManagerRegistry $doctrine): Response
    {
        
        //$repository = $doctrine->getRepository(Expense::class);
        //$expenses = $expenseRepository->expensesRemaining();
        $totalPaid = $expenseRepository->sumPaidExpenses($eventList->getId());

        return $this->render('user_expense/index.html.twig', [
            'expenses' => $eventList->getExpenses(),
            'totalPaid' => $totalPaid[0],
            // 'expense' => $expense,
            'eventList' => $eventList,
        ]);
    }


    #[Route('/budget/new', name: 'app_user_expense_new', methods: ['GET', 'POST'])]
    public function newExpense(EventList $eventList, Request $request, ExpenseRepository $expenseRepository, SluggerInterface $slugger): Response
    {
        $expense = new Expense();
        $form = $this->createForm(ExpenseType::class, $expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expense->setSlug($slugger->slug($expense->getName())->lower());
            $expense->setEventList($eventList);
            $expenseRepository->save($expense, true);

            return $this->redirectToRoute('app_user_expense_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_expense/new.html.twig', [
            'expense' => $expense,
            // 'edit' => $expense->getId(),
            'expenseForm' => $form,
            'eventList' => $eventList,
        ]);
    }

    #[Route('/budget/{expense_id}/edit', name: 'app_user_expense_edit', methods: ['GET', 'POST'])]
    #[Entity('expense', expr: 'repository.find(expense_id)')]
    public function editExpense(EventList $eventList, Expense $expense, Request $request, ExpenseRepository $expenseRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ExpenseType::class, $expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expense->setSlug($slugger->slug($expense->getName())->lower());
            $expense->setEventList($eventList);
            $expenseRepository->save($expense, true);

            return $this->redirectToRoute('app_user_expense_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_expense/new.html.twig', [
            'expense' => $expense,
            'edit' => $expense->getId(),
            'expenseForm' => $form,
            'eventList' => $eventList,
        ]);
    }

    // EXPENSE ROUT WITH EVENT ID - SHOW
    // #[Route('/budget/{budget_id}', name: 'app_user_expense_show', methods: ['GET'])]
    // public function showExpense(Expense $expense): Response
    // {
    //     return $this->render('user_expense/show.html.twig', [
    //         'expense' => $expense,
    //         'eventList' => $eventList,
    //     ]);
    // }

    // EXPENSE ROUT WITH EVENT ID - DELETE
    #[Route('/budget/{expense_id}', name: 'app_user_expense_delete', methods: ['POST'])]
    #[Entity('expense', expr: 'repository.find(expense_id)')]
    public function deleteExpense(EventList $eventList, Expense $expense, Request $request, ExpenseRepository $expenseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $expense->getId(), $request->request->get('_token'))) {
            $expenseRepository->remove($expense, true);
        }

        return $this->redirectToRoute('app_user_expense_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
    }


    // GUEST ROUT WITH EVENT ID - DISPLAY, EDIT, CREATE
    #[Route('/guest', name: 'app_user_guest_index', methods: ['GET'])]
    public function indexGuest(EventList $eventList, GuestRepository $guestRepository, ManagerRegistry $doctrine): Response
    {

        $repository = $doctrine->getRepository(Guest::class);
        $allGuestNumber = $repository->allGuestCount($eventList->getId());

        $attendings = $repository->guestsCount($eventList->getId(),"attending");
        $declines = $repository->guestsCount($eventList->getId(), "declined");

        $vegans = $repository->dietCount($eventList->getId(),'vegan');
        $vegetarians = $repository->dietCount($eventList->getId(),'vegetarian');
        $omnivores = $repository->dietCount($eventList->getId(),'omnivore');


        return $this->render('user_guest/index.html.twig', [
            'guests' => $guestRepository->findBy(['eventList' => $eventList->getId()]),
            'guestNumber' => $allGuestNumber,
            'attendings' => $attendings,
            'declines' => $declines,
            'vegans' => $vegans,
            'vegetarians' => $vegetarians,
            'omnivores' => $omnivores,

            'eventList' => $eventList,
        ]);
    }

    #[Route('/guest/new', name: 'app_user_guest_new', methods: ['GET', 'POST'])]
    public function newGuest(EventList $eventList, Request $request, GuestRepository $guestRepository): Response
    {

        $guest = new Guest();
        $form = $this->createForm(GuestType::class, $guest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $token = $request->request->all();
            $token = array_pop($token);

            $guest->setEventList($eventList);
            $guest->setToken($token);
            $guestRepository->save($guest, true);

            return $this->redirectToRoute('app_user_guest_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_guest/new.html.twig', [
            'guest' => $guest,
            'edit' => $guest->getId(),
            'form' => $form,
            'eventList' => $eventList,
        ]);
    }


    #[Route('/guest{guest_id}/edit', name: 'app_user_guest_edit', methods: ['GET', 'POST'])]
    #[Entity('guest', expr: 'repository.find(guest_id)')]
    public function editGuest(EventList $eventList, Guest $guest, Request $request, GuestRepository $guestRepository): Response
    {
        $form = $this->createForm(GuestType::class, $guest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $guest->setEventList($eventList);
            $guestRepository->save($guest, true);

            return $this->redirectToRoute('app_user_guest_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_guest/new.html.twig', [
            'guest' => $guest,
            'edit' => $guest->getId(),
            'form' => $form,
            'eventList' => $eventList,
        ]);
    }


    // GUEST ROUT WITH EVENT ID - SHOW
    #[Route('/guest/{guest_id}/show', name: 'app_user_guest_show', methods: ['GET'])]
    #[Entity('guest', expr: 'repository.find(guest_id)')]
    public function showGuest(EventList $eventList, Guest $guest): Response
    {
        return $this->render('user_guest/show.html.twig', [
            'guest' => $guest,
            'eventList' => $eventList,
        ]);
    }

    // GUEST ROUT WITH EVENT ID - DELETE
    #[Route('/guest/{guest_id}', name: 'app_user_guest_delete', methods: ['POST'])]
    #[Entity('guest', expr: 'repository.find(guest_id)')]
    public function deleteGuest(EventList $eventList, Request $request, Guest $guest, GuestRepository $guestRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $guest->getId(), $request->request->get('_token'))) {
            $guestRepository->remove($guest, true);
        }

        return $this->redirectToRoute('app_user_guest_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
    }
    
    
    
    // TABLE ROUT WITH EVENT ID - DISPLAY, EDIT, CREATE
    #[Route('/table', name: 'app_user_tabletab_index', methods: ['GET'])]
    public function indexTable(EventList $eventList, TabletabRepository $tabletabRepository): Response
    {
        
        $tableCount = $tabletabRepository->tableCount($eventList->getId());
        $tabletabs = $tabletabRepository->findBy(['eventList' => $eventList]);

        return $this->render('user_tabletab/index.html.twig', [
            'tabletabs' => $tabletabs,
            'tableCount' => $tableCount[0],
            'eventList' => $eventList,
        ]);
    }

    
    #[Route('/table/new', name: 'app_user_tabletab_new', methods: ['GET', 'POST'])]
    public function newTable(EventList $eventList, Request $request, TabletabRepository $tabletabRepository): Response
    {
        
        $tabletab = new Tabletab();
        $form = $this->createForm(TabletabType::class, $tabletab);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $tabletab->setEventList($eventList);
            $tabletabRepository->save($tabletab, true);

            return $this->redirectToRoute('app_user_tabletab_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_tabletab/new.html.twig', [
            'tabletab' => $tabletab,
            'form' => $form,
            'eventList' => $eventList,
        ]);

    }

    #[Route('/table/{tabletab_id}/edit', name: 'app_user_tabletab_edit', methods: ['GET', 'POST'])]
    #[Entity('tabletab', expr: 'repository.find(tabletab_id)')]
    public function editTable(EventList $eventList, Tabletab $tabletab, Request $request, TabletabRepository $tabletabRepository): Response
    {

        $form = $this->createForm(TabletabType::class, $tabletab);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tabletab->setEventList($eventList);
            $tabletabRepository->save($tabletab, true);

            return $this->redirectToRoute('app_user_tabletab_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_tabletab/new.html.twig', [
            'tabletab' => $tabletab,
            'edit' => $tabletab->getId(),
            'form' => $form,
            'eventList' => $eventList,
        ]);

    }
    
    
    // TABLE ROUT WITH EVENT ID - SHOW
    // #[Route('/table/{table_id}', name: 'app_user_tabletab_show', methods: ['GET'])]
    // public function showTable(Tabletab $tabletab): Response
    // {
    //     return $this->render('user_tabletab/show.html.twig', [
    //         'tabletab' => $tabletab,
    //         'eventList' => $eventList,
    //     ]);
    // }

    // TABLE ROUT WITH EVENT ID - DELETE
    #[Route('/table/{tabletab_id}', name: 'app_user_tabletab_delete', methods: ['POST'])]
    #[Entity('tabletab', expr: 'repository.find(tabletab_id)')]
    public function deleteTable(EventList $eventList, Tabletab $tabletab, Request $request, TabletabRepository $tabletabRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tabletab->getId(), $request->request->get('_token'))) {
            $tabletabRepository->remove($tabletab, true);
        }

        return $this->redirectToRoute('app_user_tabletab_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
    }

    // WEBSITE
    #[Route('/website', name: 'app_user_website_theme')]
    public function website(EventList $eventList): Response
    {
        
        return $this->render('user_website/theme.html.twig', [
            'eventList' => $eventList
        ]);
    }
    
    
    
    // PRE EVENT PHOTOS

    // PRE-EVENT PHOTOS DISPLAY
    #[Route('/pre_event_photos', name: 'app_user_picture_index', methods: ['GET', 'POST'])]
    public function indexPreevent(EventList $eventList, Request $request, PictureRepository $pictureRepository, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        $pictures = $pictureRepository->findBy(['eventList' => $eventList->getId(), 'album' => 'Pre-Event']);

        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile, 'Pre-Event-Photos');
                $picture->setNamePath($imageFileName);
            }
            $picture->setSlug($slugger->slug($picture->getNamePath()));
            $picture->setAlbum('Pre-Event');
            $picture->setEventList($eventList);
            $pictureRepository->save($picture, true);

            return $this->redirectToRoute('app_user_picture_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
        }
       
        
        return $this->renderForm('user_picture/index.html.twig', [
            'pictures' => $pictures,
            'form' => $form,
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

            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile, 'Pre-Event-Photos');
                $picture->setNamePath($imageFileName);
            }
            $picture->setSlug($slugger->slug($picture->getNamePath()));
            $picture->setAlbum('Pre-Event');
            $picture->setEventList($eventList);
            $pictureRepository->save($picture, true);

            return $this->redirectToRoute('app_user_picture_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_picture/new.html.twig', [
            'picture' => $picture,
            'form' => $form,
            'eventList' => $eventList,
        ]);
    }


    // PRE-EVENT PHOTOS SHOW
    #[Route('/pre_event_photos/{picture_id}', name: 'app_user_picture_show', methods: ['GET'])]
    public function showPreevent(EventList $eventList, Picture $picture, Request $request, PictureRepository $pictureRepository,  FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        return $this->render('user_picture/show.html.twig', [
            'picture' => $picture,
            'eventList' => $eventList,
        ]);
    }

    // PRE-EVENT PHOTOS DELETE
    #[Route('/pre_event_photos/{picture_id}', name: 'app_user_picture_delete', methods: ['POST'])]
    #[Entity('picture', expr: 'repository.find(picture_id)')]
    public function deletePreevent(EventList $eventList, Picture $picture, Request $request, PictureRepository $pictureRepository, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        if ($this->isCsrfTokenValid('delete' . $picture->getId(), $request->request->get('_token'))) {
            $fileUploader->delete($picture->getNamePath(), 'Pre-Event-Photos');
            $pictureRepository->remove($picture, true);
        }

        return $this->redirectToRoute('app_user_picture_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
    }




    // WEDDING PHOTOS DISPLAY
    #[Route('/wedding_photos', name: 'app_user_wedding_index', methods: ['GET', 'POST'])]
    public function indexWedding(EventList $eventList, Request $request, PictureRepository $pictureRepository, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        $pictures = $pictureRepository->findBy(['eventList' => $eventList->getId(), 'album' => 'wedding']);

        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile, 'Wedding-Photos');
                $picture->setNamePath($imageFileName);
            }
            $picture->setSlug($slugger->slug($picture->getNamePath()));
            $picture->setAlbum('wedding');
            $picture->setEventList($eventList);
            $pictureRepository->save($picture, true);

            return $this->redirectToRoute('app_user_wedding_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
        }
       
        
        return $this->renderForm('user_wedding/index.html.twig', [
            'pictures' => $pictures,
            'form' => $form,
            'eventList' => $eventList,
        ]);
    }


    // PRE-EVENT PHOTOS CREATE
    #[Route('/wedding_photos/new', name: 'app_user_wedding_new', methods: ['GET', 'POST'])]
    public function newWedding(EventList $eventList, Request $request, PictureRepository $pictureRepository,  FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile, 'Wedding-Photos');
                $picture->setNamePath($imageFileName);
            }
            $picture->setSlug($slugger->slug($picture->getNamePath()));
            $picture->setAlbum('wedding');
            $picture->setEventList($eventList);
            $pictureRepository->save($picture, true);

            return $this->redirectToRoute('app_user_wedding_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_wedding/new.html.twig', [
            'picture' => $picture,
            'form' => $form,
            'eventList' => $eventList,
        ]);
    }


     // WEDDING PHOTOS DELETE
     #[Route('/wedding_photos/{picture_id}', name: 'app_user_wedding_delete', methods: ['POST'])]
     #[Entity('picture', expr: 'repository.find(picture_id)')]
     public function deleteWedding(EventList $eventList, Picture $picture, Request $request, PictureRepository $pictureRepository, FileUploader $fileUploader): Response
     {
         if ($this->isCsrfTokenValid('delete' . $picture->getId(), $request->request->get('_token'))) {
             $fileUploader->delete($picture->getNamePath(), 'Wedding-Photos');
             $pictureRepository->remove($picture, true);
         }
 
         return $this->redirectToRoute('app_user_wedding_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
     }
     
     
     
     
     
     // EVENT PROPERTY
    #[Route('/edit', name: 'app_user_eventlist_edit', methods: ['GET', 'POST'])]
    // #[Entity('eventtype', expr: 'repository.find(tabletab_id)')]
    public function editEvent(EventList $eventList, Request $request, EventTypeRepository $eventTypeRepo, EventPropertyRepository $eventPropertyRepo, ClientRepository $clientRepo, PropertyRepository $propertyRepository, EventListRepository $eventListRepo, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {

        $form = $this->createForm(EventListType::class, $eventList);
        $form->handleRequest($request);

        $user = $this->getUser();
        $client = $clientRepo->findOneBy(['user' => $user]);

        // dd($eventList);
        $eventType = $eventList->getEventType();
        // dd($eventType);
        //$eventType = $eventTypeRepo->findOneBy(['id' => $eventTypeNeeded->getId()]);
        // taking the right eventType via eventList table to be able to...
        // ... find the right properties
        //$propsNeeded = $propertyRepo->findBy(['eventType' => $eventType->getId()]);
        // and get the values for that event - eventProperty table and get the values for eventListId
        $valuesNeeded = $eventPropertyRepo->findBy(['eventList' => $eventList->getId()]);

    
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $eventList->getImage();
            $imageFile = $form->get('image')->getData();

            $params = $request->request->all();
            $eventDelete = array_shift($params);


            if ($imageFile) {
                if ($image) {
                    $fileUploader->delete($image, 'eventImages');
                }
                $imageFileName = $fileUploader->upload($imageFile, 'eventImage');
                $eventList->setImage($imageFileName);
            } else {
                $eventList->setImage($eventList->getImage());
            }

            $eventList->setEventSlug($slugger->slug($eventList->getEventName())->lower());
            $eventList->setEventType($eventType);
            $eventList->setClient($client);
            $eventListRepo->save($eventList, true);


            foreach($params as $key=>$value){
                $id = (int)explode('_', $key)[1];
                // dd($value);
                // dd($id);
 
                $eventPropertyRepo->save($value,true);
            }

            return $this->redirectToRoute('app_user_eventdashboard', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_eventlist/edit.html.twig', [
            'eventList' => $eventList,
            'eventType' => $eventType,
            'form' => $form,
            'eventValues' => $valuesNeeded
        ]);
    }
}
