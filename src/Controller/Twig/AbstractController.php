<?php

declare(strict_types=1);

namespace App\Controller\Twig;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__.'/../../../vendor/autoload.php';


abstract class AbstractController
{
    /** @var FilesystemLoader */
    private $fileLoader;

    /** @var Environment */
    private $twig;

    public function __construct()
    {
        $this->fileLoader = new FilesystemLoader(__DIR__ . '/../../../templates');
        $this->twig = new Environment($this->fileLoader);
    }

    protected function render(string $template, array $context = []): string
    {
        return $this->twig->render($template, $context);
    }
}
