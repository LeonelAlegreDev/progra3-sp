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
            VALUES (:date, STR_TO_DATE(:method, %d-%m-%Y %H:%i:%s), :uri, :ip, :user_agent)"
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
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM logs WHERE id = :id");

        // Vincula el ID en la consulta
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);

        try{
            // Ejecuta la consulta.
            $consulta->execute();
    
            // Obtiene el registro de la consulta como objeto Bebida.
            $result = $consulta->fetchObject('Log');

            // Muestra error en caso de no encontrar registro
            if($result === false){
                return json_encode(array("error" => "No se encontro Log con ID {$id}"));
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
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM logs");

            $consulta->execute();
    
            // Obtiene todos los Log de la consulta.
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Logs');
        }
        catch (PDOException $e){
            return json_encode(array('error' => 'Fallo la ejecucion de la consulta a la base de datos'));
        }
    }
}


?>