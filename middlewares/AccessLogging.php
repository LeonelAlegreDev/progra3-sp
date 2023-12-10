<?php

use Slim\Psr7\Response;


class AccessLogging
{
    public function __invoke($request, $handler)
    {
        $data = [
            'date' => time(),
            'method' => $request->getMethod(), // work
            'uri' => $request->getUri(),        // work
            'ip' => $request->getServerParams()['REMOTE_ADDR'],   // work
            'user_agent' => $request->getHeaderLine('User-Agent')   // work
        ];

        echo $data["date"] . "\n";
        echo $data["method"] ."\n";
        echo $data["uri"] . "\n";
        echo $data["ip"] . "\n";
        echo $data["user_agent"] . "\n";


        return $handler->handle($request);
    }
}

?>