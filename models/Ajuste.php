<?php 

require_once __DIR__ . '/../db/AccesoDatos.php';

class Ajuste{
    public $id;
    public $id_reserva;
    public $importe_inicial;
    public $importe_final;

    /**
     *  Crea un nuevo Ajuste en la base de datos.
     *  @return int El ID del nuevo registro.
     */
    public function PostNew()
    {
        // Obtiene una instancia de la clase AccesoDatos.
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        // Prepara la consulta
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO ajustes (id_reserva, importe_inicial, importe_final) 
            VALUES (:id_reserva, :importe_inicial, :importe_final)"
        );

        // Vincula los valores de los parámetros de la consulta.
        $consulta->bindValue(':id_reserva', $this->id_reserva, PDO::PARAM_STR);
        $consulta->bindValue(':importe_inicial', $this->importe_inicial, PDO::PARAM_STR);
        $consulta->bindValue(':importe_final', $this->importe_final, PDO::PARAM_STR);

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
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ajustes WHERE id = :id");

        // Vincula el ID en la consulta
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);

        try{
            // Ejecuta la consulta.
            $consulta->execute();
    
            // Obtiene el registro de la consulta como objeto Ajuste.
            $result = $consulta->fetchObject('Ajuste');

            // Muestra error en caso de no encontrar registro
            if($result === false){
                return json_encode(array("error" => "No se encontro Ajuste con ID {$id}"));
            }

            // Devuelve el registro encontrado
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
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ajustes");

            $consulta->execute();
    
            // Obtiene todos los Clientes de la consulta.
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Ajuste');
        }
        catch (PDOException $e){
            return json_encode(array('error' => 'Fallo la ejecucion de la consulta a la base de datos'));
        }
    }
}


?>