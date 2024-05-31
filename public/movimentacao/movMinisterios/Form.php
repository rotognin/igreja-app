<?php

namespace View\Movimentacoes;

use Funcoes\Layout\Layout as L;
use Funcoes\Layout\Form as Formulario;
use Funcoes\Layout\FormControls as FC;
use App\CADASTRO\DAO\Ministerios;
use App\MOVIMENTACOES\DAO\Ministerios as MovMinisterios;
use App\CADASTRO\DAO\Pessoas;
use Funcoes\Layout\Table;
use Funcoes\Lib\GlobalHelper;

class Form extends GlobalHelper
{
    private string $cabecalho;
    private Table $table;
    private string $script;
    private Formulario $form;
    private Ministerios $ministeriosDAO;
    private MovMinisterios $movMinisteriosDAO;
    private Pessoas $pessoasDAO;
    private array $pessoas;
    private array $ministerio;

    public function __construct()
    {
        parent::__construct();
    }

    public function executar()
    {
        $this->iniciarDAO();
        $this->iniciarVars();
        $this->montarCabecalho();
        $this->inicioForm();
        $this->montarTabela();
        $this->buscarPessoas();
        $this->montarPessoas();
        $this->montarScript();
        $this->saidaPagina();
    }

    private function iniciarDAO()
    {
        $this->movMinisteriosDAO = new MovMinisterios();
        $this->ministeriosDAO = new Ministerios();
        $this->pessoasDAO = new Pessoas();
    }

    private function iniciarVars()
    {
        $this->pessoas = array();
        $this->ministerio = $this->ministeriosDAO->get($this->request->get('min_id'));
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">Atribuir pessoas ao Ministério</h1>',
            L::backButton()
        );
    }

    private function inicioForm()
    {
        $this->form = new Formulario();
        $this->form->setTitle($this->ministerio['min_sigla'] . ' - ' . $this->ministerio['min_nome']);
        $this->form->setForm('id="form-pessoas" action="?posicao=salvar" method="post"');
        $this->form->addHidden(FC::hidden('mvm_ministerio', $this->request->get('min_id')));
    }

    private function montarTabela()
    {
        $this->table = new Table('tabtla-ministerios');
        $this->table->setSize('sm');
        $this->table->setFooter(false);

        $this->table->addHeader([
            'cols' => [
                ['value' => '', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Código', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Nome', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Membro?', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Função', 'attrs' => ['class' => 'text-center']]
            ]
        ]);
    }

    private function buscarPessoas()
    {
        $this->pessoas = $this->pessoasDAO->getArray();
    }

    private function montarPessoas()
    {
        if (!empty($this->pessoas)) {
            foreach ($this->pessoas as $pes) {
                $where = array('');
                $where[0] = ' AND m.mvm_pessoa = ? AND m.mvm_ministerio = ?';
                $where[1][] = $pes['pes_id'];
                $where[1][] = $this->request->get('min_id');

                $aMovMin = $this->movMinisteriosDAO->getArray($where);

                $checkbox = FC::checkbox('', 'pessoa_' . $pes['pes_id'], '1', [
                    'checked' => (empty($aMovMin)) ? '' : 'checked'
                ]);

                $campo_funcao = FC::select('', 'mvm_funcao_' . $pes['pes_id'], $this->movMinisteriosDAO->getFuncao(), $aMovMin[0]['mvm_funcao'] ?? 'P', [
                    'class' => 'form-control form-control-sm align-middle', 'sem_label' => true
                ]);

                $this->table->addRow([
                    'cols' => [
                        ['value' => $checkbox, 'attrs' => ['class' => 'text-center align-middle m-0 p-0']],
                        ['value' => $pes['pes_id'], 'attrs' => ['class' => 'text-center align-middle m-0 p-0']],
                        ['value' => $pes['pes_nome'], 'attrs' => ['class' => 'text-left align-middle m-0 p-0']],
                        ['value' => ($pes['pes_membro'] == 'S') ? 'Sim' : 'Não', 'attrs' => ['class' => 'text-center align-middle m-0 p-0']],
                        ['value' => $campo_funcao, 'attrs' => ['class' => 'text-center align-middle m-0 p-0 pt-2 pr-1']]
                    ]
                ]);
            }
        } else {
            $this->table->addRow([
                'cols' => [
                    ['value' => L::alert('info', 'Nenhuma pessoa cadastrada'), 'attrs' => ['colspan' => '5']]
                ]
            ]);
        }

        $this->form->setFields([
            [$this->table->html()]
        ]);

        $this->form->setActions(L::submit(_('Salvar')));
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
            </script>
        HTML;
    }

    private function saidaPagina()
    {
        $this->response->page(
            <<<HTML
                {$this->cabecalho}
                <div class="content">
                    <div class="container-fluid pb-1">
                        {$this->form->html()}
                    </div>
                </div>
                {$this->script}
            HTML,
            ['title' => 'Pessoas no Ministério']
        );
    }
}
