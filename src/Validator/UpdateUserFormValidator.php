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

    /**
     * Checks if the form is valid
     * @throws \Exception
     */
    public function isUpdateUserFormValid(Request $request): bool
    {
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

    /**
     * Returns the form errors when not valid
     * @param Request $request
     * @return UpdateUserFormError
     * @throws \Exception
     */
    public function getUpdateUserFormErrors(Request $request): UpdateUserFormError
    {
        $error = new UpdateUserFormError();

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
