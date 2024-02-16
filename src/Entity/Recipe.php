<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[Assert\NotBlank(message: 'title ne peut pas être vide')]
    #[ORM\Column(length: 255)]
    public ?string $title = null;

    #[Assert\NotBlank(message: 'description ne peut pas être vide')]
    #[ORM\Column(type: Types::TEXT)]
    public ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: IngredientQuantity::class, cascade: ['persist', 'remove'])]
    public Collection|null $ingredientsQuantity;


    public function __construct()
    {
        $this->ingredientsQuantity = new ArrayCollection();
    }

    public function resetIngredientsQuantity():void 
    {
        $this->ingredientsQuantity = new ArrayCollection();
    }

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

   

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, IngredientQuantity>
     */
    public function getIngredientsQuantity(): Collection
    {
        return $this->ingredientsQuantity;
    }

    public function addIngredientsQuantity(IngredientQuantity $ingredientQuantity): self
    {
        if (!$this->ingredientsQuantity->contains($ingredientQuantity)) {
            $this->ingredientsQuantity->add($ingredientQuantity);
            $ingredientQuantity->setRecipe($this);
        }

        return $this;
    }

    public function removeIngredientQuantity(IngredientQuantity $ingredientsQuantity): self
    {
        if ($this->ingredientsQuantity->removeElement($ingredientsQuantity)) {
            // set the owning side to null (unless already changed)
            if ($ingredientsQuantity->getRecipe() === $this) {
                $ingredientsQuantity->setRecipe(null);
            }
        }

        return $this;
    }
}
