<?php

declare(strict_types=1);

namespace App\Validator;

require_once __DIR__.'/../../vendor/autoload.php';

use App\Errors\RegisterFormError;
use App\HTTP\Request;
use App\Manager\UserManager;

class RegisterFormValidator
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
    public function isRegisterFormValid(Request $request): bool
    {
        return (
            (isset($request->request['username']) && !empty($request->request['username'])) &&
            (isset($request->request['password']) && !empty($request->request['password'])) &&
            (isset($request->request['email']) && !empty($request->request['email'])) &&
            !$this->userManager->getUserByUsername($request->request['username']) &&
            !$this->userManager->getUserByEmail($request->request['email']) &&
            preg_match('|^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{8,}$|', $request->request['password'])
        );
    }

    /**
     * Returns the form errors when the form is not valid
     * @param Request $request
     * @return RegisterFormError
     * @throws \Exception
     */
    public function getRegisterFormErrors(Request $request): RegisterFormError
    {
        $error = new RegisterFormError();

        if (!isset($request->request['username']) || empty($request->request['username'])) {
            $error->setMissingUsername(true);
        }
        if (!isset($request->request['email']) || empty($request->request['email'])) {
            $error->setMissingEmail(true);
        }
        if (!isset($request->request['password']) || empty($request->request['password'])) {
            $error->setMissingPassword(true);
        }
        if (!preg_match('|^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{8,}$|', $request->request['password'])) {
            $error->setPasswordInvalid(true);
        }
        if ($this->userManager->getUserByUsername($request->request['username'])) {
            $error->setUsernameUsed(true);
        }
        if ($this->userManager->getUserByEmail($request->request['email'])) {
            $error->setEmailUsed(true);
        }

        return $error;
    }
}
