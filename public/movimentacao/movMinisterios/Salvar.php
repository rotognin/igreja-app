<?php

namespace View\Movimentacoes;

use Funcoes\Lib\GlobalHelper;
use App\MOVIMENTACOES\DAO\Ministerios;

class Salvar extends GlobalHelper
{
    private Ministerios $ministeriosDAO;

    public function __construct()
    {
        parent::__construct();
    }

    public function executar()
    {
        $this->iniciarDAO();
        $this->excluirRegistros();
        $this->gravarRegistros();
        $this->saidaPagina();
    }

    private function iniciarDAO()
    {
        $this->ministeriosDAO = new Ministerios();
    }

    private function gravarRegistros()
    {
        // Buscar todos os registros marcados no formulário
        $arrayPost = $this->request->postArray();

        foreach ($arrayPost as $key => $post) {
            if (str_starts_with($key, 'pessoa_')) {
                $aCampo = explode('_', $key);
                $mvm_pessoa = $aCampo[1];

                $record = array(
                    'mvm_pessoa' => $mvm_pessoa,
                    'mvm_ministerio' => $this->request->post('mvm_ministerio'),
                    'mvm_funcao' => $this->request->post('mvm_funcao_' . $mvm_pessoa)
                );

                $this->inserirRegistro($record);
            }
        }
    }

    private function excluirRegistros()
    {
        $this->ministeriosDAO->deleteMinisterio($this->request->post('mvm_ministerio'));
    }

    private function inserirRegistro(array $registro)
    {
        $record = array_merge($registro, [
            'mvm_data_inc' => date('Y-m-d H:i:s'),
            'mvm_usu_inc' => $this->session->get('credentials.default')
        ]);

        $mvm_id = $this->ministeriosDAO->insert($record);

        if ($mvm_id) {
            $this->session->flash('success', 'Atribuições realizadas');
        } else {
            $this->voltarErro(_('Cadastro não foi gravado'));
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
        $this->response->redirect("ministerios.php");
    }
}
