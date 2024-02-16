<?php
namespace App\Controller;

use App\Helper;
use App\Entity\Ingredient;
use App\Entity\IngredientQuantity;
use App\Entity\Recipe;
use App\RecipeConvertor;
use App\Repository\IngredientQuantityRepository;
use App\Repository\IngredientRepository;
use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Error;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RecipesController extends AbstractController 
{
    public function __construct(
        private EntityManagerInterface $em,
        private RecipeRepository $repository,
        private ValidatorInterface $validator,
        private RecipeConvertor $recipeConvertor,
        private IngredientQuantityRepository $ingredientQuantityRepository,
        private IngredientRepository $ingredientRepository
    )
    {
        
    }

    #[Route('/recipes', name: 'recipes_index', methods: ['GET'])]
    public function index(): Response
    {
        $recipes = $this->repository->findAll();
        $lightRecipes = [];
        foreach($recipes as $recipe)
        {
            $lightRecipes[] = [
                'id' => $recipe->getId(),
                'title' => $recipe->getTitle()
            ];
        }
        return Helper::createApiResponse($lightRecipes, 200);
    }

    #[Route('/recipes/{id}', name: 'recipes_show', methods: ['GET'])]
    public function show(Recipe $recipe): Response 
    {
        return Helper::createApiResponse($this->recipeConvertor->convert($recipe), 200);
    }

    #[Route('/recipes', name: 'recipes_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $data = json_decode($request->getContent());
        $recipe = new Recipe;
        $recipe->setTitle($data->title)
                    ->setDescription($data->description)
                    ;
        
        $errors = $this->validator->validate($recipe);
        foreach($data->iq as $iq) 
        {
            $ingredient = $this->ingredientRepository->find($iq->ingredientId);
            $ingredientQuantity = new IngredientQuantity;
            $ingredientQuantity->setIngredient($ingredient)
                                ->setQuantity($iq->quantity)
                                ;
            $iqErrors = $this->validator->validate($ingredientQuantity);
            if(count($iqErrors) !== 0)
            {
                $errors[$ingredient->getTitle()] = $iqErrors;
            }
            else 
            {
                $recipe->addIngredientsQuantity($ingredientQuantity);
            }
        }
        if(count($errors) === 0)
        {
            $this->em->persist($recipe);
            $this->em->flush();
            $lightRecipe = [
                'id' => $recipe->getId(),
                'title' => $recipe->getTitle()
            ];
            return Helper::createApiResponse($lightRecipe, 200);
        }
        return Helper::createApiResponse('', 400, $errors);
    }

    #[Route('/recipes/delete/{id}', name: 'recipes_delete', methods: ['POST'])]
    public function delete(int $id)
    {
        $recipe = $this->repository->find($id);
        if(!$recipe)
        {
            return Helper::createApiResponse('', 400, [new Error('suppression d\'une recette : aucune recette ne possède cet id')]);
        }
        $title = $recipe->getTitle();
        $this->em->remove($recipe);
        $this->em->flush();
        return Helper::createApiResponse('La recette "'.$title.'" a bien été supprimée !', 200);
    }

    #[Route('/recipes/update/{id}', name: 'recipes_update', methods: ['POST'])]
    public function update(int $id, Request $request): Response
    {
        $recipe = $this->repository->find($id);
        if(!$recipe)
        {
            return Helper::createApiResponse('', 400, [new Error('update d\'une recette : aucune recette ne possède cet id')]);
        }
        
        $data = json_decode($request->getContent());
        
        $recipe->setTitle($data->title)
                    ->setDescription($data->description)
                    ;
        
        $errors = $this->validator->validate($recipe);
        if(count($errors) === 0)
        {
            $this->em->flush();
            return Helper::createApiResponse($this->recipeConvertor->convert($recipe), 200);
        }
        return Helper::createApiResponse('', 400, $errors);
    }

    #[Route('/recipes/ingredientQuantity/delete/{id}', name: 'recipes_deleteIngredientQuantity', methods: ['POST'])]
    public function deleteIngredientQuantity(int $id): Response 
    {
        $ingredientQuantity = $this->ingredientQuantityRepository->find($id);
        if(!$ingredientQuantity)
        {
            return Helper::createApiResponse('', 400, [new Error('suppression d\'un ingrédient d\'une recette : aucun ingrédient d\'une recette ne possède cet id')]);
        }
        $recipe = $ingredientQuantity->getRecipe();
        $this->em->remove($ingredientQuantity);
        $this->em->flush();
        return Helper::createApiResponse($this->recipeConvertor->convert($recipe), 200);
    }

    #[Route('/recipes/{recipe_id}/addIngredientQuantity', name: 'recipes_addIngredientQuantity', methods: ['POST'])]
    public function addIngredientQuantity(int $recipe_id, Request $request): Response 
    {
        $recipe = $this->repository->find($recipe_id);
        $data = json_decode($request->getContent());

        $ingredient = $this->ingredientRepository->find($data->ingredientId);

        $existingIngredientQuantity = null;
        foreach($recipe->getIngredientsQuantity() as $ingredientQuantity)
        {
            if($ingredientQuantity->getIngredient() === $ingredient) {
                $existingIngredientQuantity = $ingredientQuantity;
            }
        }
        
        if($existingIngredientQuantity)
        {
            $existingIngredientQuantity->setQuantity((int)$data->quantity + $existingIngredientQuantity->getQuantity());
            $ingredientQuantity = $existingIngredientQuantity;
        }
        else
        {
            $ingredientQuantity = new IngredientQuantity;
            $ingredientQuantity->setQuantity((int)$data->quantity)
                                ->setIngredient($ingredient)
                                ;
        }
        
        $errors = $this->validator->validate($ingredientQuantity);
        if(count($errors) === 0)
        {
            $recipe->addIngredientsQuantity($ingredientQuantity);
            $this->em->flush();
            return Helper::createApiResponse($this->recipeConvertor->convert($recipe), 200);
        }
        return Helper::createApiResponse('', 400, $errors);
    }

    #[Route('/recipes/updateIngredientQuantity/{id}', name: 'recipes_updateIngredientQuantity', methods: ['POST'])]
    public function updateIngredientQuantity(int $id, Request $request): Response 
    {
        $ingredientQuantity = $this->ingredientQuantityRepository->find($id);
        $data = json_decode($request->getContent());

        $ingredientQuantity->setQuantity((int)$data->quantity);

        $errors = $this->validator->validate($ingredientQuantity);
        if(count($errors) === 0)
        {
            $this->em->flush();
            return Helper::createApiResponse($this->recipeConvertor->convert($ingredientQuantity->getRecipe()), 200);
        }
        return Helper::createApiResponse('', 400, $errors);
    }

    
}