<?php

namespace App\Data;

use Doctrine\ORM\Mapping as ORM;

class SearchData
{
    /**
     * @var int
     */
    public $page = 1;

    /**
     * @var string
     */
    public $q  = '';

    /**
     * @var array
     */
    public $campus = [];

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



}
