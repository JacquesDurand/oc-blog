<?php

declare(strict_types=1);

namespace App\Errors;

class LoginFormError
{
    /** @var bool  */
    private bool $missingUsername = false;

    /** @var bool  */
    private bool $missingPassword = false;

    /** @var bool  */
    private bool $credentialsIncorrect = false;

    /** @var bool  */
    private bool $awaitingVerification = false;

    /** @var bool  */
    private bool $accountRemoved = false;

    /** @var bool */
    private bool $accountMissing = false;

    /**
     * @return bool
     */
    public function areCredentialsIncorrect(): bool
    {
        return $this->credentialsIncorrect;
    }

    /**
     * @param bool $credentialsIncorrect
     */
    public function setCredentialsIncorrect(bool $credentialsIncorrect): void
    {
        $this->credentialsIncorrect = $credentialsIncorrect;
    }

    /**
     * @return bool
     */
    public function isMissingPassword(): bool
    {
        return $this->missingPassword;
    }

    /**
     * @param bool $missingPassword
     */
    public function setMissingPassword(bool $missingPassword): void
    {
        $this->missingPassword = $missingPassword;
    }

    /**
     * @return bool
     */
    public function isMissingUsername(): bool
    {
        return $this->missingUsername;
    }

    /**
     * @param bool $missingUsername
     */
    public function setMissingUsername(bool $missingUsername): void
    {
        $this->missingUsername = $missingUsername;
    }

    /**
     * @return bool
     */
    public function isAwaitingVerification(): bool
    {
        return $this->awaitingVerification;
    }

    /**
     * @param bool $awaitingVerification
     */
    public function setAwaitingVerification(bool $awaitingVerification): void
    {
        $this->awaitingVerification = $awaitingVerification;
    }

    /**
     * @return bool
     */
    public function isAccountRemoved(): bool
    {
        return $this->accountRemoved;
    }

    /**
     * @param bool $accountRemoved
     */
    public function setAccountRemoved(bool $accountRemoved): void
    {
        $this->accountRemoved = $accountRemoved;
    }

    /**
     * @return bool
     */
    public function isAccountMissing(): bool
    {
        return $this->accountMissing;
    }

    /**
     * @param bool $accountMissing
     */
    public function setAccountMissing(bool $accountMissing): void
    {
        $this->accountMissing = $accountMissing;
    }
}
