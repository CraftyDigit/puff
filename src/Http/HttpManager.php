<?php

namespace CraftyDigit\Puff\Http;

use CraftyDigit\Puff\Attributes\Singleton;
use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\Enums\ResponseType;
use CraftyDigit\Puff\Exceptions\ClassNotFoundException;
use CraftyDigit\Puff\Exceptions\ConfigParamException;
use GuzzleHttp\Psr7\Stream;
use http\Exception\RuntimeException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[Singleton]
class HttpManager implements HttpManagerInterface
{
    public function __construct(
        protected readonly Config $config,
        protected readonly ContainerExtendedInterface $container,
        protected ?ServerRequestInterface $serverRequest = null,
    )
    {}
    
    public function getClient(?string $clientClass = null, array $options = []): ClientInterface
    {
        if (is_null($clientClass)) {
            $clientClass = $this->config->http_client['default'];
        }
        
        if (!class_exists($clientClass)) {
            throw new ClassNotFoundException(className: $clientClass);
        }
        
        if (!is_subclass_of($clientClass, ClientInterface::class)) {
            throw new RuntimeException("Class $clientClass must implement " . ClientInterface::class);
        }
        
        return $this->container->get($clientClass, [...$options]);
    }

    public function getServerRequest(): ServerRequestInterface
    {
        return $this->serverRequest;
    }

    public function setServerRequest(ServerRequestInterface $serverRequest): void
    {
        $this->serverRequest = $serverRequest;
    }
    
    public function setServerRequestFromDefault(): void
    {
        $this->setServerRequestFromGlobals();
    }
    
    protected function setServerRequestFromGlobals(): void
    {
        $serverRequestProviderMethod = $this->config->http_client['default_server_request_provider_method'];
        
        if (is_null($serverRequestProviderMethod)) {
            throw new ConfigParamException(configParamName: 'http_client.default_server_request_provider_method');
        };
        
        if (!is_callable($serverRequestProviderMethod)) {
            throw new ConfigParamException('http_client.default_server_request_provider_method is not callable');
        }
        
        $this->serverRequest = $serverRequestProviderMethod();
    }
    
    public function createResponse(
        ?ResponseType $type = null,
        ?int   $code = null,
        string $codeMessage = '',
        array  $headers = [],
        string $data = ''
    ): ResponseInterface
    {
        $type = $type ?? ResponseType::HTML;
        
        if ($type === ResponseType::REDIRECT) {
            $code = $code ?? 302;
            $codeMessage = $codeMessage ?? 'Found';
        } else {
            $code = $code ?? 200;
            
            if (is_string($data)) {
                $stream = $this->container->get(Stream::class, ['stream' => fopen('php://memory', 'r+')]); 
                
                $stream->write($data);
                $data = $stream;
            }
        }
        
        $response = $this->container->get(ResponseInterface::class, ['status' => $code, 'reason' => $codeMessage]);
        $charset = 'charset=utf-8';
        
        switch($type) {
            case ResponseType::HTML:
                $response = $response->withHeader('Content-Type', 'text/html; ' . $charset);
                $response = $response->withBody($data);
                break;

            case ResponseType::JSON:
                $response = $response->withHeader('Content-Type', 'application/json; ' . $charset);

                if (is_array($data)) {
                    $data = json_encode($data);
                }

                $response = $response->withBody($data);
                break;

            case ResponseType::REDIRECT:
                
                $response = $response->withHeader('Location', $data);
                break;
            
            case ResponseType::TEXT:
                $response = $response->withHeader('Content-Type', 'text/plain; ' . $charset);
                $response = $response->withBody($data);
                break;
        }

        if (!empty($headers)) {
            foreach ($headers as $headerName => $headerValue) {
                $response = $response->withHeader($headerName, $headerValue);
            }
        }

        return $response;        
    }

    public function sendResponse(ResponseInterface $response): void
    {
        if (headers_sent()) {
            return;
        }

        $this->sendHeaders($response);
        $this->sendContent($response);
    }

    protected function sendHeaders(ResponseInterface $response): void
    {
        if (headers_sent()) {
            return;
        }

        header(sprintf('HTTP/%s %s %s', $response->getProtocolVersion(), $response->getStatusCode(), $response->getReasonPhrase()), true, $response->getStatusCode());

        foreach ($response->getHeaders() as $name => $values) {
            $replace = true;

            foreach ($values as $value) {
                header($name . ': ' . $value, $replace, $response->getStatusCode());
                $replace = false;
            }
        }

        $contentType = $response->getHeader('Content-Type')[0] ?? 'text/html; charset=utf-8';

        header('Content-Type: ' . $contentType . ';', true, $response->getStatusCode());
    }

    protected function sendContent(ResponseInterface $response): void
    {
        echo $response->getBody();
    }
}