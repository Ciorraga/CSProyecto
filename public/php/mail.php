<?php

class mail {
    public function mandarMail($asunto,$mensaje,$destino) {
        $mail             = new PHPMailer();
        $mail->IsSMTP();
        $mail->CharSet    = "UTF-8";
        $mail->Host       = "smtp.gmail.com";
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = "tls"; //$mail->Port = 587;
        $mail->Username   = "mailDesdeElQueSeManda";
        $mail->Password   = "";
        $mail->SetFrom($mail->Username, "CSGOPlay");
        $mail->Subject    = $asunto;
        $mail->MsgHTML($mensaje);
        $mail->AddAddress($destino);

        if(!$mail->Send()) {
            echo "Error al Enviar: " . $mail->ErrorInfo;
        } else {
            //redireccion a pagina
            //header("Location: index.php?r=".$recOk);
        }
    }
}