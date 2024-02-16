<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Entity\IngredientQuantity;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    )
    {

    }

    public function load(ObjectManager $manager): void
    {
        $ingredients = [];
        $ingredient = new Ingredient();
        $ingredient->setTitle('poivre')
                    ->setUnit('pincées')
                    ;
        $ingredients[] = $ingredient;
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setTitle('patates')
                    ->setUnit('kg')
                    ;
        $ingredients[] = $ingredient;
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setTitle('rôti')
                    ->setUnit('gr')
                    ;
        $ingredients[] = $ingredient;
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setTitle('saumon')
                    ->setUnit('gr')
                    ;
        $ingredients[] = $ingredient;
        $manager->persist($ingredient);

        for ($i=0; $i < 10; $i++) 
        { 
            $recipe = new Recipe;
            $recipe->setTitle('recette n°'.$i)
                    ->setDescription('Description n°'.$i)
                    ;
            $ingredientQuantity = new IngredientQuantity;
            $ingredientQuantity->setIngredient($ingredients[random_int(0, count($ingredients) - 1)])
                                ->setQuantity(random_int(0, 200))
                                ;
            $recipe->addIngredientsQuantity($ingredientQuantity);

            
            $ingredientQuantity = new IngredientQuantity;
            $ingredientQuantity->setIngredient($ingredients[random_int(0, count($ingredients) - 1)])
                                ->setQuantity(random_int(0, 200))
                                ;
            $recipe->addIngredientsQuantity($ingredientQuantity);

            
            $ingredientQuantity = new IngredientQuantity;
            $ingredientQuantity->setIngredient($ingredients[random_int(0, count($ingredients) - 1)])
                                ->setQuantity(random_int(0, 200))
                                ;
            $recipe->addIngredientsQuantity($ingredientQuantity);

            $manager->persist($recipe);
        }


        $user = new User;
        $user->setUsername('user')
            ->setPassword(
                $this->hasher->hashPassword($user, 'password')
            );
        $manager->persist($user);

        $manager->flush();
    }
}
