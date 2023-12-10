<?php
require_once './models/Cliente.php';
require_once './interfaces/IApiUsable.php';

class ClienteController extends Cliente implements IApiUsable
{
    /**
     * Crea un nuevo Cliente en la base de datos.
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
        $nombre = isset($parametros['nombre']) ? $parametros['nombre'] : null;
        $apellido = isset($parametros['apellido']) ? $parametros['apellido'] : null;
        $tipo_doc = isset($parametros['tipo_doc']) ? $parametros['tipo_doc'] : null;
        $nro_doc = isset($parametros['nro_doc']) ? $parametros['nro_doc'] : null;
        $email = isset($parametros['email']) ? $parametros['email'] : null;
        $tipo_cliente = isset($parametros['tipo_cliente']) ? $parametros['tipo_cliente'] : null;
        $pais = isset($parametros['pais']) ? $parametros['pais'] : null;
        $ciudad = isset($parametros['ciudad']) ? $parametros['ciudad'] : null;
        $telefono = isset($parametros['telefono']) ? $parametros['telefono'] : null;
        $metodo_pago = isset($parametros['metodo_pago']) ? $parametros['metodo_pago'] : null;

        if( $nombre !== '' && $nombre !== null &&
            $apellido !== '' && $apellido !== null &&
            $tipo_doc !== '' && $tipo_doc !== null &&
            $nro_doc !== '' && $nro_doc !== null &&
            $email !== '' && $email !== null &&
            $tipo_cliente !== '' && $tipo_cliente !== null &&
            $pais !== '' && $pais !== null && 
            $ciudad !== '' && $ciudad !== null &&
            $telefono !== '' && $telefono !== null)
        {            
            // Crea un nuevo objeto Cliente.
            $cliente = new Cliente();
            $cliente->nombre = $nombre;
            $cliente->apellido = $apellido;
            $cliente->tipo_doc = $tipo_doc;
            $cliente->nro_documento = $nro_doc;
            $cliente->email = $email;
            $cliente->tipo_cliente = $tipo_cliente;
            $cliente->telefono = $telefono;
            $cliente->pais = $pais;
            $cliente->ciudad = $ciudad;
            $cliente->metodo_pago = $metodo_pago;

            // Crea el Cliente en la base de datos.
            $result = $cliente->PostNew();

            // Comprueba que el resultado sea un entero
            if(ctype_digit($result))
            {
                // Crea un mensaje de éxito en formato JSON.
                $payload = json_encode(array("mensaje" => "Cliente creado con exito", "id" => "{$result}"));

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
     * Obtiene un Cliente de la base de datos por ID
     *
     * @param Request $request Objeto de solicitud HTTP.
     * @param Response $response Objeto de respuesta HTTP.
     * @param array $args Argumentos de la ruta.
     *
     * @return Response Objeto de respuesta HTTP con el usuario solicitado en formato JSON.
     */
    public function TraerUno($request, $response, $args)
    {
        // Obtiene los parámetros de la solicitud.
        $parametros = $request->getParsedBody();

        // Obtiene los valores de los parámetros
        $nro_cliente = isset($args['id']) ? $args['id'] : null;
        $tipo = isset($parametros['tipo']) ? $parametros['tipo'] : null;

        if($nro_cliente !== null && $nro_cliente !== '' && 
           $tipo !== null && $tipo !== '')
        {
            // Obtiene el usuario de la base de datos por su nombre de usuario.
            $cliente = Cliente::GetById($nro_cliente);

            if($cliente instanceof Cliente){
                if($cliente->tipo_cliente == $tipo){
                    $payload = [
                        'pais' => $cliente->pais,
                        'ciudad' => $cliente->ciudad,
                        'telefono' => $cliente->telefono,
                      ];
                    // Convierte el usuario a formato JSON.
                    $payload = json_encode($payload);

                    // Establece el contenido de la respuesta en formato JSON.
                    $response->getBody()->write($payload);
                }
                else {
                    $payload = json_encode(array("error" => "No se encontro cliente con nro_cliente {$nro_cliente} y tipo {$tipo}"));

                    $response->getBody()->write($payload);
                }
            }
            else $response->getBody()->write($cliente);
        }
        else {
            $payload = json_encode(array("error" => "Faltan parametros"));

            $response->getBody()->write($payload);
        }
        
        // Establece el encabezado Content-Type de la respuesta.
        $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Obtiene todos los Clientes de la base de datos.
     *
     * @param Request $request Objeto de solicitud HTTP.
     * @param Response $response Objeto de respuesta HTTP.
     * @param array $args Argumentos de la ruta.
     *
     * @return Response Objeto de respuesta HTTP con la lista de usuarios en formato JSON.
    */
    public function TraerTodos($request, $response, $args)
    {
        // Obtiene la lista de todos los usuarios de la base de datos.
        $clientes = Cliente::GetAll();
        $payload = '';

        if(is_array($clientes) && is_a($clientes[0], 'Cliente')){
            // Convierte la lista de usuarios a formato JSON.
            $payload = json_encode(array("clientes" => $clientes));
        }
        else {
            $payload = $clientes;
        }
        
        // Establece el contenido de la respuesta en formato JSON.
        $response->getBody()->write($payload);

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
        // Obtiene los parámetros de la solicitud.
        $parametros = $request->getParsedBody();

        // Obtiene los valores de los parámetros
        $nombre = isset($parametros['nombre']) ? $parametros['nombre'] : null;
        $apellido = isset($parametros['apellido']) ? $parametros['apellido'] : null;
        $tipo_doc = isset($parametros['tipo_doc']) ? $parametros['tipo_doc'] : null;
        $nro_doc = isset($parametros['nro_doc']) ? $parametros['nro_doc'] : null;
        $email = isset($parametros['email']) ? $parametros['email'] : null;
        $tipo_cliente = isset($parametros['tipo_cliente']) ? $parametros['tipo_cliente'] : null;
        $pais = isset($parametros['pais']) ? $parametros['pais'] : null;
        $ciudad = isset($parametros['ciudad']) ? $parametros['ciudad'] : null;
        $telefono = isset($parametros['telefono']) ? $parametros['telefono'] : null;
        $id = isset($parametros['id']) ? $parametros['id'] : null;
        $metodo_pago = isset($parametros['metodo_pago']) ? $parametros['metodo_pago'] : null;

        // Mensaje de respuesta
        $payload = '';

        if( $nombre !== '' && $nombre !== null &&
            $apellido !== '' && $apellido !== null &&
            $tipo_doc !== '' && $tipo_doc !== null &&
            $nro_doc !== '' && $nro_doc !== null &&
            $email !== '' && $email !== null &&
            $tipo_cliente !== '' && $tipo_cliente !== null &&
            $pais !== '' && $pais !== null && 
            $ciudad !== '' && $ciudad !== null &&
            $telefono !== '' && $telefono !== null &&
            $id !== null)
        {            
            // Obtiene el Cliente por ID
            $cliente = Cliente::GetById($id);
            if($cliente instanceof Cliente){
                $cliente->nombre = $nombre;
                $cliente->apellido = $apellido;
                $cliente->tipo_doc = $tipo_doc;
                $cliente->nro_documento = $nro_doc;
                $cliente->email = $email;
                $cliente->tipo_cliente = $tipo_cliente;
                $cliente->telefono = $telefono;
                $cliente->pais = $pais;
                $cliente->ciudad = $ciudad;
                $result = $cliente->Update($metodo_pago);

                // Comprueba que el resultado sea true
                if($result === true)
                {
                    // Crea un mensaje de éxito en formato JSON.
                    $payload = json_encode(array("mensaje" => "Cliente modificado con exito"));
                }
                else $payload = $result;
                
            }
            else $payload = $cliente;
        }
        else $payload = json_encode(array("error" => "faltan parametros"));
        
        // Establece el contenido de la respuesta en formato JSON.
        $response->getBody()->write($payload);
        
        // Establece el encabezado Content-Type de la respuesta.
        $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Elimina un Cliente de la base de datos por su ID.
     *
     * @param Request $request Objeto de solicitud HTTP.
     * @param Response $response Objeto de respuesta HTTP.
     * @param array $args Argumentos de la ruta.
     *
     * @return Response Objeto de respuesta HTTP con el mensaje de éxito en formato JSON.
    */
    public function BorrarUno($request, $response, $args)
    {
        // Obtiene los parámetros de la solicitud.
        $parametros = $request->getQueryParams();
        
        // Obtiene los valores de los parametros
        $id = isset($args['id']) ? $args['id'] : null;
        $tipo_cliente = isset($parametros['tipo_cliente']) ? $parametros['tipo_cliente'] : null;

        // Mensaje de respuesta
        $payload = '';

        // Valida que se haya enviado un id
        if($id !== null && $id !== '' &&
           $tipo_cliente !== null && $tipo_cliente !== '')
        {
          // Elimina el usuario de la base de datos.
          $result = Cliente::DeleteById($id, $tipo_cliente); 
          
          if($result === true){
            // Crea un mensaje de éxito en formato JSON.
            $payload = json_encode(array("mensaje" => "Cliente dado de baja con exito"));
          }
          else $payload = $result;
        }
        else $payload = json_encode(array("error" => "faltan parametros"));

        // Escribe el mensaje en el cuerpo de la respuesta
        $response->getBody()->write($payload);
        
        // Establece el encabezado Content-Type de la respuesta.
        $response->withHeader('Content-Type', 'application/json');

        return $response;
    }
}

?>