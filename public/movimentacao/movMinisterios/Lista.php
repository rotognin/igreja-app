<?php

namespace View\Movimentacoes;

use Funcoes\Layout\Layout as L;
use App\CADASTRO\DAO\Ministerios;
use App\MOVIMENTACOES\DAO\Ministerios as MovMinisterios;
use Funcoes\Layout\Table;
use Funcoes\Lib\GlobalHelper;

class Lista extends GlobalHelper
{
    private string $cabecalho;
    private Table $table;
    private string $script;
    private Ministerios $ministeriosDAO;
    private MovMinisterios $movMinisteriosDAO;
    private array $ministerios;

    public function __construct()
    {
        parent::__construct();
    }

    public function executar()
    {
        $this->iniciarDAO();
        $this->iniciarVars();
        $this->montarCabecalho();
        $this->montarTabela();
        $this->buscarMinisterios();
        $this->montarMinisterios();
        $this->montarScript();
        $this->saidaPagina();
    }

    private function iniciarDAO()
    {
        $this->ministeriosDAO = new Ministerios();
        $this->movMinisteriosDAO = new MovMinisterios();
    }

    private function iniciarVars()
    {
        $this->ministerios = array();
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">Movimentações em Ministérios</h1>'
        );
    }

    private function montarTabela()
    {
        $this->table = new Table('tabtla-ministerios');
        $this->table->setSize('sm');
        $this->table->setFooter(false);

        $this->table->addHeader([
            'cols' => [
                ['value' => 'Código', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Nome', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Sigla', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Participantes', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Ações', 'attrs' => ['class' => 'text-center']],
            ],
        ]);
    }

    private function buscarMinisterios()
    {
        $this->ministerios = $this->ministeriosDAO->getArray();
    }

    private function montarMinisterios()
    {
        if (!empty($this->ministerios)) {
            foreach ($this->ministerios as $min) {
                $where = array('');
                $where[0] = ' AND m.mvm_ministerio = ?';
                $where[1][] = $min['min_id'];

                $aParticipantes = $this->movMinisteriosDAO->getArray($where);

                $participantes = array_map(function ($pessoa) {
                    if ($pessoa['mvm_funcao'] == 'L') {
                        $nome = '<b>' . $pessoa['pes_nome'] . '</b>';
                    } else {
                        $nome = $pessoa['pes_nome'];
                    }

                    return $nome;
                }, $aParticipantes);

                $botao = L::buttonGroup([
                    L::linkButton('', '?posicao=form&min_id=' . $min['min_id'], 'Participantes', 'fas fa-users', 'outline-success', 'sm')
                ]);

                $this->table->addRow([
                    'cols' => [
                        ['value' => $min['min_id'], 'attrs' => ['class' => 'text-center']],
                        ['value' => $min['min_nome'], 'attrs' => ['class' => 'text-left']],
                        ['value' => $min['min_sigla'], 'attrs' => ['class' => 'text-center']],
                        ['value' => implode(', ', $participantes), 'attrs' => ['class' => 'text-left']],
                        ['value' => $botao, 'attrs' => ['class' => 'text-center']]
                    ]
                ]);
            }
        } else {
            $this->table->addRow([
                'cols' => [
                    ['value' => L::alert('info', 'Nenhum ministério cadastrado'), 'attrs' => ['colspan' => '5']]
                ]
            ]);
        }
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
                        {$this->table->html()}
                    </div>
                </div>
                {$this->script}
            HTML,
            ['title' => 'Movimentação em Ministérios']
        );
    }
}
