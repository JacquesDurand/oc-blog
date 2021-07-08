<?php

declare(strict_types=1);

namespace App\Controller\Api\Category;

require_once __DIR__.'/../../../../vendor/autoload.php';

use App\Controller\Twig\AbstractController;
use App\HTTP\Request;
use App\Manager\CategoryManager;

class CategoryController extends AbstractController
{
    /** @var CategoryManager */
    private CategoryManager $categoryManager;

    public function __construct()
    {
        parent::__construct();
        $this->categoryManager = new CategoryManager();
    }

    public function show(Request $request)
    {
        echo $this->render('/Front/Category/show.html.twig', [ 'categories' => $this->categoryManager->getAllCategories()]);
    }
}
