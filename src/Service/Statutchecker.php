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



    public function statutSetteurStatut(array $sorties, EntityManagerInterface $entityManager)
    {

        for ($i = 0; $i <= count($sorties) - 1; $i++) {
            // recup list de sorties
            $s = $sorties[$i];


            $now = time();
            //var_dump($now);
            $dateLimitCloture = $s->getDateLimiteInscription()->getTimestamp();

            $dateDebut = $s->getDateHeureDebut()->getTimeStamp();
            //var_dump($dateDebut);
            //var_dump(date("Y m d H i s",$dateDebut));
            $dateFin = $dateDebut + $s->getDuree()*60;
            //var_dump($dateFin);
            $dateArchivage = $dateFin+2628000;
            //var_dump(date("Y m d H i s",$dateArchivage));

            // 2 => 3 comparé la date de cloture avec la date du jour now
            if ($now >= $dateLimitCloture && $now <= $dateDebut ) {
                //si date depassé alors -> statut cloturee
                $s->setEtatSortie($this->entityManager->getRepository(Etat::class)->findOneById(3));

                $entityManager->persist($s);
                $entityManager->flush();
            }


            // 3 => 4 comparé la date du jour av date de debut d activité
            elseif ($now >= $dateDebut && $now <= $dateFin ) {

                $s->setEtatSortie($this->entityManager->getRepository(Etat::class)->findOneById(4));

                $entityManager->persist($s);
                $entityManager->flush();
            }
            // 4 => 5 si date fin depassé ou egal alors set a activité terminé
            elseif ( $now >= $dateFin && $now <= $dateArchivage  ) {

                $s->setEtatSortie($this->entityManager->getRepository(Etat::class)->findOneById(5));

                $entityManager->persist($s);
                $entityManager->flush();
            }
            // 5,6 => 7 1 mois apres date de fin prevue
            elseif ( $now >= $dateArchivage ) {

                $s->setEtatSortie($this->entityManager->getRepository(Etat::class)->findOneById(7));

                $entityManager->persist($s);
                $entityManager->flush();
            }

        }

    }
}