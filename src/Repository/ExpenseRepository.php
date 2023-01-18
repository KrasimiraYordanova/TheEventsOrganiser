<?php

namespace App\Repository;

use App\Entity\Expense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Expense>
 *
 * @method Expense|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expense|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expense[]    findAll()
 * @method Expense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    public function save(Expense $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Expense $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Expense[] Returns an array of Expense objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Expense
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function expensesRemaining(): array
   {
       return $this->createQueryBuilder('e')
           ->select('e.id, e.name, e.slug, e.description, e.totalCost, e.totalPaid, e.createdAt, e.updatedAt, (e.totalCost - e.totalPaid) as remaining')
           ->orderBy('e.id', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }

   public function sumPaidExpenses($listEventId): array
   {
       return $this->createQueryBuilder('e')
           ->select('SUM(e.totalPaid) as paidTotal')
           ->andWhere('e.eventList = :id')
           ->setParameter('id', $listEventId)
           ->orderBy('e.id', 'ASC')
           ->getQuery()
           ->getScalarResult()
       ;
   }
   public function sumTotalCost($listEventId): array
   {
       return $this->createQueryBuilder('e')
           ->select('SUM(e.totalCost) as totalCost')
           ->andWhere('e.eventList = :id')
           ->setParameter('id', $listEventId)
           ->orderBy('e.id', 'ASC')
           ->getQuery()
           ->getScalarResult()
       ;
   }
}
