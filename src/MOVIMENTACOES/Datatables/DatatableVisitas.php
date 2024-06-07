<?php

namespace App\MOVIMENTACOES\Datatables;

use App\MOVIMENTACOES\DAO\Visitas;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;
use Funcoes\Helpers\Format;

class DatatableVisitas extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'vis_data_ini' => '',
            'vis_data_fim' => '',
            'vis_familia_id' => '',
            'vis_situacao' => '',
            'vis_titulo' => ''
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
                ['name' => 'vis_id'],
                ['name' => 'vis_titulo'],
                ['name' => 'vis_familia'],
                ['name' => 'vis_quem'],
                ['name' => 'vis_data'],
                ['name' => 'vis_situacao'],
                ['name' => 'acoes']
            ],
            'order' => [[0, 'desc']],
            'columnDefs' => [
                ['targets' => [0, 4, 5, 6], 'className' => 'text-center'],
                ['targets' => [6], 'orderable' => false],
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
        $table->setAttrs(['id' => 'tabela-visitas']);
        $table->setSize('sm');
        $table->setFooter(false);

        $table->addHeader([
            'cols' => [
                ['value' => 'Código'],
                ['value' => 'Título'],
                ['value' => 'Família'],
                ['value' => 'Quem receberá?'],
                ['value' => 'Data'],
                ['value' => 'Situação'],
                ['value' => 'Ações']
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $visitasDAO = new Visitas();

        $where = ['', []];

        if (!empty($this->filters['vis_data_ini'])) {
            $data_ini = Format::sqlDatetime($this->filters['vis_data_ini'], 'd/m/Y', 'Y-m-d');
            $data_fim = Format::sqlDatetime($this->filters['vis_data_fim'], 'd/m/Y', 'Y-m-d');

            $where[0] .= ' AND vis_data BETWEEN ? AND ? ';
            $where[1][] = $data_ini;
            $where[1][] = $data_fim;
        }

        if (!empty($this->filters['vis_familia_id'])) {
            $where[0] .= ' AND vis_familia_id = ?';
            $where[1][] = $this->filters['vis_familia_id'];
        }

        if (!empty($this->filters['vis_situacao'])) {
            $where[0] .= ' AND vis_situacao = ?';
            $where[1][] = $this->filters['vis_situacao'];
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $registros = $visitasDAO->getArray($where, $orderBy ?? 'vis_data ASC', $limit, $offset);

        $data = [];
        $total = 0;

        if (!empty($registros)) {
            $total = $registros[0]['total'] ?? count($registros);

            foreach ($registros as $reg) {
                $disabled = '';
                $disabled_realizar = '';
                $disabled_cancelar = '';

                $buttonEditar = 'outline-primary';
                $buttonRealizar = 'outline-primary';
                $buttonCancelar = 'outline-primary';
                $buttonExcluir = 'outline-danger';

                if (in_array($reg['vis_situacao'], ['C', 'R'])) {
                    $disabled = ' disabled aria-disabled="true"';
                    $buttonEditar = 'outline-secondary';
                    $buttonRealizar = 'outline-secondary';
                    $buttonExcluir = 'outline-secondary';
                    $buttonCancelar = 'outline-secondary';
                }

                if ($reg['vis_situacao'] == 'P') {
                    $disabled_realizar = ' disabled aria-disabled="true"';
                    $disabled_cancelar = ' disabled aria-disabled="true"';
                    $buttonRealizar = 'outline-secondary';
                    $buttonCancelar = 'outline-secondary';
                }

                $buttons = L::buttonGroup([
                    L::button('', "editarVisita({$reg['vis_id']})", 'Editar Visita', 'fas fa-edit', $buttonEditar, 'sm', $disabled),
                    L::button('', "realizarVisita({$reg['vis_id']})", 'Realizar Visita', 'fas fa-check', $buttonRealizar, 'sm', $disabled . $disabled_realizar),
                    L::button('', "cancelarVisita({$reg['vis_id']})", 'Cancelar Visita', 'fas fa-comment-slash', $buttonCancelar, 'sm', $disabled . $disabled_cancelar),
                    L::button('', "excluirVisita({$reg['vis_id']})", 'Excluir Visita', 'fas fa-trash', $buttonExcluir, 'sm', $disabled)
                ]);

                $data[] = array(
                    $reg['vis_id'],
                    $reg['vis_titulo'],
                    $reg['fam_nome'],
                    $reg['vis_quem'],
                    Format::date($reg['vis_data']) . ' ' . $reg['vis_hora'],
                    $visitasDAO->getSituacoes($reg['vis_situacao']),
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
