<?php

namespace App\Data;

use Doctrine\ORM\Mapping as ORM;

class SearchData
{
//    /**
//     * @var int
//     */
//    public $page = 1;

    /**
     * @var string
     */
    public $q  = '';

    /**
     * @var
     */
    public $campus ;

    /**
     * @ORM\Column(type="string")
     */
    public $dateDebut ;

    /**
     * @ORM\Column(type="string")
     */
    public $dateFin ;

    /**
     * @var boolean
     */
    public $organisateur = false;

    /**
     * @var boolean
     */
    public $inscrit = false;

    /**
     * @var boolean
     */
    public $notInscrit = false;

    /**
     * @var boolean
     */
    public $terminees = false;

    /**
     * @return string
     */
    public function getQ(): string
    {
        return $this->q;
    }

    /**
     * @return mixed
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @return mixed
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * @return mixed
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * @return bool
     */
    public function isOrganisateur(): bool
    {
        return $this->organisateur;
    }

    /**
     * @return bool
     */
    public function isInscrit(): bool
    {
        return $this->inscrit;
    }

    /**
     * @return bool
     */
    public function isNotInscrit(): bool
    {
        return $this->notInscrit;
    }

    /**
     * @return bool
     */
    public function isTerminees(): bool
    {
        return $this->terminees;
    }



}
