<?php
namespace Controllers;

use Model\Usuario;
use Classes\Email;
use MVC\Router;

class LoginController{
    public static function login(Router $router){
        try{
            $alertas = [];
            if($_SERVER["REQUEST_METHOD"] === "POST"){
                $usuario = new Usuario($_POST);
                // validamos al usuario
                $alertas = $usuario->validarLogin();
                if(empty($alertas)){
                    $auth = Usuario::where("email", $usuario->email);
                    if($auth){
                        // Verificar el password
                        if($auth->comprobarPasswordAndVerificado($usuario->password)){
                            //autenticar al usuario
                            if(isset($_SESSION)){
                                session_start();
                            }
                            // Llenar el arreglo de $_SESSION
                            $_SESSION["id"] = $auth->id;
                            $_SESSION["nombre"] = "{$auth->nombre} {$auth->apellido}";
                            $_SESSION["email"] = $auth->email;
                            $_SESSION["login"] = true;
                            //redireccionamiento
                            if($auth->admin === "1"){
                                $_SESSION["admin"] = $auth->admin ?? null;
                                header("location: /index.php/admin");
                            } else{
                                header("location: /index.php/cita");
                            }
                        } else{
                            Usuario::setAlerta("error", "Usuario no encontrado");
                        }
                    }
                }          
            }
            $alertas = Usuario::getAlertas();
        } catch(\Throwable $th){
            echo $th;
        }
        $router->render("auth/login",[
            "alertas" => $alertas
        ]);
    }

    public static function logout(){
        session_start();
        $_SESSION = [];
        header("location: /");
    }
    public static function olvide(Router $router){
        $alertas = [];
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();
            if(empty($alertas)){
               $usuario = Usuario::where("email", $auth->email);
               if($usuario && $usuario->confirmado === "1"){
                    // Generar un nuevo token
                    $usuario->crearToken();
                    // Actualizamos el usuario en la base de datos
                    $usuario->guardar();
                    // Enviamos email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();
                    // Alerta exito
                    Usuario::setAlerta("exito", "Revisa tu email");
               } else{
                Usuario::setAlerta("error", "No existe o no confirmado");
               }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->render("auth/olvide-password",[
            "alertas" => $alertas
        ]);
    }
    public static function recuperar(Router $router){
        $alertas = [];
        $error = false;
        $token = s($_GET["token"]);
        // Buscar usuario por su token
        $usuario = Usuario::where("token", $token);
        if(empty($usuario)){
            Usuario::setAlerta("error", "Token No Valido");
            $error = true;
        }
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            // Leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();
            if(empty($alertas)){
                // Ponemos la contraseña del objeto en null
                $usuario->password = null;
                // Le asignamos la nueva contraseña al atributo password
                $usuario->password = $password->password;
                // Hasheamos el password
                $usuario->hashPassword();
                // Ponemos el token en null
                $usuario->token = null;
                //Actualizamos el objeto
                $resultado = $usuario->guardar();
                // Evaluamos si se actualizo correctamente
                if($resultado){
                    header("location: /index.php/");
                }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->render("auth/recuperar-password",[
            "alertas" => $alertas,
            "error" => $error
        ]);
       
    }
    public static function crear(Router $router){

        try{
            
            $usuario = new Usuario;
            // alertas vacias
            $alertas = [];
            if($_SERVER["REQUEST_METHOD"] === "POST"){
                $usuario->sincronizar($_POST["usuario"]);
                $alertas = $usuario->validarNuevaCuenta();
                // Revisar que alertas este vacio
                if(empty($alertas)){
                    //verificar que el usuario no este registrado
                    $query = $usuario->existeUsuario();
                   
                    if($query->num_rows){
                        $alertas = Usuario::getAlertas();
                    }else{
                        // No esta registrado
                        //hasheamos el password
                        $usuario->hashPassword();
                        
                        //generar un token unico
                        $usuario->crearToken();
                        // Enviar Email
                        $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                        $email->enviarConfirmacion();
                        
                        $resultado = $usuario->guardar();
                        if($resultado){
                            header("location: /index.php/mensaje");
                        }
    
                    }
                }
            }
        } catch(\Throwable $th){
            debuguear($th);
        }
        $router->render("auth/crear-cuenta",[
            "usuario" => $usuario,
            "alertas" => $alertas
        ]);

    }
    public static function mensaje(Router $router){
        $router->render("auth/mensaje");
    }

    public static function confirmar(Router $router){
        $alertas = [];
        $token = s($_GET["token"]);
        $usuario = Usuario::where("token", $token);

        if(empty($usuario)){
            // Mostrar mensaje de error
            Usuario::setAlerta("error", "Token no valido");
        } else{
            
            // Modificar a usuario confirmado
            // cambiamos el estado de confirmado de 0 a 1
            $usuario->confirmado = "1";
            // Eliminamos el token
            $usuario->token = null;
            // Actualizamos el registro
            $usuario->guardar();
            // Mensaje de exito
            Usuario::setAlerta("exito", "Cuenta Comprobada Correctamente");
        }
        $alertas = Usuario::getAlertas();
        $router->render("auth/confirmar-cuenta",[
            "alertas" => $alertas
        ]);
    }
}