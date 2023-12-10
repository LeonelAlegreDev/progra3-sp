<?php 

require_once __DIR__ . '/../db/AccesoDatos.php';

class Usuario{
    public $id;
    public $email;
    public $clave;
    public $rol;


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
            "INSERT INTO Usuarios (email, clave, rol) 
                VALUES (:email, :clave, :rol)"
            );

        // Vincula los valores de los parámetros de la consulta.
        $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':rol', $this->rol, PDO::PARAM_STR);
        
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
    
    public static function GetByCredentials($email, $clave)
    {
        // Obtiene una instancia de la clase AccesoDatos.
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        // Prepara la consulta
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE email = :email AND clave = :clave");

        // Vincula el ID en la consulta
        $consulta->bindValue(':email', $email, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);

        try{
            // Ejecuta la consulta.
            $consulta->execute();
    
            // Obtiene el registro de la consulta como objeto
            $result = $consulta->fetchObject('Usuario');

            // Muestra error en caso de no encontrar registro
            if($result === false){
                return json_encode(array("error" => "No se encontro Usuario con emai {$email}"));
            }

            // Devuelve el Empleado encontrado
            return $result;
        }
        catch (PDOException $e){
            return json_encode(array('error' => 'Fallo la ejecucion de la consulta a la base de datos'));
        }
    }
}


?>