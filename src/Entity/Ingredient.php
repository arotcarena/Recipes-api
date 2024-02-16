<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[Assert\NotBlank(message: 'title ne doit pas être vide')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'title ne doit pas dépasser 100 caractères'
    )]
    #[ORM\Column(length: 255)]
    public ?string $title = null;

    #[Assert\NotBlank(message: 'unit ne doit pas être vide')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'unit ne doit pas dépasser 100 caractères'
    )]
    #[ORM\Column(length: 255)]
    public ?string $unit = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

}
