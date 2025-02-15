<?php

namespace App\Entity;

use App\Repository\ResultatRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ResultatRepository::class)
 */
class Resultat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nni;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_add;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, cascade={"persist"})
     */
    private $admin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $matricule;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $employeur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dernier_employeur;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $debut_periode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $fin_periode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $montant;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $taxe;

    /**
     * @ORM\ManyToOne(targetEntity=Population::class, cascade={"persist"})
     */
    private $pensionner;

    /**
     * @ORM\ManyToOne(targetEntity=ActeGestion::class, cascade={"persist"})
     */
    private $acte_gestion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNni(): ?string
    {
        return $this->nni;
    }

    public function setNni(string $nni): self
    {
        $this->nni = $nni;

        return $this;
    }

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->date_add;
    }

    public function setDateAdd(\DateTimeInterface $date_add): self
    {
        $this->date_add = $date_add;

        return $this;
    }

    public function getAdmin(): ?User
    {
        return $this->admin;
    }

    public function setAdmin(?User $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(?string $matricule): self
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmployeur(): ?string
    {
        return $this->employeur;
    }

    public function setEmployeur(?string $employeur): self
    {
        $this->employeur = $employeur;

        return $this;
    }

    public function getDernierEmployeur(): ?string
    {
        return $this->dernier_employeur;
    }

    public function setDernierEmployeur(?string $dernier_employeur): self
    {
        $this->dernier_employeur = $dernier_employeur;

        return $this;
    }

    public function getDebutPeriode(): ?string
    {
        return $this->debut_periode;
    }

    public function setDebutPeriode(?string $debut_periode): self
    {
        $this->debut_periode = $debut_periode;

        return $this;
    }

    public function getFinPeriode(): ?string
    {
        return $this->fin_periode;
    }

    public function setFinPeriode(?string $fin_periode): self
    {
        $this->fin_periode = $fin_periode;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(?string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getTaxe(): ?string
    {
        return $this->taxe;
    }

    public function setTaxe(?string $taxe): self
    {
        $this->taxe = $taxe;

        return $this;
    }

    public function getPensionner(): ?Population
    {
        return $this->pensionner;
    }

    public function setPensionner(?Population $pensionner): self
    {
        $this->pensionner = $pensionner;

        return $this;
    }

    public function getActeGestion(): ?ActeGestion
    {
        return $this->acte_gestion;
    }

    public function setActeGestion(?ActeGestion $acte_gestion): self
    {
        $this->acte_gestion = $acte_gestion;

        return $this;
    }
}
