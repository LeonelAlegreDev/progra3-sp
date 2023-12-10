<?php

use Slim\Psr7\Response;
require_once './models/Log.php';


class AccessLogging
{
    public function __invoke($request, $handler)
    {
        $log = new Log();
        $log->date = date('d-m-Y H:i:s', time());
        $log->method = $request->getMethod();
        $log->uri = $request->getUri();
        $log->ip = $request->getServerParams()['REMOTE_ADDR'];
        $log->user_agent = $request->getHeaderLine('User-Agent');

        try {
            $log->PostNew();
        } catch (Exeption $ex) {
            throw $ex->message;
        }
        return $handler->handle($request);
    }
}

?>
