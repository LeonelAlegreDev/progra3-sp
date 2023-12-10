<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';
require_once './utils/AutentificadorJWT.php';

class UsuarioController extends Usuario implements IApiUsable
{
    /**
     * Crea un nuevo Usuario en la base de datos.
     *
     * @param Request $request Objeto de solicitud HTTP.
     * @param Response $response Objeto de respuesta HTTP.
     * @param array $args Argumentos de la ruta.
     *
     * @return Response Objeto de respuesta HTTP con el mensaje de éxito.
    */
    public function CargarUno($request, $response, $args)
    {
        // Obtiene los parámetros de la solicitud.
        $parametros = $request->getParsedBody();

        // Obtiene los valores de los parámetros
        $email = isset($parametros['email']) ? $parametros['email'] : null;
        $clave = isset($parametros['clave']) ? $parametros['clave'] : null;
        $rol = isset($parametros['rol']) ? $parametros['rol'] : null;

        if( $email !== '' && $email !== null &&
            $clave !== '' && $clave !== null &&
            $rol !== '' && $rol !== null)
        {            
            // Crea un nuevo objeto Cliente.
            $usuario = new Usuario();
            $usuario->email = $email;
            $usuario->clave = $clave;
            $usuario->rol = $rol;


            // Crea el Cliente en la base de datos.
            $result = $usuario->PostNew();

            // Comprueba que el resultado sea un entero
            if(ctype_digit($result))
            {
                // Crea un mensaje de éxito en formato JSON.
                $payload = json_encode(array("mensaje" => "Usuario creado con exito", "id" => "{$result}"));

                // Establece el contenido de la respuesta en formato JSON.
                $response->getBody()->write($payload);
            }
            else{
                $response->getBody()->write($result);
            }
        }
        else{
          // Crea un mensaje de éxito en formato JSON.
          $payload = json_encode(array("error" => "faltan parametros"));

          // Establece el contenido de la respuesta en formato JSON.
          $response->getBody()->write($payload);
        }

        // Establece el encabezado Content-Type de la respuesta.
        $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Obtiene un Usuario de la base de datos por ID
     *
     * @param Request $request Objeto de solicitud HTTP.
     * @param Response $response Objeto de respuesta HTTP.
     * @param array $args Argumentos de la ruta.
     *
     * @return Response Objeto de respuesta HTTP con el usuario solicitado en formato JSON.
     */
    public function TraerUno($request, $response, $args)
    {
        // Establece el encabezado Content-Type de la respuesta.
        $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Obtiene todos los Usuarios de la base de datos.
     *
     * @param Request $request Objeto de solicitud HTTP.
     * @param Response $response Objeto de respuesta HTTP.
     * @param array $args Argumentos de la ruta.
     *
     * @return Response Objeto de respuesta HTTP con la lista de usuarios en formato JSON.
    */
    public function TraerTodos($request, $response, $args)
    {
        // Establece el encabezado Content-Type de la respuesta.
        $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }
    
    /**
     * Modifica un Cliente en la base de datos por ID
     *
     * @param Request $request Objeto de solicitud HTTP.
     * @param Response $response Objeto de respuesta HTTP.
     * @param array $args Argumentos de la ruta.
     *
     * @return Response Objeto de respuesta HTTP con el mensaje de éxito en formato JSON.
    */
    public function ModificarUno($request, $response, $args)
    {   
        // Establece el encabezado Content-Type de la respuesta.
        $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Elimina un Usuario de la base de datos por su ID.
     *
     * @param Request $request Objeto de solicitud HTTP.
     * @param Response $response Objeto de respuesta HTTP.
     * @param array $args Argumentos de la ruta.
     *
     * @return Response Objeto de respuesta HTTP con el mensaje de éxito en formato JSON.
    */
    public function BorrarUno($request, $response, $args)
    {
        // Establece el encabezado Content-Type de la respuesta.
        $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    public function Login($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $email = isset($parametros['email']) ? $parametros['email'] : null;
        $clave = isset($parametros['clave']) ? $parametros['clave'] : null;

        $payload = '';
        if ($email !== null && $clave !== null) {
            $usuario = Usuario::GetByCredentials($email, $clave);
            if($usuario instanceof Usuario){
                $datos = array('email' => $usuario->email,'clave' => $usuario->clave, 'rol' => $usuario->rol);

                $token= AutentificadorJWT::CrearToken($datos); 

                $payload = json_encode(array("mensaje" => "Usuario ingreso con exito", "token" => $token));
            }
            else $payload = json_encode(array("error" => "Email o clave incorrecta"));
        }
        else $payload = json_encode(array("error" => "Falta enviar email y clave"));

        // Establece el contenido de la respuesta en formato JSON.
        $response->getBody()->write($payload);

        // Establece el encabezado Content-Type de la respuesta.
        $response->withHeader('Content-Type', 'application/json');

        return $response;
    }
}

?>