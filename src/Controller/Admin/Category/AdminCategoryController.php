<?php

declare(strict_types=1);

namespace App\Controller\Admin\Category;

require_once __DIR__.'/../../../../vendor/autoload.php';


use App\Controller\Twig\AbstractController;
use App\Exception\ResourceNotFoundException;
use App\HTTP\Request;
use App\Manager\CategoryManager;

class AdminCategoryController extends AbstractController
{
    /** @var CategoryManager */
    private $categoryManager;

    public function __construct()
    {
        parent::__construct();
        $this->categoryManager = new CategoryManager();
    }

    public function show(Request $request)
    {
        print_r( $this->render('/Admin/Category/show.html.twig', [ 'categories' => $this->categoryManager->getAllCategories()]));
    }

    public function create(Request $request): void
    {
        switch ($request->method) {
            case 'GET':
                $this->generateCsrfToken($request);
                $csrfToken = $_SESSION['csrf_token'];
                print_r( $this->render('/Admin/Category/form.html.twig', [
                    'token' => $csrfToken
                ]));
                break;
            case 'POST':
                if (!$this->verifyCsrfToken($request)) {
                    print_r( $this->render('Errors/Csrf.html.twig'));
                } else {
                    $this->cleanInput($request);
                    $name = $request->request['name'];
                    $this->categoryManager->createCategory($name);

                    header("Location: http://localhost/admin/categories");
                }
        }
    }

    public function delete(Request $request)
    {
        $categoryId = (int)$request->requirements[0];
        try {
            $this->categoryManager->deleteCategory($categoryId);
            header("Location: http://localhost/admin/categories");
        } catch (ResourceNotFoundException $exception) {
            print_r( $this->render('Errors/404_resource.html.twig'));
        }
    }

    public function update(Request $request)
    {
        $categoryId = (int)$request->requirements[0];

        switch ($request->method) {
            case 'GET':
                if ($category = $this->categoryManager->getCategoryById($categoryId)) {
                    $this->generateCsrfToken($request);
                    $csrfToken = $_SESSION['csrf_token'];
                    print_r( $this->render('/Admin/Category/update.html.twig', [
                        'category' => $category,
                        'token' => $csrfToken
                    ]));
                } else {
                    print_r( $this->render('Errors/404_resource.html.twig'));
                }
                break;

            case 'POST':
                if (!$this->verifyCsrfToken($request)) {
                    print_r( $this->render('Errors/Csrf.html.twig'));
                } else {
                    $this->cleanInput($request);
                    $name = $request->request['name'];
                    try {
                        $this->categoryManager->updateCategory($categoryId, $name);
                        print_r( $this->render('/Admin/Category/show.html.twig', ['categories' => $this->categoryManager->getAllCategories()]));
                    } catch (ResourceNotFoundException $exception) {
                        print_r( $this->render('Errors/404_resource.html.twig'));
                    }
                }
        }
    }
}
