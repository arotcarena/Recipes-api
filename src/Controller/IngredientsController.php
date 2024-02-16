<?php
namespace App\Controller;

use Exception;
use App\Config;
use App\Helper;
use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Error;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class IngredientsController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private IngredientRepository $repository,
        private ValidatorInterface $validator
    )
    {
        
    }

    #[Route('/ingredients', name: 'ingredients_index', methods: ['GET'])]
    public function index(): Response
    {
        $ingredients = $this->repository->findAll();
        return Helper::createApiResponse($ingredients, 200);
    }

    #[Route('/ingredients/delete/{id}', name: 'ingredients_delete', methods: ['POST'])]
    public function delete(int $id)
    {
        $ingredient = $this->repository->find($id);
        if(!$ingredient)
        {
            return Helper::createApiResponse('', 400, [new Error('suppression d\'un ingrédient : aucun ingrédient ne possède cet id')]);
        }
        $title = $ingredient->getTitle();
        $this->em->remove($ingredient);
        $this->em->flush();
        return Helper::createApiResponse('L\'ingrédient "'.$title.'" a bien été supprimé !', 200);
    }

    #[Route('/ingredients/update/{id}', name: 'ingredients_update', methods: ['POST'])]
    public function update(int $id, Request $request): Response
    {
        $ingredient = $this->repository->find($id);
        if(!$ingredient)
        {
            return Helper::createApiResponse('', 400, [new Error('update d\'un ingrédient : aucun ingrédient ne possède cet id')]);
        }
        
        $data = json_decode($request->getContent());
        
        $ingredient->setTitle($data->title)
                    ->setUnit($data->unit)
                    ;
        $errors = $this->validator->validate($ingredient);
        if(count($errors) === 0)
        {
            $this->em->flush();
            return Helper::createApiResponse($ingredient, 200);
        }
        return Helper::createApiResponse('', 400, $errors);
    }

    #[Route('/ingredients', name: 'ingredients_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $data = json_decode($request->getContent());
        $ingredient = new Ingredient;
        $ingredient->setTitle($data->title)
                    ->setUnit($data->unit)
                    ;
        $errors = $this->validator->validate($ingredient);
        if(count($errors) === 0)
        {
            $this->em->persist($ingredient);
            $this->em->flush();
            return Helper::createApiResponse($ingredient, 200);
        }
        return Helper::createApiResponse('', 400, $errors);
    }
}