<?php

namespace App\Repository;

use App\Entity\Guest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Guest>
 *
 * @method Guest|null find($id, $lockMode = null, $lockVersion = null)
 * @method Guest|null findOneBy(array $criteria, array $orderBy = null)
 * @method Guest[]    findAll()
 * @method Guest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Guest::class);
    }

    public function save(Guest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Guest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    // counting all guests for this event
    public function allGuestCount($listEventId): int
    {
        return $this->createQueryBuilder('g')
            ->select("count(g) as guestNumber")
            ->andWhere('g.eventList = :id')
            ->setParameter('id', $listEventId)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    // counting guests for a specific event by attendance
    public function guestsCount($listEventId, $rdsvp = null): int
   {
       return $this->createQueryBuilder('g')
           ->select("count(g.rdsvp) as {$rdsvp}")
           ->andWhere('g.eventList = :id')
           ->andWhere('g.rdsvp = :val')
           ->setParameter('id', $listEventId)
           ->setParameter('val', $rdsvp)
           ->getQuery()
           ->getSingleScalarResult()
       ;
   }

   // counting guests for a specific event by diet
   public function dietCount($listEventId, $diet = null): int
   {
       return $this->createQueryBuilder('g')
           ->select("count(g.diet) as {$diet}")
           ->andWhere('g.eventList = :id')
           ->andWhere('g.diet = :val')
           ->setParameter('id', $listEventId)
           ->setParameter('val', $diet)
           ->getQuery()
           ->getSingleScalarResult()
       ;
   }
}
