<?php

namespace Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Analog\Analog;
use Analog\Handler\File;

class RequestLoggerMiddleware implements MiddlewareInterface
{
    public function __construct($logFile = __DIR__ . '/../writable/logs/requests.json')
    {
        // Configure Analog para usar um arquivo de log
        Analog::handler(File::init($logFile));
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Start the timer to measure execution time
        $startTime = microtime(true);

        // Process the request and get the response
        $response = $handler->handle($request);

        // Calculate the execution time
        $executionTime = microtime(true) - $startTime;

        // Log the request
        $this->logRequest($request, $response, $executionTime);

        return $response;
    }

    private function logRequest(ServerRequestInterface $request, ResponseInterface $response, $executionTime)
    {
        // Start the session if it is not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Get session variables
        $sessionData = isset($_SESSION) ? $_SESSION : [];

        // Get client IP address
        $clientIp = \Quazymodo\Functions\getClientIp($request);

        // Create log data
        $logData = [
            "timestamp" => date('Y-m-d H:i:s'),
            "request" => [
                "method" => $request->getMethod(),
                "path" => $request->getUri()->getPath(),
                "query" => $request->getUri()->getQuery(),
                "ip" => $clientIp,
                "headers" => $request->getHeaders(),
                "body" => (string) $request->getBody()
                //"session" => $sessionData
            ],
            "response" => [
                "status_code" => $response->getStatusCode(),
                "status_message" => $response->getReasonPhrase(),
                "execution_time_ms" => $executionTime * 1000 // Convert to milliseconds
            ]
        ];

        // Convert log data to JSON
        $jsonLogData = json_encode($logData);

        // Directly write the JSON log data to the file without using Analog's prefix
        file_put_contents(__DIR__ . '/../writable/logs/requests.json', $jsonLogData . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
