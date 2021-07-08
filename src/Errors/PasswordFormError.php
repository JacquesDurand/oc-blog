<?php

declare(strict_types=1);


namespace App\Errors;


class PasswordFormError
{
    /** @var bool  */
    private bool $missingField = false;

    /** @var bool  */
    private bool $notConnected = false;

    /** @var bool  */
    private bool $passwordIncorrect = false;

    /** @var bool  */
    private bool $notMatching = false;

    /**
     * @return bool
     */
    public function isMissingField(): bool
    {
        return $this->missingField;
    }

    /**
     * @param bool $missingField
     */
    public function setMissingField(bool $missingField): void
    {
        $this->missingField = $missingField;
    }

    /**
     * @return bool
     */
    public function isNotConnected(): bool
    {
        return $this->notConnected;
    }

    /**
     * @param bool $notConnected
     */
    public function setNotConnected(bool $notConnected): void
    {
        $this->notConnected = $notConnected;
    }

    /**
     * @return bool
     */
    public function isPasswordIncorrect(): bool
    {
        return $this->passwordIncorrect;
    }

    /**
     * @param bool $passwordIncorrect
     */
    public function setPasswordIncorrect(bool $passwordIncorrect): void
    {
        $this->passwordIncorrect = $passwordIncorrect;
    }

    /**
     * @return bool
     */
    public function areNotMatching(): bool
    {
        return $this->notMatching;
    }

    /**
     * @param bool $notMatching
     */
    public function setNotMatching(bool $notMatching): void
    {
        $this->notMatching = $notMatching;
    }
}