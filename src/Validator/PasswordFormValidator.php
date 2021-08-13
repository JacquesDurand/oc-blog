<?php

declare(strict_types=1);

namespace App\Validator;

require_once __DIR__.'/../../vendor/autoload.php';


use App\Errors\PasswordFormError;
use App\HTTP\Request;
use App\Manager\UserManager;
use App\Model\User;
use App\Security\SecurityService;

class PasswordFormValidator
{
    /** @var UserManager */
    private UserManager $userManager;

    /** @var SecurityService */
    private SecurityService $securityService;

    public function __construct()
    {
        $this->userManager = new UserManager();
        $this->securityService = new SecurityService();
    }

    public function isPasswordFormValid(Request $request): bool
    {
        /** @var User $connectedUser */
        $connectedUser = $this->userManager->getUserById($request->session['userId']);
        return (
            (isset($request->request['oldPassword']) && !empty($request->request['oldPassword'])) &&
            (isset($request->request['newPassword']) && !empty($request->request['newPassword'])) &&
            (isset($request->request['confirmPassword']) && !empty($request->request['confirmPassword'])) &&
            (null !== $connectedUser) &&
            ($this->securityService->verifyPassword($request->request['oldPassword'], $connectedUser->getPassword())) &&
            ($request->request['newPassword'] === $request->request['confirmPassword'])
        );
    }

    public function getPasswordFormErrors(Request $request): PasswordFormError
    {
        $error = new PasswordFormError();
        $connectedUser = $this->userManager->getUserById($request->session['userId']);

        if (!isset($request->request['oldPassword']) || empty($request->request['oldPassword'])) {
            $error->setMissingField(true);
        }
        if (!isset($request->request['newPassword']) || empty($request->request['newPassword'])) {
            $error->setMissingField(true);
        }
        if (!isset($request->request['confirmPassword']) || empty($request->request['confirmPassword'])) {
            $error->setMissingField(true);
        }
        if (!$connectedUser) {
            $error->setNotConnected(true);
        }
        if (!$this->securityService->verifyPassword($request->request['oldPassword'], $connectedUser->getPassword())) {
            $error->setPasswordIncorrect(true);
        }
        if ($request->request['newPassword'] !== $request->request['confirmPassword']) {
            $error->setNotMatching(true);
        }

        return $error;
    }
}
