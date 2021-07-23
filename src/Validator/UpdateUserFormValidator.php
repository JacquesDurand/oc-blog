<?php

declare(strict_types=1);

namespace App\Validator;

use App\Errors\RegisterFormError;
use App\Errors\UpdateUserFormError;
use App\HTTP\Request;
use App\Manager\UserManager;
use App\Model\User;

require_once __DIR__.'/../../vendor/autoload.php';


class UpdateUserFormValidator
{
    /** @var UserManager  */
    private UserManager $userManager;

    public function __construct()
    {
        $this->userManager = new UserManager();
    }

    public function isUpdateUserFormValid(Request $request): bool
    {
        /** @var User $connectedUser */
        $connectedUser = $this->userManager->getUserById($request->session['userId']);
        $newEmailInUse = true;
        if ($connectedUser->getEmail() === $request->request['email']) {
            $newEmailInUse = false;
        } elseif (!$this->userManager->getUserByEmail($request->request['email'])) {
            $newEmailInUse = false;
        }
        return (
            (isset($request->request['username']) && !empty($request->request['username'])) &&
            (isset($request->request['email']) && !empty($request->request['email'])) &&
            (null === $this->userManager->getUserByUsername($request->request['username'])) &&
            !$newEmailInUse
        );
    }

    public function getUpdateUserFormErrors(Request $request): UpdateUserFormError
    {
        $error = new UpdateUserFormError();

        /** @var User $connectedUser */
        $connectedUser = $this->userManager->getUserById($request->session['userId']);
        $newEmailInUse = true;
        if ($connectedUser->getEmail() === $request->request['email']) {
            $newEmailInUse = false;
        } elseif (!$this->userManager->getUserByEmail($request->request['email'])) {
            $newEmailInUse = false;
        }
        if (!isset($request->request['username']) || empty($request->request['username'])) {
            $error->setMissingUsername(true);
        }
        if (!isset($request->request['email']) || empty($request->request['email'])) {
            $error->setMissingEmail(true);
        }
        if ($this->userManager->getUserByUsername($request->request['username'])) {
            $error->setUsernameUsed(true);
        }
        if ($newEmailInUse) {
            $error->setEmailUsed(true);
        }

        return $error;
    }
}
