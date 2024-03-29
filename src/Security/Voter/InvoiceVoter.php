<?php

namespace App\Security\Voter;

use App\Entity\Invoice;
use App\Enum\InvoiceStatusEnum;
use App\Service\CompanySession;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class InvoiceVoter extends Voter
{
    public const SEE = 'see';
    public const ADD = 'add';
    public const EDIT = 'edit';
    public const MAIL = 'mail';
    public const DELETE = 'delete';

    private CompanySession $companySession;

    public function __construct(CompanySession $companySession, Security $security)
    {
        $this->companySession = $companySession;
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if($attribute === self::ADD && $subject === null) {
            return true;
        }

        return in_array($attribute, [self::SEE, self::EDIT, self::MAIL, self::DELETE])
            && $subject instanceof \App\Entity\Invoice;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::SEE => $this->canSee($subject, $user),
            self::ADD => $this->canAdd($subject, $user),
            self::EDIT => $this->canEdit($subject, $user),
            self::MAIL => $this->canMail($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            default => false,
        };
    }

    private function canSee(Invoice $invoice, UserInterface $user): bool
    {
        $currentCompany = $this->companySession->getCurrentCompanyWithoutRedirect();
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if(!$currentCompany || $currentCompany !== $invoice->getCompany() || !$currentCompany->userInCompany($user)) {
            return false;
        }

        return true;
    }

    private function canAdd(?Invoice $invoice, UserInterface $user): bool
    {
        $currentCompany = $this->companySession->getCurrentCompanyWithoutRedirect();
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        
        if(!$currentCompany || !$currentCompany->userInCompany($user)) {
            return false;
        }

        return true;
    }

    private function canEdit(Invoice $invoice, UserInterface $user): bool
    {
        $currentCompany = $this->companySession->getCurrentCompanyWithoutRedirect();
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if(!$currentCompany || $currentCompany !== $invoice->getCompany() || !$currentCompany->userInCompany($user)) {
            return false;
        }

        return $invoice->getStatus() === InvoiceStatusEnum::DRAFT;
    }

    private function canMail(Invoice $invoice, UserInterface $user): bool
    {
        $currentCompany = $this->companySession->getCurrentCompanyWithoutRedirect();
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if(!$currentCompany || $currentCompany !== $invoice->getCompany() || !$currentCompany->userInCompany($user)) {
            return false;
        }

        return $invoice->getStatus() === InvoiceStatusEnum::VALIDATED || $invoice->getStatus() === InvoiceStatusEnum::SENT;
    }

    private function canDelete(Invoice $invoice, UserInterface $user): bool
    {
        $currentCompany = $this->companySession->getCurrentCompanyWithoutRedirect();
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if(!$currentCompany || $currentCompany !== $invoice->getCompany() || !$currentCompany->userInCompany($user)) {
            return false;
        }

        return $invoice->getStatus() === InvoiceStatusEnum::DRAFT;
    }

}
