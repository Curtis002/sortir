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

    /**
     * @return array
     */
    public function findSearch(SearchData $search): array
    {
        $queryBuilder = $this
            //récupère les sorties
            ->createQueryBuilder('s')
            //sélectionne toutes les infos liées aux sorties et aux campus
            ->select('c', 's')
            //liaison campus / sorties
            ->join('s.campus', 'c');

        //recherche nom de sortie contient
        if (!empty($search->q)) {
            $queryBuilder = $queryBuilder
                ->andWhere('s.nom LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        //recherche par campus
        if (!empty($search->campus)) {
            $queryBuilder = $queryBuilder
                ->andWhere('c.id IN (:campus)')
                ->setParameter('campus', $search->campus);
        }

        //Recherche par date
        if (!empty($search->dateDebut && $search->dateFin)) {
            //A COMPLETER / QUELLES CONDITIONS SUR RECHERCHE PAR DATE ?
        }


        //recherche checkbox
        if (!empty($search->organisateur)) {
            $queryBuilder = $queryBuilder
                ->andWhere('s.organisateur = 1');
        }

        if (!empty($search->inscrit)) {
            $queryBuilder = $queryBuilder
                ->andWhere('s.inscrit = 1');
        }

        if (!empty($search->notInscrit)) {
            $queryBuilder = $queryBuilder
                ->andWhere('s.notInscrit = 1');
                        }

        if (!empty($search->terminees)) {
            $queryBuilder = $queryBuilder
                ->andWhere('s.terminees = 1');
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
