<?php

use Slim\Psr7\Response;


class AccessLogging
{
    public function __invoke($request, $handler)
    {
        $data = [
            'date' => time(),
            'method' => $request->getMethod(),
            'uri' => $request->getUri(),
            'ip' => $request->getRemoteAddress(),
            'user_agent' => $request->getHeaders()['User-Agent'] ?? null,
            'result' => $response->getStatusCode(),
        ];

        echo var_dump($data);

        return $handler->handle($request);
    }
}

?>