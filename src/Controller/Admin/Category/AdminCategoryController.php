<?php

declare(strict_types=1);

namespace App\Controller\Admin\Category;

require_once __DIR__.'/../../../../vendor/autoload.php';


use App\Controller\Twig\AbstractController;
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
        $this->categoryManager->createCategory('category5');
    }

    public function delete(Request $request)
    {
        return  $this->categoryManager->getAllCategories();
    }
}
