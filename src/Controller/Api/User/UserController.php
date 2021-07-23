<?php

declare(strict_types=1);

namespace App\Controller\Api\User;

use App\Authentication\Role;
use App\Controller\Twig\AbstractController;
use App\Errors\RegisterFormError;
use App\Errors\UpdateUserFormError;
use App\Exception\ResourceNotFoundException;
use App\HTTP\Request;
use App\Manager\CommentManager;
use App\Manager\UserManager;
use App\Model\User;
use App\Validator\PasswordFormValidator;
use App\Validator\RegisterFormValidator;
use App\Validator\UpdateUserFormValidator;

require_once __DIR__.'/../../../../vendor/autoload.php';


class UserController extends AbstractController
{
    /** @var UserManager */
    private UserManager $userManager;

    /** @var CommentManager */
    private CommentManager $commentManager;

    /** @var UpdateUserFormValidator */
    private UpdateUserFormValidator $updateUserFormValidator;

    /** @var PasswordFormValidator */
    private PasswordFormValidator $passwordFormValidator;

    public function __construct()
    {
        parent::__construct();
        $this->userManager = new UserManager();
        $this->commentManager = new CommentManager();
        $this->updateUserFormValidator = new UpdateUserFormValidator();
        $this->passwordFormValidator = new PasswordFormValidator();
    }

    public function getProfile(Request $request)
    {
        $connectedUserId = (int) $request->session['userId'];
        if ($connectedUser = $this->userManager->getUserById($connectedUserId)) {
            $comments = $this->commentManager->getCommentsForAuthor($connectedUser);
            echo $this->render('Front/User/me.html.twig', [
                'user' => $connectedUser,
                'comments' => $comments,
                'isConnected' => isset($request->session['userId']) && !empty($request->session['userId']),
                'isAdmin' => isset($request->session['role']) && !empty($request->session['role']) && Role::ROLE_ADMIN <= $request->session['role']
            ]);
        } else {
            echo $this->render('Errors/403.html.twig');
        }
    }

    public function updateProfile(Request $request)
    {
        $connectedUserId = (int) $request->session['userId'];

        switch ($request->method) {
            case 'GET':
                if ($user = $this->userManager->getUserById($connectedUserId)) {
                    $this->generateCsrfToken($request);
                    echo $this->render('/Front/User/update.html.twig', [
                        'user' => $user,
                        'token' => $_SESSION['csrf_token'],
                        'errors' => new UpdateUserFormError(),
                        'isConnected' => isset($request->session['userId']) && !empty($request->session['userId']),
                        'isAdmin' => isset($request->session['role']) && !empty($request->session['role']) && Role::ROLE_ADMIN <= $request->session['role']
                    ]);
                } else {
                    echo $this->render('Errors/403.html.twig');
                }
                break;
            case 'POST':
                /** @var User $user */
                if ($user = $this->userManager->getUserById($connectedUserId)) {
                    if (!$this->verifyCsrfToken($request)) {
                        echo $this->render('Errors/Csrf.html.twig');
                    } elseif (!$this->updateUserFormValidator->isUpdateUserFormValid($request)) {
                        $this->generateCsrfToken($request);
                        echo $this->render('/Front/User/update.html.twig', [
                            'user' => $user,
                            'token' => $_SESSION['csrf_token'],
                            'errors' => $this->updateUserFormValidator->getUpdateUserFormErrors($request),
                            'isConnected' => isset($request->session['userId']) && !empty($request->session['userId']),
                            'isAdmin' => isset($request->session['role']) && !empty($request->session['role']) && Role::ROLE_ADMIN <= $request->session['role']
                        ]);
                    } else {
                        try {
                            $this->cleanInput($request);
                            $request->request['role'] = $user->getRole();
                            $this->userManager->updateUserWithoutPassword($connectedUserId, $request->request);
                            header('Location: http://localhost/me');
                        } catch (ResourceNotFoundException $exception) {
                            echo $this->render('Errors/404_resource.html.twig');
                        }
                    }
                } else {
                    echo $this->render('Errors/403.html.twig');
                }
        }
    }

    public function updatePassword(Request $request)
    {
        $connectedUserId = (int) $request->session['userId'];

        switch ($request->method) {
            case 'GET':
                if ($user = $this->userManager->getUserById($connectedUserId)) {
                    $this->generateCsrfToken($request);
                    echo $this->render('/Front/User/update_password.html.twig', [
                        'token' => $_SESSION['csrf_token'],
                        'errors' => new UpdateUserFormError(),
                        'isConnected' => isset($request->session['userId']) && !empty($request->session['userId']),
                        'isAdmin' => isset($request->session['role']) && !empty($request->session['role']) && Role::ROLE_ADMIN <= $request->session['role']
                    ]);
                } else {
                    echo $this->render('Errors/403.html.twig');
                }
                break;
            case 'POST':
                /** @var User $user */
                if ($user = $this->userManager->getUserById($connectedUserId)) {
                    if (!$this->verifyCsrfToken($request)) {
                        echo $this->render('Errors/Csrf.html.twig');
                    } elseif (!$this->passwordFormValidator->isPasswordFormValid($request)) {
                        $this->generateCsrfToken($request);
                        echo $this->render('/Front/User/update_password.html.twig', [
                            'token' => $_SESSION['csrf_token'],
                            'errors' => $this->passwordFormValidator->getPasswordFormErrors($request),
                            'isConnected' => isset($request->session['userId']) && !empty($request->session['userId']),
                            'isAdmin' => isset($request->session['role']) && !empty($request->session['role']) && Role::ROLE_ADMIN <= $request->session['role']
                        ]);
                    } else {
                        try {
                            $this->cleanInput($request);
                            $this->userManager->updatePassword($connectedUserId, $request->request['newPassword']);
                            header('Location: http://localhost/me');
                        } catch (ResourceNotFoundException $exception) {
                            echo $this->render('Errors/404_resource.html.twig');
                        }
                    }
                } else {
                    echo $this->render('Errors/403.html.twig');
                }
        }
    }

    public function deleteProfile(Request $request)
    {
        $connectedUserId = (int) $request->session['userId'];
        try {
            $this->userManager->deleteUser($connectedUserId);
            header('Location: http://localhost/');
        } catch (ResourceNotFoundException $exception) {
            echo $this->render('Errors/404_resource.html.twig');
        }
    }
}
