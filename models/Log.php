<?php 

require_once __DIR__ . '/../db/AccesoDatos.php';

class Log{
    public $id;
    public $date;
    public $method;
    public $uri;
    public $ip;
    public $user_agent;

    /**
     *  Crea un nuevo Cliente en la base de datos.
     *  @return int El ID del nuevo registro.
     */
    public function PostNew()
    {
        // Obtiene una instancia de la clase AccesoDatos.
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        
        // Prepara la consulta
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO logs (date, method, uri, ip, user_agent) 
            VALUES (:date, :method, :uri, :ip, :user_agent)"
        );

        // Vincula los valores de los parámetros de la consulta.
        $consulta->bindValue(':date', $this->date, PDO::PARAM_STR);
        $consulta->bindValue(':method', $this->method, PDO::PARAM_STR);
        $consulta->bindValue(':uri', $this->uri, PDO::PARAM_STR);
        $consulta->bindValue(':ip', $this->ip, PDO::PARAM_STR);
        $consulta->bindValue(':user_agent', $this->user_agent, PDO::PARAM_STR);

        try{
            // Ejecuta la consulta.
            $consulta->execute();

            // Obtiene el ID del nuevo registro.
            return $objAccesoDatos->obtenerUltimoId();
        }
        catch (PDOException $e){
            return json_encode(array('error' => 'Fallo la ejecucion de la consulta a la base de datos'));
        }
    }
    

    public static function GetById($id)
    {
        // Obtiene una instancia de la clase AccesoDatos.
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        // Prepara la consulta
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM clientes WHERE id = :id");

        // Vincula el ID en la consulta
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);

        try{
            // Ejecuta la consulta.
            $consulta->execute();
    
            // Obtiene el registro de la consulta como objeto Bebida.
            $result = $consulta->fetchObject('Cliente');

            // Muestra error en caso de no encontrar registro
            if($result === false){
                return json_encode(array("error" => "No se encontro Cliente con ID {$id}"));
            }

            // Devuelve el Empleado encontrado
            return $result;
        }
        catch (PDOException $e){
            return json_encode(array('error' => 'Fallo la ejecucion de la consulta a la base de datos'));
        }
    }

    public static function GetByDoc($nro_documento, $tipo_doc)
    {
        // Obtiene una instancia de la clase AccesoDatos.
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        // Prepara la consulta
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM clientes WHERE nro_documento = :nro_documento AND tipo_doc = :tipo_doc");

        // Vincula los parametros
        $consulta->bindValue(':nro_documento', $nro_documento, PDO::PARAM_STR);
        $consulta->bindValue(':tipo_doc', $tipo_doc, PDO::PARAM_STR);

        try{
            // Ejecuta la consulta.
            $consulta->execute();
    
            // Obtiene el registro de la consulta como objeto Bebida.
            $result = $consulta->fetchObject('Cliente');

            // Muestra error en caso de no encontrar registro
            if($result === false){
                return json_encode(array("error" => "No se encontro Cliente"));
            }

            // Devuelve el Empleado encontrado
            return $result;
        }
        catch (PDOException $e){
            return json_encode(array('error' => 'Fallo la ejecucion de la consulta a la base de datos'));
        }
    }

    public function GetAll()
    {
      // Obtiene una instancia de la clase AccesoDatos.
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        try{
            // Preapara la consulta.
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM clientes");

            $consulta->execute();
    
            // Obtiene todos los Clientes de la consulta.
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Cliente');
        }
        catch (PDOException $e){
            return json_encode(array('error' => 'Fallo la ejecucion de la consulta a la base de datos'));
        }
    }

    /**
     *  Realiza una baja logica de un Cliente en la base de datos.
     *  
     *  @return bool Devuelve true si relizo la baja correctamente, o false en caso contrario.
     */
    public static function DeleteById($id, $tipo_cliente)
    {
        // Obtiene una instancia de la clase AccesoDatos.
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        // Prepara una la consulta
        $consulta = $objAccesoDato->prepararConsulta("UPDATE clientes SET fecha_baja = NOW() WHERE id = :id AND tipo_cliente = :tipo_cliente AND fecha_baja IS NULL");
        
        // Obtiene la fecha actual.
        $fecha = new DateTime(date("d-m-Y"));

        // Vincula los valores de los nuevos datos del usuario a las variables de parámetro.
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':tipo_cliente', $tipo_cliente, PDO::PARAM_INT);
        // $consulta->bindValue(':fecha_baja', date_format($fecha, 'Y-m-d H:i:s'));

        // Ejecuta la consulta.
        try{
            $consulta->execute();
            if($consulta->rowCount() > 0){
                // Devuelve true si la modificación se realizó correctamente.
                return true;
            }
            else return json_encode(array('error' => 'No se encontro el registro'));
        }
        catch (PDOException $e){
            return json_encode(array('error' => 'Fallo la ejecucion de la consulta a la base de datos'));
        }
    }

    /**
     *  Modifica los datos de un Cliente en la base de datos.
     *
     *  @param Cliente $cliente Objeto del tipo Cliente con valores actualizados
     *  @return bool Devuelve true si la modificación se realizó correctamente, o false en caso contrario.
     */
    public function Update($metodo_pago)
    {        
        // Obtiene una instancia de la clase AccesoDatos.
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        
        // Valida si se ingreso metodo de pago
        if($metodo_pago !== null){
            // Prepara la consulta
            $consulta = $objAccesoDato->prepararConsulta("UPDATE clientes SET nombre = :nombre, apellido = :apellido, tipo_doc = :tipo_doc, nro_documento = :nro_documento, email = :email, tipo_cliente = :tipo_cliente, pais = :pais, ciudad = :ciudad, telefono = :telefono, metodo_pago = :metodo_pago WHERE id = :id");
            
            // Vincula los valores de los nuevos datos del empleado
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
            $consulta->bindValue(':tipo_doc', $this->tipo_doc, PDO::PARAM_STR);
            $consulta->bindValue(':nro_documento', $this->nro_documento, PDO::PARAM_STR);
            $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
            $consulta->bindValue(':tipo_cliente', $this->tipo_cliente, PDO::PARAM_STR);
            $consulta->bindValue(':pais', $this->pais, PDO::PARAM_STR);
            $consulta->bindValue(':ciudad', $this->ciudad, PDO::PARAM_STR);
            $consulta->bindValue(':telefono', $this->telefono, PDO::PARAM_STR);
            $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
            $consulta->bindValue(':metodo_pago', $metodo_pago, PDO::PARAM_INT);
        }
        else{
            // Prepara la consulta
            $consulta = $objAccesoDato->prepararConsulta("UPDATE clientes SET nombre = :nombre, apellido = :apellido, tipo_doc = :tipo_doc, nro_documento = :nro_documento, email = :email, tipo_cliente = :tipo_cliente, pais = :pais, ciudad = :ciudad, telefono = :telefono WHERE id = :id");
            
            // Vincula los valores de los nuevos datos del empleado
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
            $consulta->bindValue(':tipo_doc', $this->tipo_doc, PDO::PARAM_STR);
            $consulta->bindValue(':nro_documento', $this->nro_documento, PDO::PARAM_STR);
            $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
            $consulta->bindValue(':tipo_cliente', $this->tipo_cliente, PDO::PARAM_STR);
            $consulta->bindValue(':pais', $this->pais, PDO::PARAM_STR);
            $consulta->bindValue(':ciudad', $this->ciudad, PDO::PARAM_STR);
            $consulta->bindValue(':telefono', $this->telefono, PDO::PARAM_STR);
            $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        }

        // Ejecuta la consulta.
        try{
            $consulta->execute();
            // Devuelve true si la modificación se realizó correctamente.
            return true;
        }
        catch (PDOException $e){
            // Guarda el mensaje del error
            $mensajeError = $e->getMessage();

            // Verifica el codigo de error
            if ($e->getCode() == 23000){
                // Valida si la columna nro_documento da error campo UNIQUE duplicado
                if (strpos($mensajeError, 'nro_documento') !== false) {
                    return json_encode(array('error' => "El numero de documento {$this->nro_documento} ya existe"));
                }
                else if(strpos($mensajeError, 'tipo_cliente') !== false){
                    // Valida si la columna tipo_cliente da error de clave foranea
                    return json_encode(array('error' => "El tipo de cliente {$this->tipo_cliente} no existe"));
                }
                else if(strpos($mensajeError, 'metodo_pago') !== false){
                    // Valida si la columna metodo_pago da error de clave foranea
                    return json_encode(array('error' => "El metodo de pago {$metodo_pago} no existe"));
                }
                else if(strpos($mensajeError, 'tipo_doc') !== false){
                    // Valida si la columna tipo_doc da error de clave foranea
                    return json_encode(array('error' => "El tipo de documento {$this->tipo_doc} no existe"));
                }
                else return json_encode(array('error' => 'Fallo la ejecucion de la consulta a la base de datos'));
            }
            else return json_encode(array('error' => 'Fallo la ejecucion de la consulta a la base de datos'));
        }
    }
}


?>