<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Asegúrate de que esta ruta sea correcta

// Función para enviar el correo de confirmación
function enviarCorreoConfirmacion($destinatario, $nombreUsuario) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP (Mailtrap u otro servidor que estés utilizando)
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';  // Cambia esto por el host de tu servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = '1b2c441052441e';  // Cambia con tu username
        $mail->Password = 'c7edade5179bae';  // Cambia con tu password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Configuración del correo
        $mail->setFrom('no-reply@tusitio.com', 'Confirmación Registro');
        $mail->addAddress($destinatario);  // Dirección de correo del usuario
        $mail->isHTML(true);
        $mail->Subject = 'Confirmación de registro';

        // Cuerpo del correo
        $mail->Body = "
            <h1>¡Hola, $nombreUsuario!</h1>
            <p>Gracias por registrarte en nuestro sitio web.</p>
            <p>Por favor, haz clic en el siguiente enlace para confirmar tu registro:</p>
            <a href='https://tu-sitio-web.com/confirmar?email=$destinatario'>Confirmar registro</a>
            <p>Si no solicitaste este registro, ignora este correo.</p>
        ";

        // Enviar el correo
        $mail->send();
        $_SESSION['emailEnviado'] = "Correo de confirmación enviado a $destinatario.";
    } catch (Exception $e) {
        $_SESSION['emailEnviado'] = "Error al enviar el correo: {$mail->ErrorInfo}";
    }
}

// Llamada a la función para enviar el correo de confirmación
enviarCorreoConfirmacion($email, $nombre);

?>
