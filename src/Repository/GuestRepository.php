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

    public function guestsCount($rdsvp = null): array
   {
       return $this->createQueryBuilder('g')
           ->select("count(g.rdsvp) as {$rdsvp}")
           ->andWhere('g.rdsvp = :val')
           ->setParameter('val', $rdsvp)
           ->getQuery()
           ->getScalarResult()
       ;
   }

    public function allGuestCount(): array
   {
       return $this->createQueryBuilder('g')
           ->select("count(g) as guestNumber")
           ->getQuery()
           ->getScalarResult()
       ;
   }

   public function dietCount($diet): array
   {
       return $this->createQueryBuilder('g')
           ->select("count(g.diet) as {$diet}")
           ->andWhere('g.diet = :val')
           ->setParameter('val', $diet)
           ->getQuery()
           ->getScalarResult()
       ;
   }
}
