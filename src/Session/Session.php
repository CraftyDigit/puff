<?php

namespace CraftyDigit\Puff\Session;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Exceptions\SessionException;

class Session implements SessionInterface
{
    public function __construct(
        private readonly Config $config
    )
    {}

    public function start(): void
    {
        if ($this->config->sessions['enabled'] ?? false) {
            return;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            throw new SessionException('Session already started');
        }

        if (headers_sent($fileName, $lineNumber)) {
            throw new SessionException("Headers already sent");
        }
        
        session_start();
    }
    
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }
    
    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }
}