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
        echo $this->render('/Admin/Category/show.html.twig', [ 'categories' => $this->categoryManager->getAllCategories()]);
    }

    public function create(Request $request): void
    {
        $name = $request->request['name'];
        $this->categoryManager->createCategory($name);
    }

    public function delete(Request $request)
    {
        $id = (int)$request->requirements[0];
        try {
            $this->categoryManager->deleteCategory($id);
            echo $this->render('/Admin/Category/show.html.twig', [ 'categories' => $this->categoryManager->getAllCategories()]);
        } catch (ResourceNotFoundException $exception) {
            echo $this->render('Errors/404_resource.html.twig');
        }
    }

    public function update(Request $request)
    {
        $id = (int)$request->requirements[0];
        $name = $request->request['name'];
        try {
            $this->categoryManager->updateCategory($id, $name);
            echo $this->render('/Admin/Category/show.html.twig', ['categories' => $this->categoryManager->getAllCategories()]);
        } catch (ResourceNotFoundException $exception) {
            echo $this->render('Errors/404_resource.html.twig');
        }
    }
}
