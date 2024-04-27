<?php

namespace App\SGC\Datatables;

use App\SGC\DAO\Acao;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;

class DatatableAcoes extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'aca_acao' => '',
            'aca_descricao' => '',
            'aca_grupo' => '',
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
                ['name' => 'aca_acao'],
                ['name' => 'aca_descricao'],
                ['name' => 'aca_grupo'],
                ['name' => 'acoes'],
            ],
            'order' => [[2, 'asc']],
            'columnDefs' => [
                ['targets' => [0, 2, 3], 'className' => 'text-center'],
                ['targets' => [3], 'orderable' => false],
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
        $table->setSize('sm');
        $table->setFooter(false);
        $table->addHeader([
            'cols' => [
                ['value' => 'Ação', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Descrição'],
                ['value' => 'Grupo', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Ações', 'attrs' => ['class' => 'text-center']],
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $acaoDAO = new Acao();

        $where = ["", []];

        if (!empty($this->filters['aca_acao'])) {
            $where[0] .= " AND a.aca_acao LIKE ?";
            $where[1][] = "%{$this->filters['aca_acao']}%";
        }

        if (!empty($this->filters['aca_descricao'])) {
            $where[0] .= " AND a.aca_descricao LIKE ?";
            $where[1][] = "%{$this->filters['aca_descricao']}%";
        }

        if (!empty($this->filters['aca_grupo'])) {
            $where[0] .= " AND a.aca_grupo = ?";
            $where[1][] = $this->filters['aca_grupo'];
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $total = $acaoDAO->total($where);
        $registros = $acaoDAO->getArray($where, $orderBy ?? 'aca_grupo', $limit, $offset);

        $data = [];

        if (!empty($registros)) {
            foreach ($registros as $reg) {
                $buttons = L::buttonGroup([
                    L::linkButton('', "?posicao=form&aca_acao={$reg['aca_acao']}", 'Editar ação', 'fas fa-edit', 'outline-secondary', 'sm'),
                    L::button('', "deleteAcao('{$reg['aca_acao']}')", 'Excluir ação', 'fas fa-trash', 'outline-danger', 'sm'),
                ]);
                $data[] = [
                    $reg['aca_acao'],
                    $reg['aca_descricao'],
                    $reg['aca_grupo'],
                    $buttons,
                ];
            }
        }

        return [
            'data' => $data,
            'total' => $total,
        ];
    }
}
