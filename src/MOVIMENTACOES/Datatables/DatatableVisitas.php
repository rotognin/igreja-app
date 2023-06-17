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
            'vis_status' => '',
            'vis_periodo' => ''
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
                ['name' => 'vis_data'],
                ['name' => 'vis_status'],
                ['name' => 'acoes']
            ],
            'order' => [[2, 'asc']],
            'columnDefs' => [
                ['targets' => [0, 3, 4, 5], 'className' => 'text-center'],
                ['targets' => [5], 'orderable' => false],
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
                ['value' => _('Código')],
                ['value' => _('Título')],
                ['value' => _('Família')],
                ['value' => _('Data')],
                ['value' => _('Situação')],
                ['value' => _('Ações')]
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $visitasDAO = new Visitas();

        $where = ['', []];

        if ($this->filters['vis_periodo'] == '1') {
            $where[0] .= ' AND vis_data BETWEEN ? AND ? ';
            $where[1][] = $this->filters['vis_data_ini'];
            $where[1][] = $this->filters['vis_data_fim'];
        }

        if (!empty($this->filters['vis_familia_id'])) {
            $where[0] .= ' AND vis_familia_id = ?';
            $where[1][] = $this->filters['vis_familia_id'];
        }

        if (!empty($this->filters['vis_status'])) {
            if ($this->filters['vis_status'] != '0') {
                $where[0] .= ' AND vis_status = ?';
                $where[1][] = $this->filters['vis_status'];
            }
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $total = $visitasDAO->total($where);
        $registros = $visitasDAO->getArray($where, 'vis_data ASC', $limit, $offset);

        $data = [];

        if ($total > 0) {
            foreach ($registros as $reg) {
                $buttons = L::buttonGroup([
                    L::linkButton('', "?posicao=form&vis_id={$reg['vis_id']}", _('Editar Visita'), 'fas fa-edit', 'outline-primary', 'sm'),
                    L::linkButton('', "?posicao=confirmar&vis_id={$reg['vis_id']}", _('Confirmar Visita'), 'fas fa-check', 'outline-primary', 'sm'),
                    L::button('', "cancelarVisita({$reg['vis_id']})", _('Cancelar Visita'), 'fas fa-trash', 'outline-danger', 'sm')
                ]);

                $data[] = array(
                    $reg['vis_id'],
                    $reg['vis_titulo'],
                    $reg['fam_nome'],
                    Format::date($reg['vis_data']) . ' ' . $reg['vis_hora'],
                    $reg['vis_status'],
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
