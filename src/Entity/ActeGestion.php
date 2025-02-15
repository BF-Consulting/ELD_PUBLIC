<?php

namespace App\Entity;

use App\Repository\ActeGestionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ActeGestionRepository::class)
 */
class ActeGestion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $gestionnaire;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_add;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $departement;

    /**
     * @ORM\ManyToOne(targetEntity=Fournisseur::class, cascade={"persist"})
     */
    private $commercialisateur;

    /**
     * @ORM\ManyToOne(targetEntity=Employeur::class, cascade={"persist"})
     */
    private $employeur;

    /**
     * @ORM\ManyToOne(targetEntity=TypePopulation::class, cascade={"persist"})
     */
    private $type_population;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGestionnaire(): ?User
    {
        return $this->gestionnaire;
    }

    public function setGestionnaire(?User $gestionnaire): self
    {
        $this->gestionnaire = $gestionnaire;

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
    
    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(?string $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getCommercialisateur(): ?Fournisseur
    {
        return $this->commercialisateur;
    }

    public function setCommercialisateur(?Fournisseur $commercialisateur): self
    {
        $this->commercialisateur = $commercialisateur;

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

    public function getTypePopulation(): ?TypePopulation
    {
        return $this->type_population;
    }

    public function setTypePopulation(?TypePopulation $type_population): self
    {
        $this->type_population = $type_population;

        return $this;
    }
}
