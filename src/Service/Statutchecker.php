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



    public function statutClotureeSortie(array $sorties, EntityManagerInterface $entityManager)
    {

        for ($i = 0; $i <= count($sorties) - 1; $i++) {
            // recup list de sorties
            $s = $sorties[$i];


            $now = time();
            $dateLimitCloture = $s->getDateLimiteInscription()->getTimestamp();

            // comparé la date de cloture avec la date du jour now
            if ($now > $dateLimitCloture && $s->getEtatSortie()->getid() != 3) {

                //si date depassé alors -> statut cloturee
                $s->setEtatSortie($this->entityManager->getRepository(Etat::class)->findOneById(3));

                $entityManager->persist($s);
                $entityManager->flush();
            }

        }

    }
}