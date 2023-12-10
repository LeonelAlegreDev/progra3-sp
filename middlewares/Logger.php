<?php

require_once './models/Usuario.php';

class Logger
{
    public function __invoke($request, $handler){
        echo "logger";

        $parametros = $request->getQueryParams();
        $email = isset($parametros['email']) ? $parametros['email'] : null;
        $clave = isset($parametros['clave']) ? $parametros['clave'] : null;

        if ($email !== null && $clave !== null) {
            $usuario = Usuario::GetByCredentials($email, $clave);
            if($usuario instanceof Usuario){
                return $handler->handle($request);
            }
            else{
                throw new Exception("Email o clave incorrecto");
            }
        }
        else {
            throw new Exception("Falta ingresar email y clave");
        }
    }
}