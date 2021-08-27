<?php

declare(strict_types=1);

namespace App\Controller\Home;

require_once __DIR__.'/../../../vendor/autoload.php';


use App\Authentication\Role;
use App\Controller\Twig\AbstractController;
use App\HTTP\Request;

class HomePageController extends AbstractController
{
    /**
     * Renders the Blog's index
     * @param Request $request
     */
    public function index(Request $request)
    {
        print_r($this->render('Home/index.html.twig', [
            'isConnected' => isset($request->session['userId']) && !empty($request->session['userId']),
            'isAdmin' => isset($request->session['role']) && !empty($request->session['role']) && Role::ROLE_ADMIN <= $request->session['role']
        ]));
    }
}
