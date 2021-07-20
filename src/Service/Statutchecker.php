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


    public function statutActivitéeEnCoursSortie(array $sorties, EntityManagerInterface $entityManager)
    {

        for ($i = 0; $i <= count($sorties) - 1; $i++) {
            // recup list de sorties
            $s = $sorties[$i];

            date_default_timezone_set("Europe/Paris");
            $now = time();
            //var_dump($now);
            $dateDebut = $s->getDateHeureDebut()->getTimeStamp();
            //var_dump($dateDebut);
            //var_dump(date("Y m d H i s",$dateDebut));
            $dateFin = $dateDebut + $s->getDuree()*60;
            //var_dump($dateFin);

            // comparé la date du jour av date de debut d activité
            if ($now >= $dateDebut && $now <= $dateFin && $s->getEtatSortie()->getid() == 3) {

                //si date depassé alors -> statut cloturee
                $s->setEtatSortie($this->entityManager->getRepository(Etat::class)->findOneById(4));

                $entityManager->persist($s);

                $entityManager->flush();
            }

        }

    }
}