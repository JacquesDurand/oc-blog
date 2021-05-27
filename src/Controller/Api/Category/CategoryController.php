<?php

declare(strict_types=1);

namespace App\Controller\Api\Category;

require_once __DIR__.'/../../../../vendor/autoload.php';

use App\HTTP\Request;
use App\Manager\CategoryManager;

class CategoryController
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
}
