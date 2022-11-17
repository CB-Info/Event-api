<?php

namespace App\Repository;

use App\Entity\Event;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function save(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findWithPagination($page, $limit): array
    {
        $qb = $this->createQueryBuilder('e')
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit);

        //check status on
        return $qb
        ->andWhere('e.status = true')
        ->getQuery()
        ->getResult();
    }

    public function findByRegion($region): array
    {
        $qb = $this->createQueryBuilder('e');

        return $qb
        ->andWhere('e.place', 'p.placeRegion = :region')
        ->setParameter('region', $region)
        ->getQuery()
        ->getResult();
    }

    public function filterDate( DateTimeImmutable $startDate)
    {
        $startDateTime = $startDate ? $startDate : new \DateTimeImmutable();
        // $endDateTime = $endDate ? $endDate : new \DateTimeImmutable();

        $qb = $this->createQueryBuilder('e');
        $qb->add(
            'where',
            // $qb->expr()->orX(
            //     $qb->expr()->andX(
            //         $qb->expr()->gte('e.eventDate', ':startdate'),
            //         $qb->expr()->lte('e.eventDate', ':enddate')
            //     ),
            //     $qb->expr()->andX(
            //         $qb->expr()->gte('e.eventDateEnd', ':startdate'),
            //         $qb->expr()->lte('e.eventDateEnd', ':enddate')
            //     )
            // )

            $qb->expr()->gte('e.eventDate', ':startdate')
        )
        ->setParameters(
            new ArrayCollection(
                [
                    new Parameter('startdate', $startDateTime, Types::DATETIME_IMMUTABLE),
                    // new Parameter('enddate', $endDateTime, Types::DATETIME_IMMUTABLE)
                ]
                )
                );
            return $qb->getQuery()->getResult(); 
    }

    public function findByMonthYear($month, $year)
    {
        $fromTime = new \DateTime($year . '-' . $month . '-01');
        $toTime = new \DateTime($fromTime->format('Y-m-d') . ' first day of next month');
        $qb = $this->createQueryBuilder('e')
            ->where('e.eventDate >= :fromTime')
            ->andWhere('e.eventDate < :toTime')
            ->setParameter('fromTime', $fromTime)
            ->setParameter('toTime', $toTime);
        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Event[] Returns an array of Event objects
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

//    public function findOneBySomeField($value): ?Event
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
