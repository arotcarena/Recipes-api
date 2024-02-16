<?php
namespace App;

use App\Entity\Recipe;



class RecipeConvertor
{
    /**
     * Undocumented function
     *
     * @param Recipe[]|Recipe $recipeOrRecipes
     * @return array
     */
    public function convert($recipeOrRecipes): array
    {
        if(is_array($recipeOrRecipes))
        {
            $recipesArray = [];
            foreach($recipeOrRecipes as $recipe)
            {
                $recipesArray[] = $this->convertRecipe($recipe);
            }
            return $recipesArray;
        }
        else 
        {
            return $this->convertRecipe($recipeOrRecipes);
        }
        
    }

    private function convertRecipe(Recipe $recipe): array
    {
        $ingredientsQuantityArray = [];
        foreach($recipe->getIngredientsQuantity() as $ingredientQuantity)
        {
            $ingredientsQuantityArray[] = [
                'id' => $ingredientQuantity->getId(),
                'ingredient' => $ingredientQuantity->getIngredient(),
                'quantity' => $ingredientQuantity->getQuantity()
            ];
        }

        return [
            'id' => $recipe->getId(),
            'title' => $recipe->getTitle(),
            'ingredientsQuantity' => $ingredientsQuantityArray,
            'description' => $recipe->getDescription()
        ];
    }
}