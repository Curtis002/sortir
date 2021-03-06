<?php

namespace App\Repository;

use App\Data\SearchDataAdmin;
use App\Entity\Ville;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method Ville|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ville|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ville[]    findAll()
 * @method Ville[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ville::class);
    }

    public function findSearch3(SearchDataAdmin $search): array
    {
        $query = $this
            ->createQueryBuilder('v');
        if(!empty($search->q))
        {
            $query = $query
                ->andWhere('v.nom LIKE :q')
                ->orWhere('v.codePostal LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

//        if (!empty($search->codePostal)) {
//            $query = $query
//                ->andWhere('v.id IN (:ville)')
//                ->setParameter('ville', $search->codePostal);
//        }

        return $query->getQuery()->getResult();
    }


    // /**
    //  * @return Ville[] Returns an array of Ville objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ville
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}
