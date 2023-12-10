<?php 

require_once __DIR__ . '/../db/AccesoDatos.php';

class Transaccion{
    public $id;
    public $date;
    public $usuario;
    public $uri;

    /**
     *  Crea una nueva Transaccion en la base de datos.
     *  @return int El ID del nuevo registro.
     */
    public function PostNew()
    {
        // Obtiene una instancia de la clase AccesoDatos.
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        
        // Prepara la consulta
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO transacciones (date, usuario, uri) 
            VALUES (STR_TO_DATE(:date, '%d-%m-%Y %H:%i:%s'), :usuario, :uri)"
        );

        // Vincula los valores de los parámetros de la consulta.
        $consulta->bindValue(':date', $this->date, PDO::PARAM_STR);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':uri', $this->uri, PDO::PARAM_STR);

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
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM transacciones WHERE id = :id");

        // Vincula el ID en la consulta
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);

        try{
            // Ejecuta la consulta.
            $consulta->execute();
    
            // Obtiene el registro de la consulta como objeto Bebida.
            $result = $consulta->fetchObject('Transaccion');

            // Muestra error en caso de no encontrar registro
            if($result === false){
                return json_encode(array("error" => "No se encontro Transaccion con ID {$id}"));
            }

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
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM transacciones");

            $consulta->execute();
    
            // Obtiene todos los Log de la consulta.
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Transaccion');
        }
        catch (PDOException $e){
            return json_encode(array('error' => 'Fallo la ejecucion de la consulta a la base de datos'));
        }
    }
}


?>