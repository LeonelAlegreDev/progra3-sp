<?php

use Slim\Psr7\Response;
require_once './models/Log.php';


class LogTransaccion
{
    public function __invoke($request, $handler)
    {
        $log = new LogTransaccion();
        $log->date = date('d-m-Y H:i:s', time());
        $log->uri = $request->getUri();

        try {
            echo "ejecutando LogTransaccion\n";
        } catch (Exeption $ex) {
            throw $ex->message;
        }
        return $handler->handle($request);
    }
}

?>