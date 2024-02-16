<?php 
namespace App;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class Helper
{
    public static function createApiResponse(mixed $content, int $status_code, mixed $errors = null): Response
    {
        if($errors)
        {
            foreach($errors as $error)
            {
                if($errors instanceof ConstraintViolationListInterface)
                {
                    /** @var ConstraintViolation */
                    $error = $error;
                    $data['errors'][$error->getPropertyPath()] = $error->getMessage();
                }
                else
                {
                    $data['errors'][] = $error->getMessage();
                }
            }
        }
        else 
        {
            $data = $content;
        }
        
        return new Response(json_encode($data), $status_code, [
            'Access-Control-Allow-Origin' => Config::CROSS_ORIGIN,
            'Access-Control-Allow-Credentials' => 'true'
        ]);
    }
}