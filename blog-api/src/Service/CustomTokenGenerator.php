<?php
namespace App\Service;

use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class CustomTokenGenerator implements TokenGeneratorInterface
{
    public function generateToken(): string
    {

        return bin2hex(random_bytes(32)); 

    }
}
