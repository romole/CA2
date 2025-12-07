<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email
{
  private PHPMailer $mailer;

  public $toEmail;
  public $toNombre;
  public $token;

  public function __construct($email, $nombre, $token)
  {
    $this->toEmail = $email;
    $this->toNombre = $nombre;
    $this->token = $token;

    $this->mailer = new PHPMailer(true);
    $this->configuracion();
  }

  private function configuracion()
  {
    $email_Host = $_ENV['EMAIL_HOST'] ?: 'sandbox.smtp.mailtrap.io'; // test credentials - remote test server

    try {
      $this->mailer->isSMTP();
      $this->mailer->Host = $email_Host;
      $this->mailer->SMTPAuth = true;
      $this->mailer->Username = $_ENV['EMAIL_USERNAME'];
      $this->mailer->Password = $_ENV['EMAIL_PASSWORD'];
      $this->mailer->SMTPSecure = 'tls';
      $this->mailer->Port = (int)($_ENV['EMAIL_PORT'] ?? 587);

      // contenido
      $this->mailer->isHTML(TRUE);
      $this->mailer->CharSet = 'UTF-8';
    } catch (\Exception $e) {
      error_log("Error de configuración SMTP: " . $e->getMessage());
    }
  }

  private function generarContenido(string $tipo)
  {
    $email_Url = $_ENV['EMAIL_HOST'] ?: 'http://localhost:3000'; // test credentials - local test server

    $app_url = $email_Url;
    $nombre = $this->toNombre;
    $token = $this->token;
    $contenido = '';

    if ($tipo === 'confirmacion') {
      $titulo = 'Confirma tu Cuenta';
      $mensaje_saludo = "Has registrado correctamente tu cuenta en la plataforma CA2, pero es necesario confirmarla.";
      $link_texto = "Confirmar Cuenta";
      $link_ruta = $app_url . '/alta-confirmar?token=' . $token;
      $mensaje_cierre = "Si tú no creaste esta cuenta, puedes ignorar el mensaje.";
    } else if ($tipo === 'instrucciones') {
      $titulo = 'Restablecer Contraseña';
      $mensaje_saludo = "Has solicitado reestablecer tu password en la plataforma CA2, sigue el siguiente enlace para hacerlo.";
      $link_texto = "Reestablecer Password";
      $link_ruta = $app_url . '/recuperar?token=' . $token;
      $mensaje_cierre = "Si tú no solicitaste este cambio, puedes ignorar el mensaje.";
    } else {
      return ''; // retorna vacio si $tipo no es valido
    }

    // --- bloque Heredoc ---
    $contenido = <<<CUERPO_EMAIL
        <html>
        <head>
            <title>$titulo</title>
        </head>
        <body>
            <p>Hola <strong>$nombre</strong></p>
            <p>$mensaje_saludo</p>
            <p>Presiona aquí para: <a href="$link_ruta">$link_texto</a></p>
            <p>$mensaje_cierre</p>
        </body>
        </html>
        CUERPO_EMAIL;

    return $contenido;
  }

  // email - NUEVA cuenta
  public function enviarConfirmacion(string $fromEmail, string $fromName)
  {
    $this->mailer->setFrom($fromEmail, $fromName);
    $this->mailer->addAddress($this->toEmail, $this->toNombre);
    $this->mailer->Subject = 'Confirma tu Cuenta';

    // Llama al método unificado
    $this->mailer->Body = $this->generarContenido('confirmacion');

    $this->mailer->send();
  }

  // email - OLVIDO cuenta - reset
  public function enviarInstrucciones(string $fromEmail, string $fromName)
  {
    $this->mailer->setFrom($fromEmail, $fromName);
    $this->mailer->addAddress($this->toEmail, $this->toNombre);
    $this->mailer->Subject = 'Reestablece tu password';

    // Llama al método unificado
    $this->mailer->Body = $this->generarContenido('instrucciones');

    $this->mailer->send();
  }
}
