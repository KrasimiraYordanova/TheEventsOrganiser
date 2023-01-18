<?php

namespace App\Repository;

use App\Entity\Tabletab;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tabletab>
 *
 * @method Tabletab|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tabletab|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tabletab[]    findAll()
 * @method Tabletab[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TabletabRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tabletab::class);
    }

    public function save(Tabletab $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Tabletab $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function tableCount($value): array
   {
       return $this->createQueryBuilder('t')
           ->select('count(t.name) as numberTables')
           ->andWhere('t.eventList = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getScalarResult()
       ;
   }

//    /**
//     * @return Tabletab[] Returns an array of Tabletab objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Tabletab
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
