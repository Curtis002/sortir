<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Data\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findSearch(SearchData $search)
    {
        $queryBuilder = $this
            ->createQueryBuilder('s')
            ->select('c','s')
            ->join('s.campus', 'c');

        if(!empty($search->q)) {
            $queryBuilder = $queryBuilder
                ->andWhere('s.nom LIKE :q')
                ->setParameter('q',"%{$search->q}%");
        }

       /* if(!empty($search->dateDebut)) {
            $queryBuilder = $queryBuilder
                ->andWhere('dateDebut >= ')
                ->__ A COMPLETER __ ;

        } ==> A COMPLETER POUR RECHERCHER PAR DATE
       */

        if(!empty($search->organisateur)) {
            $queryBuilder = $queryBuilder
                ->andWhere('s.organisateur = 1')
                ->setParameter('q',"%{$search->q}%");
        }

        if(!empty($search->inscrit)) {
            $queryBuilder = $queryBuilder
                ->andWhere('s.in = 1')
                ->setParameter('q',"%{$search->q}%");
        }

        if(!empty($search->notInscrit)) {
            $queryBuilder = $queryBuilder
                ->andWhere('s.organisateur = 1')
                ->setParameter('q',"%{$search->q}%");
        }

        if(!empty($search->terminees)) {
            $queryBuilder = $queryBuilder
                ->andWhere('s.organisateur = 1')
                ->setParameter('q',"%{$search->q}%");
        }

        if(!empty($search->campus)) {
            $queryBuilder = $queryBuilder
                ->andWhere('c.id IN (:campus)')
                ->setParameter('campus',$search->campus);
        }

        return $queryBuilder->getQuery();
    }
}
