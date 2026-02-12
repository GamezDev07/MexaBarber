<?php
namespace Model;

use PDO;

class ActiveRecord {

    // Base DE DATOS
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];

    // Alertas y Mensajes
    protected static $alertas = [];
    
    // Definir la conexión a la BD - includes/database.php
    public static function setDB($database) {
        self::$db = $database;
    }

    public static function setAlerta($tipo, $mensaje) {
        static::$alertas[$tipo][] = $mensaje;
    }

    // Validación
    public static function getAlertas() {
        return static::$alertas;
    }

    public function validar() {
        static::$alertas = [];
        return static::$alertas;
    }

    // Consulta SQL para crear un objeto en Memoria
    public static function consultarSQL($query) {
        // Consultar la base de datos
        $stmt = self::$db->prepare($query);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Iterar los resultados
        $array = [];
        foreach($resultado as $registro) {
            $array[] = static::crearObjeto($registro);
        }

        // retornar los resultados
        return $array;
    }

    // Crea el objeto en memoria que es igual al de la BD
    protected static function crearObjeto($registro) {
        $objeto = new static;

        foreach($registro as $key => $value ) {
            if(property_exists( $objeto, $key  )) {
                $objeto->$key = $value;
            }
        }

        return $objeto;
    }

    // Identificar y unir los atributos de la BD
    public function atributos() {
        $atributos = [];
        foreach(static::$columnasDB as $columna) {
            if($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    // Sanitizar los datos antes de guardarlos en la BD
    public function sanitizarAtributos() {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach($atributos as $key => $value ) {
            $sanitizado[$key] = $value; // PDO/Prepared statements maneja escaping
        }
        return $sanitizado;
    }

    // Sincroniza BD con Objetos en memoria
    public function sincronizar($args=[]) { 
        foreach($args as $key => $value) {
          if(property_exists($this, $key) && !is_null($value)) {
            $this->$key = $value;
          }
        }
    }

    // Registros - CRUD
    public function guardar() {
        $resultado = '';
        if(!is_null($this->id)) {
            // actualizar
            $resultado = $this->actualizar();
        } else {
            // Creando un nuevo registro
            $resultado = $this->crear();
        }
        return $resultado;
    }

    // Todos los registros
    public static function all() {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Busca un registro por su id
    public static function find($id) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->execute([$id]);
        $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if($resultado) {
            return static::crearObjeto($resultado);
        }
        return null;
    }

    // Obtener Registros con cierta cantidad
    public static function get($limite) {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT ?";
        $stmt = self::$db->prepare($query);
        $stmt->bindParam(1, $limite, \PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $resultado ? static::crearObjeto($resultado) : null;
    }

    // Busca un registro por un campo específico
    public static function where($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE {$columna} = ?";
        $stmt = self::$db->prepare($query);
        $stmt->execute([$valor]);
        $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if($resultado) {
            return static::crearObjeto($resultado);
        }
        return null;
    }

    // Consulta Plana de SQL (Utilizar cuando los métodos del modelo no son suficientes)
    public static function SQL($query) {
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // crea un nuevo registro
    public function crear() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Construir valores con placeholders
        $campos = array_keys($atributos);
        $placeholders = array_fill(0, count($campos), '?');
        
        // Insertar en la base de datos
        $query = "INSERT INTO " . static::$tabla . " (" . join(', ', $campos) . ") VALUES (" . join(', ', $placeholders) . ")";
        
        try {
            // Ejecutar con prepared statement
            $stmt = self::$db->prepare($query);
            $resultado = $stmt->execute(array_values($atributos));
            
            return [
               'resultado' => $resultado,
               'id' => self::$db->lastInsertId()
            ];
        } catch (\PDOException $e) {
            // Manejar errores de base de datos
            if(strpos($e->getMessage(), 'duplicate') !== false) {
                self::$alertas['error'][] = 'Este registro ya existe en la base de datos';
            } else {
                self::$alertas['error'][] = 'Error al guardar: ' . $e->getMessage();
            }
            return [
               'resultado' => false,
               'id' => null
            ];
        }
    }

    // Actualizar el registro
    public function actualizar() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Construir SET clause con placeholders
        $valores = [];
        $valores_bind = [];
        foreach($atributos as $key => $value) {
            $valores[] = "{$key} = ?";
            $valores_bind[] = $value;
        }
        
        // Agregar id al final para WHERE
        $valores_bind[] = $this->id;

        // Consulta SQL
        $query = "UPDATE " . static::$tabla . " SET " . join(', ', $valores) . " WHERE id = ? LIMIT 1";
        
        try {
            // Ejecutar con prepared statement
            $stmt = self::$db->prepare($query);
            $resultado = $stmt->execute($valores_bind);
            return $resultado;
        } catch (\PDOException $e) {
            // Manejar errores de base de datos
            self::$alertas['error'][] = 'Error al actualizar: ' . $e->getMessage();
            return false;
        }
    }

    // Eliminar un Registro por su ID
    public function eliminar() {
        $query = "DELETE FROM " . static::$tabla . " WHERE id = ? LIMIT 1";
        try {
            $stmt = self::$db->prepare($query);
            $resultado = $stmt->execute([$this->id]);
            return $resultado;
        } catch (\PDOException $e) {
            // Manejar errores de base de datos
            self::$alertas['error'][] = 'Error al eliminar: ' . $e->getMessage();
            return false;
        }
    }

}