<?php

namespace App\Security\Voter;

use App\Entity\Category;
use App\Service\CompanySession;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ReferentCurrentCompanyVoter extends Voter
{
    private CompanySession $companySession;

    public function __construct(CompanySession $companySession)
    {
        $this->companySession = $companySession;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === 'company_referent';
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            default => $this->canAccess($user),
        };
    }

    private function canAccess(UserInterface $user): bool
    {
        $currentCompany = $this->companySession->getCurrentCompanyWithoutRedirect();
        if(!$currentCompany && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return false;
        }
        
        return in_array('ROLE_ADMIN', $user->getRoles()) || $user === $currentCompany->getReferent();
    }
}
