<?php
namespace App;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;


class Utils 
{
    

    public static function tokenToUserId ($token )
    {
        $tokenData = null;

        try{
            $tokenData = JWT::decode($token,  new Key ( 'secret_key', 'HS256'));
        }
        catch (\Exception $e){
            // throw new CustomUserMessageAuthenticationException('Invalid API token.');
            return new JsonResponse('Invalid API token.');
        }
        // dd($tokenData);
        $userId = $tokenData->data->user_id;
        // $user = $userRepository->find($userId);
        return $userId;
    }

}
