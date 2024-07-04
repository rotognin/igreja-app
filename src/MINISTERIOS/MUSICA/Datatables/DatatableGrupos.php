<?php

namespace App\MINISTERIOS\MUSICA\Datatables;

use App\MINISTERIOS\MUSICA\DAO\Grupos;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;

class DatatableGrupos extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'gru_nome' => '',
            'gru_situacao' => ''
        ];

        $script = <<<'SCRIPT'
            function(settings, start, end, max, total, pre) {
                if (max == -1) {
                    return "Mostrando todos os registros. Total de " + total + " registros.";
                } else {
                    return pre;
                }
            }
        SCRIPT;

        //Definições das opções do datatable dando merge com as opções padrão
        $this->setOptions([
            'columns' => [
                ['name' => 'gru_id'],
                ['name' => 'gru_nome'],
                ['name' => 'gru_sigla'],
                ['name' => 'gru_situacao'],
                ['name' => 'acoes'],
            ],
            'order' => [[0, 'asc']],
            'columnDefs' => [
                ['targets' => [0, 2, 3, 4], 'className' => 'text-center'],
                ['targets' => [4], 'orderable' => false],
            ],
            'fixedHeader' => true,
            'lengthMenu' => [[10, 50, 100, -1], [10, 50, 100, 'Todos']],
            'infoCallback' => $script
        ]);

        //carregar os filtros a partir da requisição
        $this->loadFilters();
    }

    public function tableConfig(Datatable $table)
    {
        $table->setAttrs(['id' => 'tabela-grupos']);
        $table->setSize('sm');
        $table->setFooter(false);

        $table->addHeader([
            'cols' => [
                ['value' => 'Código', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Nome', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Sigla', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Situação', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Ações', 'attrs' => ['class' => 'text-center']]
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $gruposDAO = new Grupos();

        $where = ['', []];

        if ($this->filters['gru_nome'] != '') {
            $where[0] .= ' AND gru_nome LIKE ?';
            $where[1][] = '%' . $this->filters['gru_nome'] . '%';
        }

        if ($this->filters['gru_situacao'] != 'T') {
            $where[0] .= ' AND gru_situacao = ?';
            $where[1][] = $this->filters['gru_situacao'];
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $registros = $gruposDAO->getArray($where, $orderBy ?? ' gru_id ASC ', $limit, $offset);

        $data = [];
        $total = 0;

        if (!empty($registros)) {
            $total = $registros[0]['total'] ?? count($registros);

            foreach ($registros as $reg) {
                $buttons = L::buttonGroup([
                    L::linkButton('', "?posicao=form&gru_id={$reg['gru_id']}", 'Editar Grupo', 'fas fa-edit', 'outline-info', 'sm'),
                    L::linkButton('', "?posicao=composicao&gru_id={$reg['gru_id']}", 'Composição', 'fas fa-users', 'outline-success', 'sm'),
                    L::button('', "excluir('{$reg['gru_id']}')", 'Excluir Grupo', 'fas fa-trash', 'outline-danger', 'sm')
                ]);

                $data[] = array(
                    $reg['gru_id'],
                    $reg['gru_nome'],
                    $reg['gru_sigla'],
                    $gruposDAO->getSituacao($reg['gru_situacao']),
                    $buttons
                );
            }
        }

        return [
            'data' => $data,
            'total' => $total,
        ];
    }
}
