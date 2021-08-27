<?php

declare(strict_types=1);

namespace App\Controller\Api\Category;

require_once __DIR__.'/../../../../vendor/autoload.php';

use App\Authentication\Role;
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

    /**
     * Renders all categories
     * @param Request $request
     */
    public function show(Request $request)
    {
        print_r($this->render('/Front/Category/show.html.twig', [
            'categories' => $this->categoryManager->getAllCategories(),
            'isConnected' => isset($request->session['userId']) && !empty($request->session['userId']),
            'isAdmin' => isset($request->session['role']) && !empty($request->session['role']) && Role::ROLE_ADMIN <= $request->session['role']
        ]));
    }
}
