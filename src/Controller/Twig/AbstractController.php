<?php

declare(strict_types=1);

namespace App\Controller\Twig;

use App\HTTP\Request;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__.'/../../../vendor/autoload.php';


abstract class AbstractController
{
    /** @var FilesystemLoader */
    private FilesystemLoader $fileLoader;

    /** @var Environment */
    private Environment $twig;

    public function __construct()
    {
        $this->fileLoader = new FilesystemLoader(__DIR__ . '/../../../templates');
        $this->twig = new Environment($this->fileLoader);
    }

    protected function render(string $template, array $context = []): string
    {
        return $this->twig->render($template, $context);
    }

    protected function generateCsrfToken(Request $request): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_expiresAt'] = (new \DateTime())->add(new \DateInterval('PT1H'));
    }

    protected function verifyCsrfToken(Request $request): bool
    {
        $token = $request->request['token'];
        if ($request->session['csrf_token'] === $token) {
            if ($request->session['csrf_token_expiresAt'] <= new \DateTime()) {
                return false;
            }
            unset($request->session['csrf_token']);
            unset($request->session['csrf_token_expiresAt']);
            return true;
        }
        return false;
    }

    protected function cleanInput(Request $request): void
    {
        $cleanRequest = [];
        foreach ($request->request as $key => $value) {
            $value = addslashes(trim(htmlentities(htmlspecialchars($value))));
            $cleanRequest[$key] = $value;
        }
        unset($request->request);
        $request->request = $cleanRequest;
    }
}
