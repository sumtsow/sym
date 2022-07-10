<?php

namespace App\Repository;

use App\Entity\Device;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Device>
 *
 * @method Device|null find($id, $lockMode = null, $lockVersion = null)
 * @method Device|null findOneBy(array $criteria, array $orderBy = null)
 * @method Device[]    findAll()
 * @method Device[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Device::class);
    }

    public function add(Device $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Device $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Device[] Returns an array of Device objects
     */
    public function findById(array $ids): array
    {
      return $ids ? $this->createQueryBuilder('d')
        ->andWhere('d.id IN (:val)')
        ->setParameter('val', $ids, Connection::PARAM_STR_ARRAY)
        ->orderBy('d.id', 'ASC')
        ->getQuery()
        ->getResult() : [];
    }

//    public function findOneBySomeField($value): ?Device
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function getParameterList($ids): array
    {
      $parameterList = $deviceParams = [];
      if (!$ids) return $parameterList;
      foreach ($ids as $id) {
        $device = $this->find($id);
        $params = $device->getParameters();
        foreach ($params as $param) {
          $parameterList[] = $param->getAvParameter()->getId();
        }
      }
      return array_unique($parameterList);
    }
}
