<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ParticipantRepository::class)
 */
class Participant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $motPasse;

    /**
     * @ORM\Column(type="boolean")
     */
    private $administrateur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoProfil;

    /**
     * @ORM\ManyToMany(targetEntity=sortie::class, inversedBy="participants")
     */
    private $inscritSortie;

    /**
     * @ORM\OneToMany(targetEntity=sortie::class, mappedBy="organisateur")
     */
    private $organisateurSortie;

    /**
     * @ORM\ManyToOne(targetEntity=campus::class, inversedBy="participants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campus;

    public function __construct()
    {
        $this->inscritSortie = new ArrayCollection();
        $this->organisateurSortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getMotPasse(): ?string
    {
        return $this->motPasse;
    }

    public function setMotPasse(string $motPasse): self
    {
        $this->motPasse = $motPasse;

        return $this;
    }

    public function getAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getPhotoProfil(): ?string
    {
        return $this->photoProfil;
    }

    public function setPhotoProfil(?string $photoProfil): self
    {
        $this->photoProfil = $photoProfil;

        return $this;
    }

    /**
     * @return Collection|sortie[]
     */
    public function getInscritSortie(): Collection
    {
        return $this->inscritSortie;
    }

    public function addInscritSortie(sortie $inscritSortie): self
    {
        if (!$this->inscritSortie->contains($inscritSortie)) {
            $this->inscritSortie[] = $inscritSortie;
        }

        return $this;
    }

    public function removeInscritSortie(sortie $inscritSortie): self
    {
        $this->inscritSortie->removeElement($inscritSortie);

        return $this;
    }

    /**
     * @return Collection|sortie[]
     */
    public function getOrganisateurSortie(): Collection
    {
        return $this->organisateurSortie;
    }

    public function addOrganisateurSortie(sortie $organisateurSortie): self
    {
        if (!$this->organisateurSortie->contains($organisateurSortie)) {
            $this->organisateurSortie[] = $organisateurSortie;
            $organisateurSortie->setOrganisateur($this);
        }

        return $this;
    }

    public function removeOrganisateurSortie(sortie $organisateurSortie): self
    {
        if ($this->organisateurSortie->removeElement($organisateurSortie)) {
            // set the owning side to null (unless already changed)
            if ($organisateurSortie->getOrganisateur() === $this) {
                $organisateurSortie->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function getCampus(): ?campus
    {
        return $this->campus;
    }

    public function setCampus(?campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }
}
