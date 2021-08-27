<?php

declare(strict_types=1);

namespace App\Security;

require_once __DIR__.'/../../vendor/autoload.php';


class SecurityService
{
    /**
     * Hashes the User's password
     * @param string $password
     * @return string
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Checks if the password is the same as the hashed one
     * @param string $password
     * @param string $hashed
     * @return bool
     */
    public function verifyPassword(string $password, string $hashed): bool
    {
        return password_verify($password, $hashed);
    }
}
