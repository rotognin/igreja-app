<?php

namespace Funcoes\Lib\Notificacoes;

use Funcoes\Interfaces\Notificavel;

class Notificacao
{
    private int $id;
    private string $tipo;
    private string $icone;
    private string $url;
    private string $mensagem;

    public function __construct(string $tipo, string $icone, string $url, string $mensagem)
    {
        $this->tipo = $tipo;
        $this->mensagem = $mensagem;
        $this->icone = $icone;
        $this->url = $url;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getMensagem()
    {
        return $this->mensagem;
    }

    public function getIcone()
    {
        return $this->icone;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function enviar(Notificavel $destino)
    {
        $this->salvar($destino);

        //enviar notificação através de um serviço externo. Talvez implementar um rabbit para enviar
    }

    private function salvar($destino)
    {
        $dao = new \Funcoes\DAO\Notificacao();
        $dao->insert([
            'not_tipo' => $this->tipo,
            'not_icone' => $this->icone,
            'not_url' => $this->url,
            'not_mensagem' => $this->mensagem,
            'not_tipo_destino' => $destino->getTipoNotificavel(),
            'not_id_destino' => $destino->getID(),
        ]);
    }
}
