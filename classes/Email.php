<?php
namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email{
    public $email;
    public $nombre;
    public $token;
    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }
    public function enviarConfirmacion(){
        try{
            //crear el objeto de email
            $email = new PHPMailer();
            // Configurar SMTP
            $email->isSMTP();
            $email->Host = $_ENV["EMAIL_HOST"];
            $email->SMTPAuth = true;
            $email->Port = $_ENV["EMAIL_PORT"];
            $email->Username = $_ENV["EMAIL_USER"];
            $email->Password = $_ENV["EMAIL_PASS"];
            $email->SMTPSecure = "ssl";
            // Configurar el contenido del email
            $email->setFrom("cuentas@appsalon.com");
            $email->addAddress("joel.adcascar@gmail.com");
            $email->Subject = "Confirma tu cuenta";
            // Habilitar HTML
            $email->isHTML(TRUE);
            $email->CharSet = "UTF-8";
            // Definimos el contenido
            $contenido = "<html>";
            $contenido .= "<p>Hola <strong> {$this->nombre} </strong> Has creado tu cuenta en App Salon, solo debes confirmarla presionando el siguiente enlace</p>";
            $contenido .= "<p>Presiona aquí: <a href=\"{$_ENV["APP_URL"]}/confirmar-cuenta?token={$this->token}\">Confirmar Cuenta</a></p>";
            $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>";
            $contenido .= "</html>";
            $email->Body = $contenido;
            // Enviar el email
            $email->send();
        }catch(\Throwable $th){
            debuguear($th);
        }

    }

    public function enviarInstrucciones(){
        try{
            // Crear el objeto de email
            $email = new PHPMailer();
            // Configurar SMTP
            $email->isSMTP();
            $email->Host = $_ENV["EMAIL_HOST"];
            $email->SMTPAuth = true;
            $email->Port = $_ENV["EMAIL_PORT"];
            $email->Username = $_ENV["EMAIL_USER"];
            $email->Password = $_ENV["EMAIL_PASS"];
            $email->SMTPSecure = "tls";
            // Configurar el contenido del email
            $email->setFrom("cuentas@appsalon.com");
            $email->addAddress("cuentas@appsalon.com", "appSalon.com");
            $email->Subject = "Reestablece tu contraseña";
            // Habilitar HTML
            $email->isHTML(true);
            $email->CharSet = "UTF-8";
            // Definimos el contenido
            $contenido = "<html>";
            $contenido .= "<p>Hola<strong>{$this->nombre}</strong> Has solicitado reestablecer tu contraseña, sigue el siguiente enlace para hacerlo</p>";
            $contenido .= "<p>Presiona aqui: <a href=\"{$_ENV["APP_URL"]}/recuperar?token={$this->token}\">Reestablecer contraseña</a></p>";
            $contenido .= "<p>si tu no solicitaste esto, puedes ignortar el mensaje</p>";
            $contenido .= "</html>";
            $email->Body = $contenido;
            // Enviar email
            $email->send();
        } catch(\Throwable $th){
            debuguear($th);
        }
    }
}