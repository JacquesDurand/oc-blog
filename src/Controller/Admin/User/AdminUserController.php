<?php

declare(strict_types=1);

namespace App\Controller\Admin\User;

use App\Controller\Twig\AbstractController;
use App\Exception\ResourceNotFoundException;
use App\HTTP\Request;
use App\Manager\UserManager;

require_once __DIR__.'/../../../../vendor/autoload.php';


class AdminUserController extends AbstractController
{
    /** @var UserManager */
    private $userManager;

    public function __construct()
    {
        parent::__construct();
        $this->userManager = new UserManager();
    }

    public function getAllUsers(Request $request)
    {
        $users = $this->userManager->getAllUsers();
        if ($users) {
            print_r($this->render('/Admin/User/show.html.twig', ['users' => $users]));
        } else {
            print_r($this->render('/Admin/User/show.html.twig'));
        }
    }

    public function addUser(Request $request)
    {
        switch ($request->method) {
            case 'GET':
                $this->generateCsrfToken($request);
                $csrfToken = $_SESSION['csrf_token'];
                print_r($this->render('Admin/User/form.html.twig', [
                    'token' => $csrfToken
                ]));
                break;
            case 'POST':
                if (!$this->verifyCsrfToken($request)) {
                    print_r($this->render('Errors/Csrf.html.twig'));
                } else {
                    $this->cleanInput($request);
                    $this->userManager->createUser($request->request);
                    header("Location: http://localhost/admin/users");
                }
        }
    }

    public function getUserById(Request $request)
    {
        $userId = (int)$request->requirements[0];
        if ($user = $this->userManager->getUserById($userId)) {
            print_r($this->render('/Admin/User/show_one.html.twig', ['user' => $user]));
        }
    }

    public function deleteUser(Request $request)
    {
        $userId = (int)$request->requirements[0];
        try {
            $this->userManager->deleteUser($userId);
            print_r($this->render('/Admin/User/show.html.twig'));
        } catch (ResourceNotFoundException $exception) {
            print_r($this->render('Errors/404_resource.html.twig'));
        }
    }

    public function updateUser(Request $request)
    {
        $userId = (int) $request->requirements[0];

        switch ($request->method) {
            case 'GET':
                if ($user = $this->userManager->getUserById($userId)) {
                    $this->generateCsrfToken($request);
                    $csrfToken = $_SESSION['csrf_token'];
                    print_r($this->render('/Admin/User/update.html.twig', [
                        'user' => $user,
                        'token' => $csrfToken
                    ]));
                } else {
                    print_r($this->render('Errors/404_resource.html.twig'));
                }
                break;
            case 'POST':
                if (!$this->verifyCsrfToken($request)) {
                    print_r($this->render('Errors/Csrf.html.twig'));
                } else {
                    try {
                        $this->cleanInput($request);
                        $this->userManager->updateUser($userId, $request->request);
                        header("Location: http://localhost/admin/users");
                    } catch (ResourceNotFoundException $exception) {
                        print_r($this->render('Errors/404_resource.html.twig'));
                    }
                }
        }
    }

    public function verifyUser(Request $request)
    {
        $userId = (int) $request->requirements[0];
        try {
            $this->userManager->verifyUser($userId);
            header('Location: http://localhost/admin/users');
        } catch (ResourceNotFoundException $exception) {
            print_r($this->render('Errors/404_resource.html.twig'));
        }
    }
}
