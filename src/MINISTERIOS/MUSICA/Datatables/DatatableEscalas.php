<?php

namespace App\MINISTERIOS\MUSICA\Datatables;

use App\MINISTERIOS\MUSICA\DAO\Escalas;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;
use Funcoes\Helpers\Format;

class DatatableEscalas extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'esc_titulo' => '',
            'esc_data_ini' => '',
            'esc_data_fim' => '',
            'esc_situacao' => ''
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
                ['name' => 'esc_id'],
                ['name' => 'esc_titulo'],
                ['name' => 'esc_data'],
                ['name' => 'esc_hora'],
                ['name' => 'esc_situacao'],
                ['name' => 'acoes'],
            ],
            'order' => [[0, 'asc']],
            'columnDefs' => [
                ['targets' => [0, 2, 3, 4, 5], 'className' => 'text-center'],
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
        $table->setAttrs(['id' => 'tabela-escalas']);
        $table->setSize('sm');
        $table->setFooter(false);

        $table->addHeader([
            'cols' => [
                ['value' => 'Código', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Título', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Data', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Hora', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Situação', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Ações', 'attrs' => ['class' => 'text-center']]
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $escalasDAO = new Escalas();

        $where = ['', []];

        if ($this->filters['esc_titulo'] != '') {
            $where[0] = ' AND esc_titulo LIKE ?';
            $where[1][] = '%' . $this->filters['esc_titulo'] . '%';
        }

        if ($this->filters['esc_data_ini'] != '') {
            $data_ini = Format::sqlDatetime($this->filters['esc_data_ini'], 'd/m/Y', 'Y-m-d');
            $data_fim = Format::sqlDatetime($this->filters['esc_data_fim'], 'd/m/Y', 'Y-m-d');

            $where[0] .= ' AND esc_data BETWEEN ? AND ?';
            $where[1][] = $data_ini;
            $where[1][] = $data_fim;
        }

        if ($this->filters['esc_situacao'] != 'T') {
            $where[0] .= ' AND esc_situacao = ?';
            $where[1][] = $this->filters['esc_situacao'];
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $registros = $escalasDAO->getArray($where, $orderBy ?? ' esc_id ASC ', $limit, $offset);

        $data = [];
        $total = 0;

        if (!empty($registros)) {
            $total = $registros[0]['total'] ?? count($registros);

            foreach ($registros as $reg) {
                $buttons = L::buttonGroup([
                    L::linkButton('', "?posicao=form&esc_id={$reg['esc_id']}", 'Editar Escala', 'fas fa-edit', 'outline-info', 'sm'),
                    L::linkButton('', "?posicao=composicao&esc_id={$reg['esc_id']}", 'Composição', 'fas fa-users', 'outline-success', 'sm'),
                    L::button('', "fechar('{$reg['esc_id']}')", 'Fechar Escala', 'fas fa-door-closed', 'outline-info', 'sm'),
                    L::button('', "realizar('{$reg['esc_id']}')", 'Realizar Escala', 'fas fa-thumbs-up', 'outline-success', 'sm'),
                    L::button('', "cancelar('{$reg['esc_id']}')", 'Cancelar Escala', 'fas fa-window-close', 'outline-danger', 'sm'),
                    L::button('', "adiar('{$reg['esc_id']}')", 'Adiar Escala', 'fas fa-forward', 'outline-primary', 'sm'),
                    L::button('', "excluir('{$reg['esc_id']}')", 'Excluir Grupo', 'fas fa-trash', 'outline-danger', 'sm')
                ]);

                $data[] = array(
                    $reg['esc_id'],
                    $reg['esc_titulo'],
                    Format::date($reg['esc_data'], 'd/m/Y'),
                    $reg['esc_hora'],
                    $escalasDAO->getSituacao($reg['esc_situacao']),
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
