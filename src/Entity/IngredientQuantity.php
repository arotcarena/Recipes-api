<?php

namespace App\Entity;

use App\Repository\IngredientQuantityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: IngredientQuantityRepository::class)]
class IngredientQuantity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    public ?Ingredient $ingredient = null;

    #[Assert\Positive(message: 'quantity ne peut pas être négatif ou égal à zéro')]
    #[ORM\Column]
    public ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'ingredientsQuantity')]
    public ?Recipe $recipe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): self
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }
}
