<?php

namespace App\Repository;

use App\Entity\AvParameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AvParameter>
 *
 * @method AvParameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method AvParameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method AvParameter[]    findAll()
 * @method AvParameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvParameterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AvParameter::class);
    }

    public function add(AvParameter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AvParameter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Parameter[] Returns an array of Parameter objects
     */
    public function findById(array $ids): array
    {
      return $ids ? $this->createQueryBuilder('p')
        ->andWhere('p.id IN (:val)')
        ->setParameter('val', $ids, Connection::PARAM_STR_ARRAY)
        ->orderBy('p.id', 'ASC')
        ->getQuery()
        ->getResult() : [];
    }

//    public function findOneBySomeField($value): ?AvParameter
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
