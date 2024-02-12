<?php

namespace App\Security\Voter;

use App\Entity\User\LegalEntity;
use App\Entity\User\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class LegalEntityVoter extends Voter
{
    public function __construct(
        private Security $security
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        $supportsAttribute = in_array($attribute, [LegalEntity::LEGAL_ENTITY_READ]);
        $supportsSubject = $subject instanceof LegalEntity;

        return $supportsAttribute && $supportsSubject;
    }

    /**
     * @param string $attribute
     * @param LegalEntity $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if ($token->getUser() === null) {
            return false;
        }


        if ($this->security->isGranted(User::ROLE_ADMIN)) {
            return true;
        }

        switch ($attribute) {
            case LegalEntity::LEGAL_ENTITY_READ:

                if ($token->getUser()->legalEntity === $subject) {
                    return true;
                }
                break;
        }

        return false;
    }
}