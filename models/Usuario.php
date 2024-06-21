<?php
namespace Model;
class Usuario extends ActiveRecord{
    //base de datos
    protected static $tabla = "usuarios";
    protected static $columnasDB = ["id", "nombre", "apellido", "email", "password", "telefono", "admin", "confirmado", "token"];
    //atributos
    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;
    //constructor
    public function __construct($args = []){
        $this->id = $_POST["id"] ?? null;
        $this->nombre = $_POST["nombre"] ?? "";
        $this->apellido = $_POST["apellido"] ?? "";
        $this->email = $_POST["email"] ?? "";
        $this->password = $_POST["password"] ?? "";
        $this->telefono = $_POST["telefono"] ?? "";
        $this->admin = $_POST["admin"] ?? "0";
        $this->confirmado = $_POST["confirmado"] ?? "0";
        $this->token = $_POST["token"] ?? "";
    }
    // mensajes de validacion para la creacion de una cuenta
    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas["error"][] = "El Nombre es Obligatorio";
        }
        if(!$this->apellido){
            self::$alertas["error"][] = "El Apellido es Obligatorio";
        }
        if(!$this->telefono){
            self::$alertas["error"][] = "El Teléfono es Obligatorio";
        }
        if(!$this->email){
            self::$alertas["error"][] = "El Email es Obligatorio";
        }
        if(!$this->password){
            self::$alertas["error"][] = "El Password es Obligatorio";
        }
        if(strlen($this->password) < 6){
            self::$alertas["error"][] = "El Password debe tener al menos 6 caracteres";
        }
        return self::$alertas;
    }

    // Validar el Login
    public function validarLogin(){
        if(!$this->email){
            self::$alertas["error"][] = "El usuario es obligatorio";
        }
        if(!$this->password){
            self::$alertas["error"][] = "La contraseña es obligatorio";
        }
        return self::$alertas;
    }

    // Validar email
    public function validarEmail(){
        if(!$this->email){
            self::$alertas["error"][] = "El email es obligatorio";
        }
        return self::$alertas;
    }

    // Validar password
    public function validarPassword(){
        if(!$this->password){
            self::$alertas["error"][] = "La nueva contraseña es obligatoria";
        }
        if(strlen($this->password) < 6){
            self::$alertas["error"][] = "La nueva contraseña debe tener al menos 6 caracteres";
        }
        return self::$alertas;

    }

    // Revisar si el usuario existe
    public function existeUsuario(){
        $sql = "SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";
        $query = self::$db->query($sql);
        if($query->num_rows){
            self::$alertas["error"][] = "El Usuario ya esta registrado";
        }
        return $query;
    }

    // Hashear el password
    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
    }

    // Generar token unico
    public function crearToken(){
        $this->token = uniqid();
    }

    // Comprobar el password si esta correcto
    public function comprobarPasswordAndVerificado($password){
        $resultado = password_verify($password, $this->password);
        if(!$resultado || !$this->confirmado){
            self::$alertas["error"][] = "Password Incorrecto o tu cuenta no ha sido confirmada";
        }else{
            return true;
        }
    }
}