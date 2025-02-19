<?php

namespace App\Http;

class Request
{
    private string $uri;
    private string $method;
    private array $headers;
    private string $payload;

    public function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->headers = getallheaders();
        $this->payload = file_get_contents('php://input');
    }

    public function getPayload(): array
    {
        $data = json_decode($this->payload, true);
        return is_array($data) ? $data : [];
    }

    public function getRawPayload(): string
    {
        return $this->payload;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
