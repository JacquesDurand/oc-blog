<?php

declare(strict_types=1);

namespace App\Validator;

use App\Authentication\Role;
use App\Errors\LoginFormError;
use App\HTTP\Request;
use App\Manager\UserManager;
use App\Model\User;
use App\Security\SecurityService;

require_once __DIR__.'/../../vendor/autoload.php';



class LoginFormValidator
{
    /** @var UserManager  */
    private UserManager $userManager;

    /** @var SecurityService  */
    private SecurityService $securityService;

    public function __construct()
    {
        $this->userManager = new UserManager();
        $this->securityService = new SecurityService();
    }

    public function isLoginFormValid(Request $request): bool
    {
        /** @var User $user */
        $user = $this->userManager->getUserByUsername($request->request['username']);
        if (!$user) {
            return false;
        }

        return (
        (isset($request->request['username']) && !empty($request->request['username'])) &&
        (isset($request->request['password']) && !empty($request->request['password'])) &&
        $this->securityService->verifyPassword($request->request['password'], $user->getPassword()) &&
        ($user->getRole() > Role::ROLE_AWAITING_VERIFICATION)
        );
    }

    public function getLoginFormErrors(Request $request): LoginFormError
    {
        $error = new LoginFormError();
        /** @var User $user */
        $user = $this->userManager->getUserByUsername($request->request['username']);

        if (!isset($request->request['username']) || empty($request->request['username'])) {
            $error->setMissingUsername(true);
        }
        if (!isset($request->request['password']) || empty($request->request['password'])) {
            $error->setMissingPassword(true);
        }
        if (!$user) {
            $error->setAccountMissing(true);
            return $error;
        }
        if (!$this->securityService->verifyPassword($request->request['password'], $user->getPassword())) {
            $error->setCredentialsIncorrect(true);
        }
        if ($user->getRole() === Role::ROLE_AWAITING_VERIFICATION) {
            $error->setAwaitingVerification(true);
        }
        if ($user->getRole() === Role::ROLE_REMOVED) {
            $error->setAccountRemoved(true);
        }
        return $error;
    }
}
