<?php

declare(strict_types=1);

namespace App\Controller\Contact;

require_once __DIR__.'/../../../vendor/autoload.php';


use App\Authentication\Role;
use App\Controller\Twig\AbstractController;
use App\HTTP\Request;
use App\Mail\Mail;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__.'/../../..');
$dotenv->load();

class ContactController extends AbstractController
{
    public function contact(Request $request)
    {
        switch ($request->method) {
            case 'GET':
                $this->generateCsrfToken($request);
                $csrfToken = $_SESSION['csrf_token'];
                print_r($this->render('Contact/form.html.twig', [
                    'token' => $csrfToken,
                    'isConnected' => isset($request->session['userId']) && !empty($request->session['userId']),
                    'isAdmin' => isset($request->session['role']) && !empty($request->session['role']) && Role::ROLE_ADMIN <= $request->session['role']
                ]));
                break;
            case 'POST':
                if (!$this->verifyCsrfToken($request)) {
                    print_r($this->render('Errors/Csrf.html.twig'));
                } else {
                    $this->cleanInput($request);
                    $mail = $this->hydrateMailFromRequest($request);
                    if ($this->sendMail($mail)) {
                        print_r($this->render('Contact/confirmation.html.twig', [
                            'message' => $request->request['message'],
                            'isConnected' => isset($request->session['userId']) && !empty($request->session['userId']),
                            'isAdmin' => isset($request->session['role']) && !empty($request->session['role']) && Role::ROLE_ADMIN <= $request->session['role']
                        ]));
                    } else {
                        print_r($this->render('Contact/error.html.twig', [
                            'isConnected' => isset($request->session['userId']) && !empty($request->session['userId']),
                            'isAdmin' => isset($request->session['role']) && !empty($request->session['role']) && Role::ROLE_ADMIN <= $request->session['role']
                        ]));
                    }
                }
                break;
        }
    }

    private function sendMail(Mail $mail): bool
    {
        return mail($mail->getRecipientMail(), $mail->getSubject(), $mail->getMessage(), implode("\r\n", $mail->getHeaders()));
    }

    private function hydrateMailFromRequest(Request $request): Mail
    {
        $mail = new Mail();
        $recipientEmail = $_ENV['MAIL_TO'];
        $senderEmail = $request->request['email'];

        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';
        $headers[] = sprintf('From: Oc Blog User: <%s>', $senderEmail);
        $headers[] = 'X-Mailer: PHP/' . phpversion();

        $message =  sprintf('
<html>
<head>
  <title>Contact from Oc Blog</title>
</head>
<body>
  <p>%s</p>
   <hr>
   <p>User email: %s </p>
</body>
</html>
', $request->request['message'], $senderEmail);

        $mail->setRecipientMail($recipientEmail);
        $mail->setSubject($request->request['subject']);
        $mail->setMessage($message);
        $mail->setHeaders($headers);

        return $mail;
    }
}
