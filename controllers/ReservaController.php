<?php
require_once './models/Reserva.php';
require_once './models/Cliente.php';
require_once './models/Ajuste.php';

require_once './interfaces/IApiUsable.php';

class ReservaController extends Reserva implements IApiUsable
{
    /**
     * Crea una nueva Reserva en la base de datos.
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
        $tipo_cliente = isset($parametros['tipo_cliente']) ? $parametros['tipo_cliente'] : null;
        $nro_cliente = isset($parametros['nro_cliente']) ? $parametros['nro_cliente'] : null;
        $fecha_entrada = isset($parametros['fecha_entrada']) ? $parametros['fecha_entrada'] : null;
        $fecha_salida = isset($parametros['fecha_salida']) ? $parametros['fecha_salida'] : null;
        $tipo_habitacion = isset($parametros['tipo_habitacion']) ? $parametros['tipo_habitacion'] : null;
        $importe = isset($parametros['importe']) ? $parametros['importe'] : null;

        if( $nro_cliente !== '' && $nro_cliente !== null && 
            $fecha_entrada !== '' && $fecha_entrada !== null &&
            $fecha_salida !== '' && $fecha_salida !== null &&
            $tipo_habitacion !== '' && $tipo_habitacion !== null &&
            $importe !== '' && $importe !== null &&
            $tipo_cliente !== '' && $tipo_cliente !== null)
        {            
            // Crea un nuevo objeto Reserva.
            $reserva = new Reserva();
            $reserva->nro_cliente = $nro_cliente;
            $reserva->tipo_habitacion = $tipo_habitacion;
            $reserva->fecha_entrada = $fecha_entrada;
            $reserva->fecha_salida = $fecha_salida;
            $reserva->importe = $importe;

            // Crea la Reserva en la base de datos.
            $result = $reserva->PostNew();

            // Comprueba que el resultado sea un entero
            if(ctype_digit($result))
            {
                // Crea un mensaje de éxito en formato JSON.
                $payload = json_encode(array("mensaje" => "Reserva creada con exito", "id" => "{$result}"));

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
     * Obtiene una Reserva de la base de datos por ID
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

    public function TraerReservasPorCliente($request, $response, $args){

        // Obtiene los parámetros de la solicitud.
        $parametros = $request->getQueryParams();

        // Obtiene los valores de los parametros
        $canceladas = isset($parametros['canceladas']) ? $parametros['canceladas'] : null;
        $id = isset($args['id']) ? $args['id'] : null;

        if($id !== null && $id !== ''){
            $reservas = Reserva::GetAllByCliente($id, $canceladas);
            $result = [];
            if(is_array($reservas) && count($reservas) > 0 && is_a($reservas[0], 'Reserva')){
                foreach ($reservas as $reserva) {
                    if($reserva->nro_cliente == $id){
                        array_push($result, $reserva);
                    }
                }
            }
            else $payload = $reservas;

            if(count($result) > 0){
                $payload = json_encode(array("reservas" => $result));
            }
            else $payload = json_encode(array("error" => "No se encontraron reservas del cliente nro {$id}"));
        }
        // Establece el cuerpo de la respuesta
        $response->getBody()->write($payload);

        // Establece el encabezado Content-Type de la respuesta.
        $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Obtiene todos las Reservas de la base de datos.
     *
     * @param Request $request Objeto de solicitud HTTP.
     * @param Response $response Objeto de respuesta HTTP.
     * @param array $args Argumentos de la ruta.
     *
     * @return Response Objeto de respuesta HTTP con la lista de usuarios en formato JSON.
    */
    public function TraerTodos($request, $response, $args)
    {
        echo "ejecutando TraerTodos\n";
        // Obtiene los parámetros de la solicitud.
        $parametros = $request->getQueryParams();

        // Obtiene los valores de los parámetros
        $fecha_desde = isset($parametros['fecha_desde']) ? $parametros['fecha_desde'] . ' 00:00:00' : null;
        $fecha_hasta = isset($parametros['fecha_hasta']) ? $parametros['fecha_hasta'] . ' 23:59:59' : null;
        $tipo_habitacion = isset($parametros['tipo_habitacion']) ? $parametros['tipo_habitacion'] : null;
        $tipo_cliente = isset($parametros['tipo_cliente']) ? $parametros['tipo_cliente'] : null;
        $canceladas = isset($parametros['canceladas']) ? $parametros['canceladas'] : null;

        // Obtiene la lista de todos los usuarios de la base de datos.
        $reservas = Reserva::GetAll($fecha_desde, $fecha_hasta, $tipo_habitacion, $tipo_cliente, $canceladas);
        $payload = '';

        if(is_array($reservas)){
            if(count($reservas) > 0 && is_a($reservas[0], 'Reserva')){
                // Convierte la lista de usuarios a formato JSON.
                $payload = json_encode(array("reservas" => $reservas));                
            }
            else $payload = json_encode(array("error" => "no se econtraron registros"));
        }
        else $payload = $reservas;
                
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
        $id = isset($args['id']) ? $args['id'] : null;
        $causa = isset($parametros['causa']) ? $parametros['causa'] : null;
        $importe_final = isset($parametros['importe_final']) ? $parametros['importe_final'] : null;

        // Mensaje de respuesta
        $payload = '';

        if($id !== null && $id !== '' &&
           $causa !== null && $causa !== '' &&
           $importe_final !== null && $importe_final !== '')
        {
            // Obtiene la reserva por ID
            $reserva = Reserva::GetById($id);

            // Valida que sea una Reserva
            if($reserva instanceof Reserva){        
                // Crea un objeto Ajuste
                $ajuste = new Ajuste();
                $ajuste->id_reserva = $id;
                $ajuste->importe_inicial = $reserva->importe;
                $ajuste->importe_final = $importe_final;

                // Actualiza el importe de la Reserva
                $reserva->importe = $importe_final;

                // Carga el Ajuste en la bd
                $result = $ajuste->PostNew();

                if(ctype_digit($result)){
                    // Ajuste realizado con exito
                    $id_ajuste = $result;

                    $result = $reserva->Update();

                    if($result === true){
                        $payload = json_encode(array("mensaje" => "Ajuste realizado con exito.", "id_ajuste" => "{$id_ajuste}"));
                    }
                    else $payload = $result;
                }
                else $payload = $result;
            }
            else $payload = $reserva;
        }
        // Escribe la respuesta en el body de response
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
        $parametros = $request->getParsedBody();

        // Obtiene los valores de los parámetros
        $nro_cliente = isset($parametros['nro_cliente']) ? $parametros['nro_cliente'] : null;
        $tipo_cliente = isset($parametros['tipo_cliente']) ? $parametros['tipo_cliente'] : null;
        $id = isset($args['id']) ? $args['id'] : null;

        // Mensaje de respuesta
        $payload = '';

        // Valida que se haya enviado un id
        if($id !== null && $id !== ''){
            // Obtiene la reserva
            $reserva = Reserva::GetById($id);

            // Valida que sea una Reserva
            if($reserva instanceof Reserva){
                // Obtiene el cliente por ID
                $cliente = Cliente::GetById($nro_cliente);

                // Valida que sea un Cliente
                if($cliente instanceof Cliente){
                    // Valida que sea del tipo correcto
                    if($cliente->tipo_cliente == $tipo_cliente ){
                        // Valida que el cliete ingresado sea el propietario de la reserva
                        if($reserva->nro_cliente == $cliente->id){
                            $result = Reserva::DeleteById($id);
                            if($result === true){
                                $payload = json_encode(array("mensaje" => "Reserva cancelada con exito"));
                            }
                            else $payload = $result;
                        }
                        else $payload = json_encode(array("error" => "La Reserva no pertenece al Cliente"));
                    }
                    else $payload = json_encode(array("error" => "No se encontro usuario del tipo {$tipo_cliente} con ID {$nro_cliente}"));
                }
                else $payload = $cliente;
            }
            else $payload = $reserva;
        }
        else $payload = json_encode(array("error" => "falta parametro id"));

        // Establece el contenido de la respuesta en formato JSON.
        $response->getBody()->write($payload);

        // Establece el encabezado Content-Type de la respuesta.
        $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    public function TraerImportes($request, $response, $args)
    {
        // Obtiene los parámetros de la solicitud.
        $parametros = $request->getQueryParams();

        // Obtiene los valores de los parámetros
        $fecha_desde = isset($parametros['fecha_desde']) ? $parametros['fecha_desde'] . ' 00:00:00' : null;
        $fecha_hasta = isset($parametros['fecha_hasta']) ? $parametros['fecha_hasta'] . ' 23:59:59' : null;
        $tipo_cliente = isset($parametros['tipo_cliente']) ? $parametros['tipo_cliente'] : null;
        $canceladas = isset($parametros['canceladas']) ? $parametros['canceladas'] : null;

        // Obtiene la lista de todos los usuarios de la base de datos.
        $reservas = Reserva::GetAll($fecha_desde, $fecha_hasta, null, $tipo_cliente, $canceladas);
        $payload = '';

        if(is_array($reservas)){
            if(count($reservas) > 0 && is_a($reservas[0], 'Reserva')){
                $importe = 0;
                foreach ($reservas as $reserva) {
                    $importe += $reserva->importe;
                }

                // Escribe la respuesta en formato json
                $payload = json_encode(array("importe" => $importe, "nro_registros" => count($reservas)));                
            }
            else $payload = json_encode(array("error" => "no se econtraron registros"));
        }
        else $payload = $reservas;
                
        // Establece el contenido de la respuesta en formato JSON.
        $response->getBody()->write($payload);

        // Establece el encabezado Content-Type de la respuesta.
        $response->withHeader('Content-Type', 'application/json');
        
        return $response;
    }
}

?>