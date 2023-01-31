<?php

namespace App\Repository;

use App\Entity\EventList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventList>
 *
 * @method EventList|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventList|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventList[]    findAll()
 * @method EventList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventList::class);
    }

    public function save(EventList $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EventList $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function lastThreeEvents($value): array
    {
        return $this->createQueryBuilder('e')
        ->andWhere('e.client = :val')
        ->andWhere('e.eventDate > :now')
        ->setParameter('val', $value)
        ->setParameter('now', new \DateTime('now'))
        ->orderBy('e.eventDate', 'ASC')
        ->setMaxResults(3)
        ->getQuery()
        ->getResult();
    }

    
    public function pastThreeEvents($value): array
    {
        return $this->createQueryBuilder('e')
        ->andWhere('e.client = :val')
        ->andWhere('e.eventDate < :now')
        ->setParameter('val', $value)
        ->setParameter('now', new \DateTime('now'))
        ->orderBy('e.eventDate', 'ASC')
        ->setMaxResults(3)
        ->getQuery()
        ->getResult();
    }


//    /**
//     * @return EventList[] Returns an array of EventList objects
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

//    public function findOneBySomeField($value): ?EventList
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
