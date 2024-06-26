<?php

namespace App\Security;

use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {   
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request): ?bool
    {
        // dd ( 'Im here ' );
        // return true;
        return $request->headers->has('x-api-token');
    }

    public function authenticate(Request $request): Passport
    {
        // dd ('auth');
        $apiToken = $request->headers->get('x-api-token');

        if (!$apiToken)
        {
            throw new CustomUserMessageAuthenticationException('No API token provided.');
        }

        $tokenData = null;
        try{
            $tokenData = JWT::decode($apiToken,  new Key ( 'secret_key', 'HS256'));
        }
        catch (\Exception $e){
            throw new CustomUserMessageAuthenticationException('Invalid API token.');
        }

        // try to count expiration date 
        // $expirationTime = $tokenData->exp;        
        // time() > $expirationTime ? throw new CustomUserMessageAuthenticationException('API token has expired.') : null;
    
        $userId = $tokenData->data->user_id;
        

        return new SelfValidatingPassport(
            new UserBadge($userId, function($userId) {
                $user = $this->userRepository->find($userId);
                // $user = $this->userRepository->findByApiToken($apiToken);

                // dd($user);
                // $user->setRoles(['ROLE_USER']);
                // dd($user);
                if (!$user)
                {
                    throw new UserNotFoundException();
                }
                // if (empty($user->getRoles())) {
                //     $user->setRoles(['ROLE_USER']);
                // }
                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // TODO: Implement onAuthenticationSuccess() method.
        return null;
        dd ('UPI');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // return null;
        $data = [
            'message' =>  strtr($exception->getMessageKey() , $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
        
    }

    //    public function start(Request $request, AuthenticationException $authException = null): Response
    //    {
    //        /*
    //         * If you would like this class to control what happens when an anonymous user accesses a
    //         * protected page (e.g. redirect to /login), uncomment this method and make this class
    //         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
    //         *
    //         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
    //         */
    //    }
}
