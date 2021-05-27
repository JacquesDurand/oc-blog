<?php

declare(strict_types=1);

namespace App\Controller\Api\User;

use App\HTTP\Request;
use App\Manager\UserManager;

require_once __DIR__.'/../../../../vendor/autoload.php';


class UserController
{
    /** @var UserManager */
    private $userManager;

    public function __construct()
    {
        $this->userManager = new UserManager();
    }

    public function getProfile(Request $request)
    {
    }

    public function updateProfile(Request $request)
    {
    }
}
