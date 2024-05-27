<?php

namespace App\Repository;

use App\Entity\SpamBlockedIP;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SpamBlockedIP>
 *
 * @method SpamBlockedIP|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpamBlockedIP|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpamBlockedIP[]    findAll()
 * @method SpamBlockedIP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpamBlockedIPRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpamBlockedIP::class);
    }

    public function save(SpamBlockedIP $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SpamBlockedIP $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SpamBlockedIP[] Returns an array of SpamBlockedIP objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SpamBlockedIP
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
