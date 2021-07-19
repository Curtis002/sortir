<?php


namespace App\Service;

use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;

class Statutchecker
{





    public function statutSortie(EntityManagerInterface $entityManager)
    {
        // recup list de sorties
        $sortierepo = $entityManager->getRepository(Sortie::class);
        $etatrepo = $entityManager->getRepository(Etat::class);
        $etat = $etatrepo->findAll();
        var_dump($etat);

        $sorties = $sortierepo->findAll();
        var_dump($etat);


        // comparé la date de cloture avec la date du jour now

        //si date depassé alors -> statut cloturee

    }
}