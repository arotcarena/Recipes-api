<?php
namespace App\Controller;

use App\Helper;
use App\Repository\UserRepository;
use Error;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $hasher,
        private Security $security
    )
    {

    }
    #[Route('/login', name: 'security_login')]
    public function login(Request $request)
    {
        $data = json_decode($request->getContent());
        $user = $this->userRepository->findOneBy(['username' => $data->username]);
        if($user && $this->hasher->isPasswordValid($user, $data->password))
        {
            $options = [
                'expires' => time() + 60*60*24*7, 
                'secure' => true,
                'samesite' => 'None'
            ]; 
            $token = $user->getId() . '==' . $user->getPassword();
            setcookie('sessid', $token, $options);
            return Helper::createApiResponse([
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'password' => $user->getPassword()
            ], 200);
        }
        return Helper::createApiResponse('', 400, [new Error('Identifiants invalides')]);
    }


    #[Route('/me', name: 'security_me')]
    public function me():Response
    {
        if(isset($_COOKIE['sessid']))
        {
            if(str_contains($_COOKIE['sessid'], '=='))
            {
                $id = explode('==', $_COOKIE['sessid'])[0];
                $password = explode('==', $_COOKIE['sessid'])[1];
                $user = $this->userRepository->find($id);
                if($user && $password === $user->getPassword())
                {
                    return Helper::createApiResponse([
                        'id' => $user->getId(),
                        'username' => $user->getUsername(),
                        'password' => $user->getPassword()
                    ], 200);
                }
            }
        }
        return Helper::createApiResponse(false, 200);
    }


}