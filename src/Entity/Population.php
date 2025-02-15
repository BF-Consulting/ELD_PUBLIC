<?php

namespace App\Entity;

use App\Repository\PopulationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PopulationRepository::class)
 */
class Population
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TypePopulation::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $type_population;

    /**
     * @ORM\ManyToOne(targetEntity=Employeur::class, cascade={"persist"})
     */
    private $employeur;

    /**
     * @ORM\ManyToOne(targetEntity=Employeur::class, cascade={"persist"})
     */
    private $dernier_employeur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $matricule;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nni;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom_usuel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom_naissance;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $prenom;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $debut_periode_conso;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fin_periode_conso;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $taxe;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $montant;

    /**
     * @ORM\ManyToOne(targetEntity=Fournisseur::class, cascade={"persist"})
     */
    private $commercialisateur_pdl1;

    /**
     * @ORM\ManyToOne(targetEntity=Fournisseur::class, cascade={"persist"})
     */
    private $commercialisateur_pdl2;

    /**
     * @ORM\ManyToOne(targetEntity=Fournisseur::class, cascade={"persist"})
     */
    private $commercialisateur_pdl3;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $departement;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypePopulation(): ?TypePopulation
    {
        return $this->type_population;
    }

    public function setTypePopulation(?TypePopulation $type_population): self
    {
        $this->type_population = $type_population;

        return $this;
    }

    public function getEmployeur(): ?Employeur
    {
        return $this->employeur;
    }

    public function setEmployeur(?Employeur $employeur): self
    {
        $this->employeur = $employeur;

        return $this;
    }

    public function getDernierEmployeur(): ?Employeur
    {
        return $this->dernier_employeur;
    }

    public function setDernierEmployeur(?Employeur $dernier_employeur): self
    {
        $this->dernier_employeur = $dernier_employeur;

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

    public function getNni(): ?string
    {
        return $this->nni;
    }

    public function setNni(?string $nni): self
    {
        $this->nni = $nni;

        return $this;
    }

    public function getNomUsuel(): ?string
    {
        return $this->nom_usuel;
    }

    public function setNomUsuel(?string $nom_usuel): self
    {
        $this->nom_usuel = $nom_usuel;

        return $this;
    }

    public function getNomNaissance(): ?string
    {
        return $this->nom_naissance;
    }

    public function setNomNaissance(?string $nom_naissance): self
    {
        $this->nom_naissance = $nom_naissance;

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

    public function getDebutPeriodeConso(): ?\DateTimeInterface
    {
        return $this->debut_periode_conso;
    }

    public function setDebutPeriodeConso(?\DateTimeInterface $debut_periode_conso): self
    {
        $this->debut_periode_conso = $debut_periode_conso;

        return $this;
    }

    public function getFinPeriodeConso(): ?\DateTimeInterface
    {
        return $this->fin_periode_conso;
    }

    public function setFinPeriodeConso(?\DateTimeInterface $fin_periode_conso): self
    {
        $this->fin_periode_conso = $fin_periode_conso;

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

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(?string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getCommercialisateurPdl1(): ?Fournisseur
    {
        return $this->commercialisateur_pdl1;
    }

    public function setCommercialisateurPdl1(?Fournisseur $commercialisateur_pdl1): self
    {
        $this->commercialisateur_pdl1 = $commercialisateur_pdl1;

        return $this;
    }

    public function getCommercialisateurPdl2(): ?Fournisseur
    {
        return $this->commercialisateur_pdl2;
    }

    public function setCommercialisateurPdl2(?Fournisseur $commercialisateur_pdl2): self
    {
        $this->commercialisateur_pdl2 = $commercialisateur_pdl2;

        return $this;
    }

    public function getCommercialisateurPdl3(): ?Fournisseur
    {
        return $this->commercialisateur_pdl3;
    }

    public function setCommercialisateurPdl3(?Fournisseur $commercialisateur_pdl3): self
    {
        $this->commercialisateur_pdl3 = $commercialisateur_pdl3;

        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(?string $departement): self
    {
        $this->departement = $departement;

        return $this;
    }
}
