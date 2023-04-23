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
use Doctrine\Common\Collections\Expr\Value;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/user/event/{eventSlug}')]
class UserEventController extends AbstractController
{


    public function verifyOwner($eventList){
        $user = $this->getUser();

        if($eventList->getClient()->getId() != $user->getClient()->getId())
            throw new AccessDeniedException('Accès non autorisé !');

    }

    // DASHBOARD ROUT WITH EVENT ID
    // NEED CHECKLIST REPOSITORY, EVENTLIST RESPOSITORY (DATE), EVENTLIST VALUES(NAMES), BUDGET REPOSITORY, GUEST REPOSITORY
    #[Route('/', name: 'app_user_eventdashboard')]
    public function index(EventList $eventList, EventPropertyRepository $eventPropertyRepository, ClientRepository $clientRepository, EventListRepository $eventListRepository, GuestRepository $guestRepository, ChecklistRepository $checklistRepository, ExpenseRepository $expenseRepository): Response
    {

        $user = $this->getUser();
        $this->verifyOwner($eventList);

        // budget calculation
        $totalCost = $expenseRepository->sumTotalCost($eventList->getId());
        $totalPaid = $expenseRepository->sumPaidExpenses($eventList->getId());
        $diff = date_diff(new \DateTime(), $eventList->getEventDate());
        // dd($diff);
        // calculating guests
        $allGuestNumber = $guestRepository->allGuestCount($eventList->getId());
        $attendings = $guestRepository->guestsCount($eventList->getId(),"attending");
        $declines = $guestRepository->guestsCount($eventList->getId(), "declined");
        $awating = abs($allGuestNumber - ($attendings + $declines));

        

        // last three unchecked tasks
        $checklistUnchecked = $checklistRepository->findBy(['eventList' => $eventList->getId(), 'isChecked' => false], ['createdAt' => 'DESC'], 5);

        // taking the values of the event
        $valuesNeeded = $eventPropertyRepository->findBy(['eventList' => $eventList->getId()]); 

        if($valuesNeeded) {
            $bride = $valuesNeeded[0];
            if(count($valuesNeeded) > 1) {
                $groom = $valuesNeeded[2];
            } else {
                $groom = '';
            }
        }
        

        return $this->render('user_eventdashboard/index.html.twig', [
            'user' => $user,
            'eventList' => $eventList,

            'bride' => $bride,
            'groom' => $groom,

            'attend' => $attendings,
            'awaiting' => $awating,

            'unchecked' => $checklistUnchecked,

            'totalCost' => $totalCost[0],
            'totalPaid' => $totalPaid[0],

            'diff' => $diff
        ]);
    }

    // CHECKLIST ROUT WITH EVENT ID - DISPLAY, EDIT, CREATE
    #[Route('/checklist', name: 'app_user_checklist_index', methods: ['GET', 'POST'])]

