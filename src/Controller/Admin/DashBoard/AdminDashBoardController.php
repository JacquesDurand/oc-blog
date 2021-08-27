<?php

declare(strict_types=1);

namespace App\Controller\Admin\DashBoard;

require_once __DIR__.'/../../../../vendor/autoload.php';

use App\Controller\Twig\AbstractController;
use App\HTTP\Request;

class AdminDashBoardController extends AbstractController
{
    public function index(Request $request)
    {
        print_r($this->render('Admin/base.html.twig'));
    }
}
