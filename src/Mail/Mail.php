<?php

declare(strict_types=1);

namespace App\Mail;

class Mail
{
    /** @var string */
    private string $recipientMail;

    /** @var string */
    private string $subject;

    /** @var string */
    private string $message;

    /** @var array */
    private array $headers;

    /** @var array */
    private array $params;

    /**
     * @return string
     */
    public function getRecipientMail(): string
    {
        return $this->recipientMail;
    }

    /**
     * @param string $recipientMail
     */
    public function setRecipientMail(string $recipientMail): void
    {
        $this->recipientMail = $recipientMail;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * @param string $header
     */
    public function addHeader(string $header): void
    {
        $this->headers[] = $header;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * @param string $param
     */
    public function addParam(string $param): void
    {
        $this->params[] = $param;
    }
}
