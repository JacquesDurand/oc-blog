<?php

declare(strict_types=1);

namespace App\Security;

require_once __DIR__.'/../../vendor/autoload.php';


class SecurityService
{
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $password, string $hashed): bool
    {
        return password_verify($password, $hashed);
    }
}
