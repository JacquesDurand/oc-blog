<?php

declare(strict_types=1);

namespace App\Controller\Home;

require_once __DIR__.'/../../../vendor/autoload.php';


use App\Controller\Twig\AbstractController;
use App\HTTP\Request;

class HomePageController extends AbstractController
{
    public function index(Request $request)
    {
        echo $this->render('Home/index.html.twig', []);
    }
}
