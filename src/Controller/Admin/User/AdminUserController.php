<?php

declare(strict_types=1);

namespace App\Controller\Admin\User;

use App\HTTP\Request;
use App\Manager\UserManager;

require_once __DIR__.'/../../../../vendor/autoload.php';


class AdminUserController
{
    /** @var UserManager */
    private $userManager;

    public function __construct()
    {
        $this->userManager = new UserManager();
    }

    public function getAllUsers(Request $request)
    {
    }

    public function addUser(Request $request)
    {
    }

    public function getUserById(Request $request)
    {
    }

    public function deleteUser(Request $request)
    {
    }

    public function updateUser(Request $request)
    {
    }
}
