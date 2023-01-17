<?php

namespace App\Repository;

use App\Entity\Checklist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Checklist>
 *
 * @method Checklist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Checklist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Checklist[]    findAll()
 * @method Checklist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChecklistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Checklist::class);
    }

    public function save(Checklist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Checklist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function isCheckedCount($value): array
   {
       return $this->createQueryBuilder('c')
           ->select('count(c.isChecked) as check')
           ->where('c.eventList = :val')
           ->andWhere('c.isChecked = true')
           ->setParameter('val', $value)
           ->orderBy('c.id', 'ASC')
           ->getQuery()
           ->getScalarResult()
       ;
   }
    public function isUncheckedCount($value): array
   {
       return $this->createQueryBuilder('c')
           ->select('count(c.isChecked) as uncheck')
           ->where('c.eventList = :val')
           ->andWhere('c.isChecked = false')
           ->setParameter('val', $value)
           ->orderBy('c.id', 'ASC')
           ->getQuery()
           ->getScalarResult()
       ;
   }

//    /**
//     * @return Checklist[] Returns an array of Checklist objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Checklist
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
