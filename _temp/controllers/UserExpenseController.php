<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Form\ExpenseType;
use App\Repository\ExpenseRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user/expense')]
class UserExpenseController extends AbstractController
{
    // #[Route('/', name: 'app_user_expense_index', methods: ['GET'])]
    // public function index(ExpenseRepository $expenseRepository, ManagerRegistry $doctrine): Response
    // {
    //     $repository = $doctrine->getRepository(Expense::class);
    //     $expenses = $repository->expensesRemaining();
    //     $totalPaid = $repository->sumPaidExpenses();
    //     return $this->render('user_expense/index.html.twig', [
    //         'expenses' => $expenses,
    //         'totalPaid' =>$totalPaid[0]
    //     ]);
    // }

    #[Route('/', name: 'app_user_expense_index', methods: ['GET'])]
    #[Route('/{id}/edit', name: 'app_user_expense_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_user_expense_new', methods: ['GET', 'POST'])]
    public function index(Request $request, Expense $expense = null, ExpenseRepository $expenseRepository, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
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
        ]);
    }

    // #[Route('/{id}/edit', name: 'app_user_expense_edit', methods: ['GET', 'POST'])]
    // #[Route('/new', name: 'app_user_expense_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, Expense $expense = null, ExpenseRepository $expenseRepository, SluggerInterface $slugger): Response
    // {
    //     if(!$expense){
    //     $expense = new Expense();
    //     }

    //     $form = $this->createForm(ExpenseType::class, $expense);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $expense->setSlug($slugger->slug($expense->getName())->lower());
    //         $expenseRepository->save($expense, true);

    //         return $this->redirectToRoute('app_user_expense_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('user_expense/new.html.twig', [
    //         'expense' => $expense,
    //         'edit' => $expense->getId(),
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}', name: 'app_user_expense_show', methods: ['GET'])]
    public function show(Expense $expense): Response
    {
        return $this->render('user_expense/show.html.twig', [
            'expense' => $expense,
        ]);
    }

    // #[Route('/{id}/edit', name: 'app_user_expense_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Expense $expense, ExpenseRepository $expenseRepository): Response
    // {
    //     $form = $this->createForm(ExpenseType::class, $expense);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $expenseRepository->save($expense, true);

    //         return $this->redirectToRoute('app_user_expense_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('user_expense/edit.html.twig', [
    //         'expense' => $expense,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}', name: 'app_user_expense_delete', methods: ['POST'])]
    public function delete(Request $request, Expense $expense, ExpenseRepository $expenseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$expense->getId(), $request->request->get('_token'))) {
            $expenseRepository->remove($expense, true);
        }

        return $this->redirectToRoute('app_user_expense_index', [], Response::HTTP_SEE_OTHER);
    }
}
