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

        echo $data["date"];
        echo $data["method"];
        echo $data["uri"];
        echo $data["ip"];
        echo $date["user_agent"];


        return $handler->handle($request);
    }
}

?>