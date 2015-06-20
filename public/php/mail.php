<?php

class mail {
    public function mandarMail($asunto,$mensaje,$destino) {
        $mail = new PHPMailer(); // create a new object
        $mail->IsSMTP(); // enable SMTP
        $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true; // authentication enabled
        $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465; // or 587
        $mail->IsHTML(true);
        $mail->Username = "";
        $mail->Password = "";
        $mail->SetFrom("maciorraga@gmail.com");
        $mail->Subject = $asunto;
        $mail->Body = $mensaje;
        $mail->AddAddress($destino);
        if(!$mail->Send())
        {
            echo "Mailer Error: " . $mail->ErrorInfo;
        }
        else
        {
            echo "Message has been sent";
        }
    }
}