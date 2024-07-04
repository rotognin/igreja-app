<?php

namespace View\Ministerios\Musica\Grupo;

use Funcoes\Layout\Layout as L;
use App\CADASTRO\DAO\Pessoas;
use App\MINISTERIOS\MUSICA\DAO\Grupos;
use App\MINISTERIOS\MUSICA\DAO\GrupoPessoas;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Table;
use Funcoes\Lib\GlobalHelper;
use Funcoes\Layout\ModalForm;

class Composicao extends GlobalHelper
{
    private string $cabecalho;
    private Form $formFiltros;
    private Table $table;
    private Table $tabelaModal;
    private Pessoas $pessoasDAO;
    private Grupos $grupoDAO;
    private GrupoPessoas $grupoPessoasDAO;
    private ModalForm $form;
    private array $aGrupo;
    private string $script;
    private int $gru_id;

    public function __construct()
    {
        parent::__construct();
    }

    public function executar()
    {
        $this->iniciarDAO();
        $this->lerRegistro();
        $this->montarCabecalho();
        $this->montarModal();
        $this->montarTabela();
        $this->montarScript();
        $this->saidaPagina();
    }

    private function iniciarDAO()
    {
        $this->pessoasDAO = new Pessoas();
        $this->grupoDAO = new Grupos();
        $this->grupoPessoasDAO = new GrupoPessoas();
    }

    private function lerRegistro()
    {
        $this->gru_id = $this->request->get('gru_id');
        $this->aGrupo = $this->grupoDAO->get($this->gru_id);
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">Grupos</h1>',
            L::backButton() . ' ' . L::button('Selecionar Integrantes', 'abrirModal()', 'Selecionar Integrantes', 'fas fa-users', 'primary', 'sm'),
            '<h6 class="ml-2"><b>Integrantes do Grupo: </b>' . $this->aGrupo['gru_id'] . ' - ' . $this->aGrupo['gru_nome'] . '</h6>'
        );
    }

    private function montarModal()
    {
        $this->form = new ModalForm('modal-integrantes');
        $this->form->setForm('id="form-integrantes" action="?posicao=salvarIntegrantes" method="POST"');
        $this->form->setTitle('Integrantes do Grupo');
        $this->form->setModalSize('modal-lg');

        $this->form->addHidden(FC::hidden('gru_id', $this->gru_id));

        $this->tabelaModal = new Table('tabela-modal');
        $this->tabelaModal->setSize('sm');
        $this->tabelaModal->setFooter(false);

        $this->tabelaModal->addHeader([
            'cols' => [
                ['value' => '#', 'attrs' => ['class' => 'text-center col-sm-1']],
                ['value' => 'Nome', 'attrs' => ['class' => 'text-center col-sm-5']],
                ['value' => 'Observações', 'attrs' => ['class' => 'text-center col-sm-5']],
                ['value' => 'Selecionar', 'attrs' => ['class' => 'text-center col-sm-1']]
            ]
        ]);

        // Buscar pessoas cadastradas
        $aPessoas = $this->pessoasDAO->getArray();

        $qtd = 0;

        if (!empty($aPessoas)) {
            foreach ($aPessoas as $pes) {
                $where = array('');
                $where[0] = ' AND grp_grupo_id = ? AND grp_pessoa_id = ?';
                $where[1][] = $this->gru_id;
                $where[1][] = $pes['pes_id'];

                $registro = $this->grupoPessoasDAO->getArray($where);

                if (empty($registro)) {
                    $qtd++;

                    $input = FC::input('', 'pes_obs_' . $pes['pes_id'], '', ['class' => 'mt-1', 'style' => 'width:100%', 'has_label' => false]);
                    $check = FC::checkbox('', 'pes_check_' . $pes['pes_id'], '1', []);

                    $this->tabelaModal->addRow([
                        'cols' => [
                            ['value' => $pes['pes_id'], 'attrs' => ['class' => 'text-center align-middle m-0']],
                            ['value' => $pes['pes_nome'], 'attrs' => ['class' => 'text-center align-middle m-0']],
                            ['value' => $input, 'attrs' => ['class' => 'text-left align-middle m-0']],
                            ['value' => $check, 'attrs' => ['class' => 'text-center align-middle m-0']]
                        ]
                    ]);
                }
            }
        }

        if ($qtd == 0) {
            $this->tabelaModal->addRow([
                'cols' => [
                    ['value' => '<i>Sem pessoas a serem adicionadas</i>', 'attrs' => ['class' => 'text-center', 'colspan' => '3']]
                ]
            ]);
        }

        $this->form->setFields([
            [$this->tabelaModal->html()]
        ]);

        $this->form->setActions(
            L::button('Cancelar', '', '', '', 'secondary', 'sm', 'data-dismiss="modal"') . ' ' .
                L::button('Gravar', '', '', 'fas fa-check', 'primary')
        );
    }

    private function montarTabela()
    {
        $this->table = new Table('tabela-integrantes');
        $this->table->setFooter(false);
        $this->table->setSize('sm');

        $this->table->addHeader([
            'cols' => [
                ['value' => '#', 'attrs' => ['class' => 'text-center col-md-1']],
                ['value' => 'Nome', 'attrs' => ['class' => 'text-left col-md-5']],
                ['value' => 'Observações', 'attrs' => ['class' => 'text-left col-md-5']],
                ['value' => 'Excluir', 'attrs' => ['class' => 'text-center col-md-1']]
            ]
        ]);

        $aIntegrantes = $this->grupoPessoasDAO->getPessoas($this->gru_id);

        if (!empty($aIntegrantes)) {
            foreach ($aIntegrantes as $int) {
                $botao = L::button('', "excluir({$int['grp_id']})", 'Excluir pessoa do Grupo', 'fas fa-trash', 'outline-danger', 'sm');

                $this->table->addRow([
                    'cols' => [
                        ['value' => $int['pes_id'], 'attrs' => ['class' => 'text-center']],
                        ['value' => $int['pes_nome'], 'attrs' => ['class' => 'text-left']],
                        ['value' => $int['grp_observacoes'], 'attrs' => ['class' => 'text-left']],
                        ['value' => $botao, 'attrs' => ['class' => 'text-center']]
                    ]
                ]);
            }
        } else {
            $this->table->addRow([
                'cols' => [
                    ['value' => '<i>Nenhum integrante adicionado</i>', 'attrs' => ['class' => 'text-center', 'colspan' => '3']]
                ]
            ]);
        }
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                function excluir(grp_id){
                    confirm('Deseja realmente excluir essa pessoa do Grupo?').then(result => {
                        if (result.isConfirmed) {
                            window.location.href = '?posicao=excluirPessoa&grp_id=' + grp_id;
                        }
                    });
                }

                function abrirModal(){
                    $("#modal-integrantes").modal('show');
                }
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
                        {$this->table->html()}
                    </div>
                </div>
                {$this->script}
                {$this->form->html()}
            HTML,
            ['title' => 'Integrantes do Grupo']
        );
    }
}
