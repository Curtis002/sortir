<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ParticipantRepository::class)
 * @UniqueEntity(fields={"pseudo"}, message="There is already an account with this identifiant")
 */
class Participant implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=50)
     */
    private $prenom;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=50)
     */
    private $pseudo;

    /**
     * @Assert\Length(10)
     * @Assert\NotBlank(message="Veuillez remplir votre numero de telephone")
     * @ORM\Column(type="string", length=10)
     */
    private $telephone;

    /**
     * @Assert\Email(message="saisissez un email valide")
     * @ORM\Column(type="string", length=255)
     */
    private $mail;

    /**
     * @Assert\Length(
     *     min=6,
     *     max=255,
     *     minMessage="Minimum 4 characters please!",
     *     maxMessage="Maximum 255 characters please!"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $motPasse;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];


    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoProfil;

    /**
     *
     * @Assert\File(
     *     maxSize = "1024k",
     *     mimeTypes = {"image/png", "image/jpg", "image/jpeg"},
     *     mimeTypesMessage = "l'image doit etre aux formats suivants .jpg, .png, .jpeg"
     * )
     */
    private $photoProfilFile;

    /**
     * @ORM\ManyToMany(targetEntity=Sortie::class, inversedBy="participants")
     */
    private $inscritSortie;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="organisateur")
     */
    private $organisateurSortie;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="participants")
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

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

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

    public function getPhotoProfilFile(): ?string
    {
        return $this->photoProfilFile;
    }

    public function setPhotoProfilFile(string $photoProfilFile): self
    {
        $this->photoProfilFile = $photoProfilFile;

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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->pseudo;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->motPasse;
    }

    public function setPassword(string $motPasse): self
    {
        $this->motPasse = $motPasse;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
