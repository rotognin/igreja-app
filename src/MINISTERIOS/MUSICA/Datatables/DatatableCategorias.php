<?php

namespace App\MINISTERIOS\MUSICA\Datatables;

use App\MINISTERIOS\MUSICA\DAO\Categorias;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;

class DatatableCategorias extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'cam_descricao' => ''
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
                ['name' => 'cam_id'],
                ['name' => 'cam_descricao'],
                ['name' => 'acoes'],
            ],
            'order' => [[0, 'asc']],
            'columnDefs' => [
                ['targets' => [0, 2], 'className' => 'text-center'],
                ['targets' => [2], 'orderable' => false],
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
        $table->setAttrs(['id' => 'tabela-categorias']);
        $table->setSize('sm');
        $table->setFooter(false);

        $table->addHeader([
            'cols' => [
                ['value' => 'Código', 'attrs' => ['class' => 'text-center col-md-1']],
                ['value' => 'Descrição', 'attrs' => ['class' => 'text-center col-md-9']],
                ['value' => 'Ações', 'attrs' => ['class' => 'text-center col-md-2']]
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $categoriasDAO = new Categorias();

        $where = ['', []];

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        //$total = $familiasDAO->total($where);
        $registros = $categoriasDAO->getArray($where, $orderBy ?? 'cam_id ASC', $limit, $offset);

        $data = [];
        $total = 0;

        if (!empty($registros)) {
            $total = $registros[0]['total'] ?? count($registros);

            foreach ($registros as $reg) {
                $buttons = L::buttonGroup([
                    L::linkButton('', "?posicao=form&cam_id={$reg['cam_id']}", 'Editar Categoria', 'fas fa-edit', 'outline-secondary', 'sm'),
                    L::button('', "excluirCategoria({$reg['cam_id']})", 'Excluir Categoria', 'fas fa-trash', 'outline-danger', 'sm')
                ]);

                $data[] = array(
                    $reg['cam_id'],
                    $reg['cam_descricao'],
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
