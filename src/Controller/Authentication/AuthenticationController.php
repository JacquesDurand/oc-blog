<?php

declare(strict_types=1);

namespace App\Controller\Authentication;

use App\Authentication\Role;
use App\Controller\Twig\AbstractController;
use App\Errors\LoginFormError;
use App\Errors\RegisterFormError;
use App\HTTP\Request;
use App\Manager\UserManager;
use App\Model\User;
use App\Validator\LoginFormValidator;
use App\Validator\RegisterFormValidator;
use PDOException;

require_once __DIR__.'/../../../vendor/autoload.php';


class AuthenticationController extends AbstractController
{
    /** @var UserManager */
    private UserManager $userManager;

    /** @var RegisterFormValidator  */
    private RegisterFormValidator $registerFormValidator;

    /** @var LoginFormValidator  */
    private LoginFormValidator $loginFormValidator;

    public function __construct()
    {
        parent::__construct();
        $this->userManager = new UserManager();
        $this->registerFormValidator = new RegisterFormValidator();
        $this->loginFormValidator = new LoginFormValidator();
    }

    public function register(Request $request)
    {
        switch ($request->method) {
            case 'GET':
                $this->generateCsrfToken($request);
                $csrfToken = $_SESSION['csrf_token'];
                print_r( $this->render('Authentication/register_form.html.twig', [
                    'token' => $csrfToken,
                    'errors' => new RegisterFormError()
                ]));
                break;
            case 'POST':
                if (!$this->verifyCsrfToken($request)) {
                    print_r( $this->render('Errors/Csrf.html.twig'));
                }
                if (!$this->registerFormValidator->isRegisterFormValid($request)) {
                    $this->generateCsrfToken($request);
                    $csrfToken = $_SESSION['csrf_token'];
                    print_r( $this->render('Authentication/register_form.html.twig', [
                        'token' => $csrfToken,
                        'errors' => $this->registerFormValidator->getRegisterFormErrors($request)
                    ]));
                } else {
                    try {
                        $request->request['role'] = Role::ROLE_AWAITING_VERIFICATION;
                        $this->userManager->createUser($request->request);
                        header('Location: http://localhost/admin/users');
                    } catch (PDOException $exception) {
                        print_r( $exception->getMessage());
                    }
                }

        }
    }

    public function login(Request $request)
    {
        switch ($request->method) {
            case 'GET':
                $this->generateCsrfToken($request);
                $csrfToken = $_SESSION['csrf_token'];
                print_r( $this->render('Authentication/login_form.html.twig', [
                    'token' => $csrfToken,
                    'errors' => new LoginFormError()
                ]));
                break;
            case 'POST':
                if (!$this->verifyCsrfToken($request)) {
                    print_r( $this->render('Errors/Csrf.html.twig'));
                }
                if (!$this->loginFormValidator->isLoginFormValid($request)) {
                    $errors = $this->loginFormValidator->getLoginFormErrors($request);
                    if ($errors->isAwaitingVerification()) {
                        print_r( $this->render('Authentication/awaiting_verification.html.twig'));
                    } elseif (!$errors->isAccountMissing() && !$errors->isAccountRemoved()) {
                        $this->generateCsrfToken($request);
                        $csrfToken = $_SESSION['csrf_token'];
                        print_r( $this->render('Authentication/login_form.html.twig', [
                            'token' => $csrfToken,
                            'errors' => $this->loginFormValidator->getLoginFormErrors($request)
                        ]));
                    } else {
                        header('Location: http://localhost/register');
                    }
                } else {
                    /** @var User $user */
                    $user = $this->userManager->getUserByUsername($request->request['username']);
                    $_SESSION['userId'] = $user->getId();
                    $_SESSION['role'] = $user->getRole();
                    header('Location: http://localhost');
                }
        }
    }

    public function logout(Request $request)
    {
        $_SESSION['userId'] = null;
        $_SESSION['role'] = null;
        session_unset();
        session_destroy();
        $request->session = [];
        header('Location: http://localhost');
    }
}
