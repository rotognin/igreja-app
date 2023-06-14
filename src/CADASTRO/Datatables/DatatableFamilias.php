<?php

namespace App\CADASTRO\Datatables;

use App\CADASTRO\DAO\Familias;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;

class DatatableFamilias extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'fam_nome' => ''
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
                ['name' => 'fam_id'],
                ['name' => 'fam_nome'],
                ['name' => 'acoes'],
            ],
            'order' => [[1, 'asc']],
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
        $table->setAttrs(['id' => 'tabela-familias']);
        $table->setSize('sm');
        $table->setFooter(false);

        $table->addHeader([
            'cols' => [
                ['value' => _('Código')],
                ['value' => _('Nome')],
                ['value' => _('Ações')]
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $familiasDAO = new Familias();

        $where = ['', []];

        if (!empty($this->filters['fam_nome'])) {
            $where[0] .= ' AND fam_nome LIKE ?';
            $where[1][] = '%' . $this->filters['fam_nome'] . '%';
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $total = $familiasDAO->total($where);
        $registros = $familiasDAO->getArray($where, $orderBy ?? 'fam_nome ASC', $limit, $offset);

        $data = [];

        if ($total > 0) {
            foreach ($registros as $reg) {
                $buttons = L::buttonGroup([
                    L::linkButton('', "?posicao=form&fam_id={$reg['fam_id']}", _('Editar Família'), 'fas fa-edit', 'outline-secondary', 'sm'),
                    L::button('', "excluirFamilia({$reg['fam_id']})", _('Excluir Família'), 'fas fa-trash', 'outline-danger', 'sm')
                ]);

                $data[] = array(
                    $reg['fam_id'],
                    $reg['fam_nome'],
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
