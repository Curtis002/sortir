<?php

namespace App\Repository;

use App\Entity\Participant;
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

//    public function getParticipantsSortie($id)
//    {
//        return $this->createQueryBuilder('s')
//            ->select('p', 's')
//            ->innerJoin('s.participants', 'p')
//            ->andWhere('s.id = :id')
//            ->setParameter('id', $id)
//            ->getQuery()
//            ->getResult();
//    }

     /**
     * @return array
     */
    public function findSearch(SearchData $search, Participant $participant): array
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
                ->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $participant->getId());
        }

//        if (!empty($search->inscrit)) {
//            $queryBuilder = $queryBuilder
//                ->andWhere('s.participants = :inscrit')
//                ->setParameter('inscrit', $participant->getInscritSortie());
//        }

//        if (!empty($search->notInscrit)) {
//            $queryBuilder = $queryBuilder
//                ->andWhere('s.notInscrit = :notInscrit')
//                ->setParameter('notInscrit', $search->notInscrit);
//        }
//
//        if (!empty($search->terminees)) {
//            $queryBuilder = $queryBuilder
//                ->andWhere('s.terminees > :terminees')
//                ->setParameter('terminees', $search->dateFin);
//        }

        return $queryBuilder->getQuery()->getResult();
    }
}