    public function indexChecklist(EventList $eventList, Request $request, ChecklistRepository $checklistRepository, ClientRepository $clientRepository, EventListRepository $eventListRepository): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);

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

            'user' => $user,
        ]);
    }

    #[Route('/checklist/new', name: 'app_user_checklist_new', methods: ['GET', 'POST'])]
    public function newChecklist(EventList $eventList, Request $request, ChecklistRepository $checklistRepository, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);

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
            'edit' => $checklist->getId(),
            'user' => $user,
        ]);
    }

    #[Route('/checklist/{checklist_id}/edit', name: 'app_user_checklist_edit', methods: ['GET', 'POST'])]
    #[Entity('checklist', expr: 'repository.find(checklist_id)')]
    public function editChecklist(EventList $eventList, Checklist $checklist, Request $request, ChecklistRepository $checklistRepository, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);

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

            'user' => $user,
        ]);
    }

    // CHECKLIST ROUT WITH EVENT ID - DELETE
    #[Route('/checklist/{checklist_id}', name: 'app_user_checklist_delete', methods: ['POST'])]
    #[Entity('checklist', expr: 'repository.find(checklist_id)')]
    public function deleteChecklist(EventList $eventList, Checklist $checklist, Request $request, ChecklistRepository $checklistRepository): Response
    {
        
        $this->verifyOwner($eventList);

        if ($this->isCsrfTokenValid('delete' . $checklist->getId(), $request->request->get('_token'))) {
            $checklistRepository->remove($checklist, true);
        }

        return $this->redirectToRoute('app_user_checklist_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
    }



    // EXPENSE ROUT WITH EVENT ID - DISPLAY, EDIT, CREATE
    #[Route('/budget', name: 'app_user_expense_index', methods: ['GET'])]
    public function indexExpense(EventList $eventList, ExpenseRepository $expenseRepository, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);
        //$repository = $doctrine->getRepository(Expense::class);
        //$expenses = $expenseRepository->expensesRemaining();
        $totalPaid = $expenseRepository->sumPaidExpenses($eventList->getId());

        return $this->render('user_expense/index.html.twig', [
            'expenses' => $eventList->getExpenses(),
            'totalPaid' => $totalPaid[0],
            // 'expense' => $expense,
            'eventList' => $eventList,

            'user' => $user,
        ]);
    }


    #[Route('/budget/new', name: 'app_user_expense_new', methods: ['GET', 'POST'])]
    public function newExpense(EventList $eventList, Request $request, ExpenseRepository $expenseRepository, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);

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
            'edit' => $expense->getId(),
            'expenseForm' => $form,
            'eventList' => $eventList,

            'user' => $user,
        ]);
    }

    #[Route('/budget/{expense_id}/edit', name: 'app_user_expense_edit', methods: ['GET', 'POST'])]
    #[Entity('expense', expr: 'repository.find(expense_id)')]
    public function editExpense(EventList $eventList, Expense $expense, Request $request, ExpenseRepository $expenseRepository, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);

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

            'user' => $user,
        ]);
    }

    // EXPENSE ROUT WITH EVENT ID - DELETE
    #[Route('/budget/{expense_id}', name: 'app_user_expense_delete', methods: ['POST'])]
    #[Entity('expense', expr: 'repository.find(expense_id)')]
    public function deleteExpense(EventList $eventList, Expense $expense, Request $request, ExpenseRepository $expenseRepository): Response
    {
        $this->verifyOwner($eventList);

        if ($this->isCsrfTokenValid('delete' . $expense->getId(), $request->request->get('_token'))) {
            $expenseRepository->remove($expense, true);
        }

        return $this->redirectToRoute('app_user_expense_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
    }


    // GUEST ROUT WITH EVENT ID - DISPLAY, EDIT, CREATE
    #[Route('/guest', name: 'app_user_guest_index', methods: ['GET'])]
    public function indexGuest(EventList $eventList, GuestRepository $guestRepository, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);

        $repository = $doctrine->getRepository(Guest::class);
        $allGuestNumber = $repository->allGuestCount($eventList->getId());

        $attendings = $repository->guestsCount($eventList->getId(),"attending");
        $declines = $repository->guestsCount($eventList->getId(), "declined");

        $vegans = $repository->dietCount($eventList->getId(),'vegan');
        $vegetarians = $repository->dietCount($eventList->getId(),'vegetarian');
        $omnivores = $repository->dietCount($eventList->getId(),'omnivore');
        $pescatarians = $repository->dietCount($eventList->getId(),'pescatarian');


        return $this->render('user_guest/index.html.twig', [
            'guests' => $guestRepository->findBy(['eventList' => $eventList->getId()]),
            'guestNumber' => $allGuestNumber,
            'attendings' => $attendings,
            'declines' => $declines,
            'vegans' => $vegans,
            'vegetarians' => $vegetarians,
            'omnivores' => $omnivores,
            'pescatarians' => $pescatarians,

            'eventList' => $eventList,

            'user' => $user,
        ]);
    }

    #[Route('/guest/new', name: 'app_user_guest_new', methods: ['GET', 'POST'])]
    public function newGuest(EventList $eventList, Request $request, GuestRepository $guestRepository): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);

        $guest = new Guest();

        $form = $this->createForm(GuestType::class, $guest, ['eventListId'=> $eventList->getId()]);
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

            'user' => $user,
        ]);
    }


    #[Route('/guest{guest_id}/edit', name: 'app_user_guest_edit', methods: ['GET', 'POST'])]
    #[Entity('guest', expr: 'repository.find(guest_id)')]
    public function editGuest(EventList $eventList, Guest $guest, Request $request, GuestRepository $guestRepository): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);

        $form = $this->createForm(GuestType::class, $guest, ['eventListId'=> $eventList->getId()]);
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

            'user' => $user,
        ]);
    }


    // GUEST ROUT WITH EVENT ID - SHOW
    #[Route('/guest/{guest_id}/show', name: 'app_user_guest_show', methods: ['GET'])]
    #[Entity('guest', expr: 'repository.find(guest_id)')]
    public function showGuest(EventList $eventList, Guest $guest): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);

        return $this->render('user_guest/show.html.twig', [
            'guest' => $guest,
            'eventList' => $eventList,

            'user' => $user,
        ]);
    }

    // GUEST ROUT WITH EVENT ID - DELETE
    #[Route('/guest/{guest_id}', name: 'app_user_guest_delete', methods: ['POST'])]
    #[Entity('guest', expr: 'repository.find(guest_id)')]
    public function deleteGuest(EventList $eventList, Request $request, Guest $guest, GuestRepository $guestRepository): Response
    {
        $this->verifyOwner($eventList);

        if ($this->isCsrfTokenValid('delete' . $guest->getId(), $request->request->get('_token'))) {
            $guestRepository->remove($guest, true);
        }

        return $this->redirectToRoute('app_user_guest_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
    }
    
    
    
    // TABLE ROUT WITH EVENT ID - DISPLAY, EDIT, CREATE
    #[Route('/table', name: 'app_user_tabletab_index', methods: ['GET'])]
    public function indexTable(EventList $eventList, TabletabRepository $tabletabRepository): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);

        $tableCount = $tabletabRepository->tableCount($eventList->getId());
        $tabletabs = $tabletabRepository->findBy(['eventList' => $eventList]);

        return $this->render('user_tabletab/index.html.twig', [
            'tabletabs' => $tabletabs,
            'tableCount' => $tableCount[0],
            'eventList' => $eventList,

            'user' => $user,
        ]);
    }

    
    #[Route('/table/new', name: 'app_user_tabletab_new', methods: ['GET', 'POST'])]
    public function newTable(EventList $eventList, Request $request, TabletabRepository $tabletabRepository): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);

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
            'edit' => $tabletab->getId(),
            'user' => $user,
        ]);

    }

    #[Route('/table/{tabletab_id}/edit', name: 'app_user_tabletab_edit', methods: ['GET', 'POST'])]
    #[Entity('tabletab', expr: 'repository.find(tabletab_id)')]
    public function editTable(EventList $eventList, Tabletab $tabletab, Request $request, TabletabRepository $tabletabRepository): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);

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

            'user' => $user,
        ]);

    }
    

    // TABLE ROUT WITH EVENT ID - DELETE
    #[Route('/table/{tabletab_id}', name: 'app_user_tabletab_delete', methods: ['POST'])]
    #[Entity('tabletab', expr: 'repository.find(tabletab_id)')]
    public function deleteTable(EventList $eventList, Tabletab $tabletab, Request $request, TabletabRepository $tabletabRepository): Response
    {
        $this->verifyOwner($eventList);

        if ($this->isCsrfTokenValid('delete' . $tabletab->getId(), $request->request->get('_token'))) {
            $tabletabRepository->remove($tabletab, true);
        }

        return $this->redirectToRoute('app_user_tabletab_index', ['id' => $eventList->getId()], Response::HTTP_SEE_OTHER);
    }

    // WEBSITE
    #[Route('/website', name: 'app_user_website_theme')]
    public function website(EventList $eventList, GuestRepository $guestRepository): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);
        $guests = $guestRepository->findBy(['eventList' => $eventList->getId()]);
        
        return $this->render('user_website/theme.html.twig', [
            'eventList' => $eventList,
            'guests' => $guests,

            'user' => $user,
        ]);
    }
    
    
    
    // PRE EVENT PHOTOS

    // PRE-EVENT PHOTOS DISPLAY
    #[Route('/pre_event_photos', name: 'app_user_picture_index', methods: ['GET', 'POST'])]
    public function indexPreevent(EventList $eventList, Request $request, PictureRepository $pictureRepository, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);

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

            return $this->redirectToRoute('app_user_picture_index', ['eventSlug' => $eventList->getEventSlug()], Response::HTTP_SEE_OTHER);
        }
       
        
        return $this->renderForm('user_picture/index.html.twig', [
            'pictures' => $pictures,
            'form' => $form,
            'eventList' => $eventList,

            'user' => $user,
        ]);
    } 

    // PRE-EVENT PHOTOS CREATE
    #[Route('/pre_event_photos/new', name: 'app_user_picture_new', methods: ['GET', 'POST'])]
    public function newPreevent(EventList $eventList, Request $request, PictureRepository $pictureRepository,  FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);

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

            'user' => $user,
        ]);
    }


    // PRE-EVENT PHOTOS SHOW
    #[Route('/pre_event_photos/{picture_id}', name: 'app_user_picture_show', methods: ['GET'])]
    public function showPreevent(EventList $eventList, Picture $picture, Request $request, PictureRepository $pictureRepository,  FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);

        return $this->render('user_picture/show.html.twig', [
            'picture' => $picture,
            'eventList' => $eventList,

            'user' => $user,
        ]);
    }

    // PRE-EVENT PHOTOS DELETE
    #[Route('/pre_event_photos/{picture_id}', name: 'app_user_picture_delete', methods: ['POST'])]
    #[Entity('picture', expr: 'repository.find(picture_id)')]
    public function deletePreevent(EventList $eventList, Picture $picture, Request $request, PictureRepository $pictureRepository, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        $this->verifyOwner($eventList);

        if ($this->isCsrfTokenValid('delete' . $picture->getId(), $request->request->get('_token'))) {
            $fileUploader->delete($picture->getNamePath(), 'Pre-Event-Photos');
            $pictureRepository->remove($picture, true);
        }

        return $this->redirectToRoute('app_user_picture_index', ['eventSlug' => $eventList->getEventSlug()], Response::HTTP_SEE_OTHER);
    }




    // WEDDING PHOTOS DISPLAY
    #[Route('/wedding_photos', name: 'app_user_wedding_index', methods: ['GET', 'POST'])]
    public function indexWedding(EventList $eventList, Request $request, PictureRepository $pictureRepository, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);

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

            return $this->redirectToRoute('app_user_wedding_index', ['eventSlug' => $eventList->getEventSlug()], Response::HTTP_SEE_OTHER);
        }
       
        
        return $this->renderForm('user_wedding/index.html.twig', [
            'pictures' => $pictures,
            'form' => $form,
            'eventList' => $eventList,
            'user' => $user,
        ]);
    }


    // WEDDING PHOTOS CREATE
    #[Route('/wedding_photos/new', name: 'app_user_wedding_new', methods: ['GET', 'POST'])]
    public function newWedding(EventList $eventList, Request $request, PictureRepository $pictureRepository,  FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $this->verifyOwner($eventList);

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

            return $this->redirectToRoute('app_user_wedding_index', ['eventSlug' => $eventList->getEventSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_wedding/new.html.twig', [
            'picture' => $picture,
            'form' => $form,
            'eventList' => $eventList,
            'user' => $user,
        ]);
    }


     // WEDDING PHOTOS DELETE
     #[Route('/wedding_photos/{picture_id}', name: 'app_user_wedding_delete', methods: ['POST'])]
     #[Entity('picture', expr: 'repository.find(picture_id)')]
     public function deleteWedding(EventList $eventList, Picture $picture, Request $request, PictureRepository $pictureRepository, FileUploader $fileUploader): Response
     {
        $this->verifyOwner($eventList);

         if ($this->isCsrfTokenValid('delete' . $picture->getId(), $request->request->get('_token'))) {
             $fileUploader->delete($picture->getNamePath(), 'Wedding-Photos');
             $pictureRepository->remove($picture, true);
         }
 
         return $this->redirectToRoute('app_user_wedding_index', ['eventSlug' => $eventList->getEventSlug()], Response::HTTP_SEE_OTHER);
     }
     

     // EVENT PROPERTY
    #[Route('/edit', name: 'app_user_eventlist_edit', methods: ['GET', 'POST'])]
    // #[Entity('eventtype', expr: 'repository.find(tabletab_id)')]
    public function editEvent(EventList $eventList, Request $request, EventTypeRepository $eventTypeRepo, EventPropertyRepository $eventPropertyRepo, ClientRepository $clientRepo, PropertyRepository $propertyRepository, EventListRepository $eventListRepo, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        $this->verifyOwner($eventList);
        $user = $this->getUser();

        $form = $this->createForm(EventListType::class, $eventList);
        $form->handleRequest($request);

        $user = $this->getUser();
        $client = $clientRepo->findOneBy(['user' => $user]);

        // dd($eventList);
        $eventType = $eventList->getEventType();

        $valuesNeeded = $eventPropertyRepo->findBy(['eventList' => $eventList->getId()]);
        // dd($valuesNeeded);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $eventList->getImage();
            $imageFile = $form->get('image')->getData();

            $params = $request->request->all();
            $eventDelete = array_shift($params);
            // dump($params);
            
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


            $entityValues = [];
            foreach($params as $key=>$value){
                $entityValues [] = $value;
            }

            for($i = 0; $i < count($valuesNeeded); $i++) {
                    $valuesNeeded[$i]->setValue($entityValues[$i]);
                    dump($valuesNeeded[$i]);
                    $eventPropertyRepo->save($valuesNeeded[$i],true);
                }
                
            return $this->redirectToRoute('app_user_eventdashboard', ['eventSlug' => $eventList->getEventSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_eventlist/edit.html.twig', [
            'user' => $user,
            'eventList' => $eventList,
            'eventType' => $eventType,
            'form' => $form,
            'eventValues' => $valuesNeeded
        ]);
    }
}
