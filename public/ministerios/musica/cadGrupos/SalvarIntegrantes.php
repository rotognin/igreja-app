<?php

namespace View\Ministerios\Musica\Grupo;

use Funcoes\Lib\GlobalHelper;
use App\MINISTERIOS\MUSICA\DAO\GrupoPessoas;

class SalvarIntegrantes extends GlobalHelper
{
    private GrupoPessoas $grupoPessoasDAO;
    private array $campos = [];
    private int $gru_id;

    public function __construct()
    {
        parent::__construct();
    }

    public function executar()
    {
        $this->iniciarDAO();
        $this->inserirRegistro();
        $this->saidaPagina();
    }

    private function iniciarDAO()
    {
        $this->grupoPessoasDAO = new GrupoPessoas();
    }

    private function inserirRegistro()
    {
        $this->gru_id = $this->request->post('gru_id');
        $array = $this->request->postArray();

        foreach ($array as $key => $value) {
            if (str_starts_with($key, 'pes_check_')) {
                $arrGru = explode('_', $key);
                $pes_id = $arrGru[2];

                $record = array(
                    'grp_grupo_id' => $this->gru_id,
                    'grp_pessoa_id' => $pes_id,
                    'grp_observacoes' => $this->request->post('pes_obs_' . $pes_id)
                );

                $grp_id = $this->grupoPessoasDAO->insert($record);
            }
        }

        if ($grp_id) {
            $this->session->flash('success', 'Cadastro efetuado com sucesso');
        } else {
            $this->voltarErro('Cadastro não foi gravado');
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
        $this->response->redirect("grupos.php?posicao=composicao&gru_id={$this->gru_id}");
    }
}
