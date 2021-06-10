<?php

declare(strict_types=1);

namespace App\Errors;

class RegisterFormError
{
    /** @var bool */
    private bool $missingUsername = false;

    /** @var bool */
    private bool $missingPassword = false;

    /** @var bool */
    private bool $missingEmail = false;

    /** @var bool */
    private bool $usernameUsed = false;

    /** @var bool */
    private bool $emailUsed = false;

    /** @var bool */
    private bool $passwordInvalid = false;

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
    public function isMissingEmail(): bool
    {
        return $this->missingEmail;
    }

    /**
     * @param bool $missingEmail
     */
    public function setMissingEmail(bool $missingEmail): void
    {
        $this->missingEmail = $missingEmail;
    }

    /**
     * @return bool
     */
    public function isUsernameUsed(): bool
    {
        return $this->usernameUsed;
    }

    /**
     * @param bool $usernameUsed
     */
    public function setUsernameUsed(bool $usernameUsed): void
    {
        $this->usernameUsed = $usernameUsed;
    }

    /**
     * @return bool
     */
    public function isEmailUsed(): bool
    {
        return $this->emailUsed;
    }

    /**
     * @param bool $emailUsed
     */
    public function setEmailUsed(bool $emailUsed): void
    {
        $this->emailUsed = $emailUsed;
    }

    /**
     * @return bool
     */
    public function isPasswordInvalid(): bool
    {
        return $this->passwordInvalid;
    }

    /**
     * @param bool $passwordInvalid
     */
    public function setPasswordInvalid(bool $passwordInvalid): void
    {
        $this->passwordInvalid = $passwordInvalid;
    }
}
