<?php

namespace View\Ministerios\Musica\Musica;

use Funcoes\Lib\GlobalHelper;
use App\MINISTERIOS\MUSICA\DAO\MusicaAnexos;

class SalvarAnexo extends GlobalHelper
{
    private MusicaAnexos $musicaAnexosDAO;
    private array $campos = [];
    private string $usuario;
    private string $arquivo; // Nome do arquivo a ser gravado. anexo_{mua_id}.{ext}
    private string $nomeArquivo; // Nome do arquivo físico original
    private array $file; // Array com as informações do arquivo vinda
    private int $mus_id;
    private int $mua_id;
    private string $ext; // Extensão do arquivo
    private string $dir; // Pasta de destino

    public function __construct()
    {
        parent::__construct();
        $this->usuario = $this->session->get('credentials.default');
        $this->mus_id = $this->request->post('mus_id');
    }

    public function executar()
    {
        $this->iniciarDAO();
        $this->criarPasta();
        $this->preencherCampos();
        $this->inserirRegistro();
        $this->salvarAnexo();
        $this->saidaPagina();
    }

    private function iniciarDAO()
    {
        $this->musicaAnexosDAO = new MusicaAnexos();
    }

    private function criarPasta()
    {
        $this->file = $this->request->file('mua_arquivo');
        $this->nomeArquivo = $this->file['name'];

        $parts = explode('.', $this->file['name']);
        $this->ext = array_pop($parts);

        $this->dir = __DIR__ . '/../../../' . $this->config->get('folders.upload') . '/anexos_musica/' . $this->mus_id;
        if (!file_exists($this->dir)) {
            mkdir($this->dir, 0755, true);
        }
    }

    private function preencherCampos()
    {
        $this->campos = [
            'mua_arquivo' => $this->nomeArquivo,
            'mua_musica_id' => $this->mus_id,
            'mua_descricao' => $this->request->post('mua_descricao', ''),
            'mua_tipo' => $this->request->post('mua_tipo'),
            'mua_usuario_cadastro' => $this->usuario,
            'mua_data_hora_cadastro' => date('Y-m-d H:i:s')
        ];
    }

    private function inserirRegistro()
    {
        $this->mua_id = $this->musicaAnexosDAO->insert($this->campos);

        if ($this->mua_id) {
            $this->session->flash('success', 'Cadastro efetuado com sucesso');
            $this->arquivo = 'anexo_' . $this->mua_id . '.' . $this->ext;
        } else {
            $this->voltarErro('Cadastro não foi gravado');
        }
    }

    private function salvarAnexo()
    {
        if (!move_uploaded_file($this->file['tmp_name'], $this->dir . '/' . $this->arquivo)) {
            $this->voltarErro('Não foi possível anexar o arquivo');
        }
    }

    private function voltarErro(string $mensagem)
    {
        $this->session->set('previous', $this->request->postArray());
        $this->session->flash('error', $mensagem);
        $this->response->back();
    }

    private function saidaPagina()
    {
        $this->response->redirect("musicas.php?posicao=anexos&mus_id={$this->mus_id}");
    }
}
