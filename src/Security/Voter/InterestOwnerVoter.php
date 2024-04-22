<?php

namespace App\Security\Voter;

use App\Entity\Interest;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;


class InterestOwnerVoter extends Voter
{
 

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === 'INTEREST_OWNER' && $subject instanceof Interest;
    }

    protected function voteOnAttribute( string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // dd($id);
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        return $subject->getProfile()->getUser() === $user;
    }
}
