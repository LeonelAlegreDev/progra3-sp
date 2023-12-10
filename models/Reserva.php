<?php 

require_once __DIR__ . '/../db/AccesoDatos.php';

class Reserva{
    public $id;
    public $nro_cliente;
    public $fecha_entrada;
    public $fecha_salida;
    public $tipo_habitacion;
    public $importe;
    public $fecha_baja;

    /**
     *  Crea una nueva Reserva en la base de datos.
     *  @return int El ID del nuevo registro.
     */
    public function PostNew()
    {
        // Obtiene una instancia de la clase AccesoDatos.
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        // Prepara la consulta
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO reservas (nro_cliente, fecha_entrada, fecha_salida, tipo_habitacion, importe) 
            VALUES (:nro_cliente,  STR_TO_DATE(:fecha_entrada, '%d/%m/%Y %H:%i:%s'), STR_TO_DATE(:fecha_salida, '%d/%m/%Y %H:%i:%s'), :tipo_habitacion, :importe)"
        );

        // Vincula los valores de los parametros de la consulta.
        $consulta->bindValue(':nro_cliente', $this->nro_cliente, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_entrada', $this->fecha_entrada, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_salida', $this->fecha_salida, PDO::PARAM_STR);
        $consulta->bindValue(':tipo_habitacion', $this->tipo_habitacion, PDO::PARAM_STR);
        $consulta->bindValue(':importe', $this->importe, PDO::PARAM_STR);

        try{
            // Ejecuta la consulta.
            $consulta->execute();

            // Obtiene el ID del nuevo registro.
            return $objAccesoDatos->obtenerUltimoId();
        }
        catch (PDOException $e){
            if ($e->getCode() == '23000') {
                return json_encode(array('error' => 'El nro_cliente ' . $this->nro_cliente . ' no existe en la base de datos'));
            } else {
                return json_encode(array('error' => 'Fallo la ejecucion de la consulta a la base de datos'));
            }
        }
    }

    public function GetAllByCliente($id_cliente, $canceladas){
        // Obtiene una instancia de la clase AccesoDatos.
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        // Valida si se filtra por reservas canceladas
        if($canceladas === 'true'){
            // Se realiza consulta sin filtros
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM reservas WHERE nro_cliente = :nro_cliente AND  fecha_baja IS NOT NULL");
            
            // Vincula los valores de los parametros de la consulta.
            $consulta->bindValue(':nro_cliente', $id_cliente, PDO::PARAM_STR);
        }
        else if($canceladas === 'false'){
            // Se realiza consulta sin filtros
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM reservas WHERE nro_cliente = :nro_cliente AND  fecha_baja IS NULL");
            
            // Vincula los valores de los parametros de la consulta.
            $consulta->bindValue(':nro_cliente', $id_cliente, PDO::PARAM_STR);
        }
        else{
            // Se realiza consulta sin filtros
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM reservas WHERE nro_cliente = :nro_cliente");
            
            // Vincula los valores de los parametros de la consulta.
            $consulta->bindValue(':nro_cliente', $id_cliente, PDO::PARAM_STR);
        }

        try{
            $consulta->execute();
    
            // Obtiene todos los Clientes de la consulta.
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
        }
        catch (PDOException $e){
            return json_encode(array('error' => 'Fallo la ejecucion de la consulta a la base de datos'));
        }
    }
    public function GetAll($fecha_desde, $fecha_hasta, $tipo_habitacion, $tipo_cliente, $canceladas)
    {
        // Obtiene una instancia de la clase AccesoDatos.
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        // Construye la cadena de consulta.
        $consulta = "SELECT * FROM reservas";

        // Bandera para detectar si se agregó WHERE.
        $where = false;

        // Filtra entre fechas
        if ($fecha_desde !== null && $fecha_hasta !== null) {
            $consulta .= $where ? " AND " : " WHERE ";
            $consulta .= " fecha_entrada >= STR_TO_DATE('{$fecha_desde}', '%d/%m/%Y %H:%i:%s') AND fecha_salida <= STR_TO_DATE('{$fecha_hasta}', '%d/%m/%Y %H:%i:%s')";
            $where = true;
        }
        // Fitra por tipo de habitacion
        if ($tipo_habitacion !== null) {
            $consulta .= $where ? " AND " : " WHERE ";
            $consulta .= " tipo_habitacion = '{$tipo_habitacion}'";
            $where = true;
        }
        // Filtra por reservas canceladas
        if ($canceladas !== null) {
            if($canceladas === 'true'){
                $consulta .= $where ? " AND " : " WHERE ";
                $consulta .= " fecha_baja IS NOT NULL";
                $where = true;
            }
            else if($canceladas === 'false'){
                $consulta .= $where ? " AND " : " WHERE ";
                $consulta .= " fecha_baja IS NULL";
                $where = true;
            }
        }
        // Filtra por tipo_cliente
        if ($tipo_cliente !== null) {
            $consulta .= $where ? " AND " : " WHERE ";
            $consulta .= " reservas.nro_cliente IN (SELECT id FROM clientes WHERE tipo_cliente = '{$tipo_cliente}')";
            $where = true;
        }

        // Perpara la consulta
        $consulta = $objAccesoDatos->prepararConsulta($consulta);

        try{
            $consulta->execute();
    
            // Obtiene todos los Clientes de la consulta.
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
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
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM reservas WHERE id = :id");

        // Vincula el ID en la consulta
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);

        try{
            // Ejecuta la consulta.
            $consulta->execute();
    
            // Obtiene el registro de la consulta como objeto Bebida.
            $result = $consulta->fetchObject('Reserva');

            // Muestra error en caso de no encontrar registro
            if($result === false){
                return json_encode(array("error" => "No se encontro Reserva con ID {$id}"));
            }

            // Devuelve el Empleado encontrado
            return $result;
        }
        catch (PDOException $e){
            return json_encode(array('error' => 'Fallo la ejecucion de la consulta a la base de datos'));
        }
    }

    /**
     *  Realiza una baja logica de una Reserva en la base de datos.
     *  
     *  @return bool Devuelve true si relizo la baja correctamente, o false en caso contrario.
     */
    public static function DeleteById($id)
    {
        // Obtiene una instancia de la clase AccesoDatos.
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        // Prepara una la consulta
        $consulta = $objAccesoDato->prepararConsulta("UPDATE reservas SET fecha_baja = CURRENT_TIMESTAMP WHERE id = :id AND fecha_baja IS NULL");
        
        // Vincula los valores de los nuevos datos del usuario a las variables de parámetro.
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);

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
     *  Modifica los datos de una Reserva en la base de datos.
     *
     *  @return bool Devuelve true si la modificación se realizó correctamente, o false en caso contrario.
     */
    public function Update()
    {        
        // Obtiene una instancia de la clase AccesoDatos.
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        
        // Prepara la consulta
        $consulta = $objAccesoDato->prepararConsulta("UPDATE reservas SET importe = :importe WHERE id = :id");
        
        // Vincula los valores de los nuevos datos del empleado
        $consulta->bindValue(':importe', $this->importe, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        
        // Ejecuta la consulta.
        try{
            $consulta->execute();
            // Devuelve true si la modificación se realizó correctamente.
            return true;
        }
        catch (PDOException $e){
            return json_encode(array('error' => 'Fallo la ejecucion de la consulta a la base de datos'));
        }
    }
}

?>