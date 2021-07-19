<?php


namespace App\Service;

use App\Entity\Etat;
use App\Entity\Sortie;


use Doctrine\ORM\EntityManagerInterface;


class Statutchecker
{

    public function __construct( EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }



    public function statutClotureeSortie(Sortie $sortie)
    {
        // recup list de sorties






        }


        //$sortie->setEtatSortie(2);



        // comparé la date de cloture avec la date du jour now

        //si date depassé alors -> statut cloturee


}