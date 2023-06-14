<?php

namespace Funcoes\Lib\Email;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email
{
    /** Propriedades configuradas */
    private string $host;
    private string $user;
    private string $pass;
    private int $port;
    private string $reply;

    /** Propriedades a serem informadas */
    private string $destino;
    private string $assunto;
    private string $mensagem;

    public function __construct(string $provider = 'default')
    {
        global $config;
        $this->host = $config->get('email.' . $provider . '.host');
        $this->user = $config->get('email.' . $provider . '.user');
        $this->pass = $config->get('email.' . $provider . '.pass');
        $this->port = $config->get('email.' . $provider . '.port');
        $this->reply = $config->get('email.' . $provider . '.reply');
    }

    public function setDestino(string $destino)
    {
        $this->destino = (filter_var($destino, FILTER_VALIDATE_EMAIL)) ? $destino : '';

        if ($this->destino == '') {
            echo 'E-mail incorreto. Não enviado para: ' . $destino;
            exit;
        }
    }

    public function setMensagem(string $mensagem)
    {
        $this->mensagem = $mensagem;
    }

    public function setAssunto(string $assunto)
    {
        $this->assunto = $assunto;
    }

    public function enviar(): string
    {
        $retorno = '';

        $email = new PHPMailer(true);

        try {
            //$email->SMTPDebug = SMTP::DEBUG_LOWLEVEL;
            $email->isSMTP();
            $email->Host = $this->host;
            $email->SMTPAuth = true;
            $email->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // PHPMailer::ENCRYPTION_SMTPS;
            $email->Username = $this->user;
            $email->Password = $this->pass;
            $email->Port = $this->port;
            //$email->CharSet = PHPMailer::CHARSET_UTF8;
            $email->Encoding = PHPMailer::ENCODING_BASE64;

            /*
            $email->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            */

            $email->setFrom($this->user, 'Sistema');
            $email->addReplyTo($this->reply);
            $email->addAddress($this->destino);
            $email->isHTML(true);
            $email->Subject = utf8_decode($this->assunto);
            $email->Body = utf8_decode($this->mensagem);
            $email->AltBody = strip_tags($this->mensagem);

            if (!$email->send()) {
                //echo 'E-mail não foi enviado para: ' . $this->destino . ' - assunto: ' . $this->assunto;
                $retorno = $email->ErrorInfo;
            }
        } catch (Exception $exc) {
            $retorno = "Exceção ao enviar e-mail: " . $email->ErrorInfo . ' - ' .  $exc->getMessage();
        }

        return $retorno;
    }
}
