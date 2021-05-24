<?php

declare(strict_types=1);

namespace App\Controller\Admin\Category;

require_once __DIR__.'/../../../../vendor/autoload.php';


use App\HTTP\Request;
use App\Manager\CategoryManager;

class AdminCategoryController
{
    /** @var CategoryManager */
    private $categoryManager;

    public function __construct()
    {
        $this->categoryManager = new CategoryManager();
    }

    public function show(Request $request): array
    {
        return  $this->categoryManager->getAllCategories();
    }

    public function create(Request $request): void
    {
        $this->categoryManager->createCategory('category5');
    }
}
