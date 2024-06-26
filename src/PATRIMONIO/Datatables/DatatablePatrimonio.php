<?php

namespace App\PATRIMONIO\Datatables;

use App\PATRIMONIO\DAO\Patrimonio;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;

class DatatablePatrimonio extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'pat_descricao' => '',
            'pat_categoria_id' => ''
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
                ['name' => 'pat_id'],
                ['name' => 'pat_descricao'],
                ['name' => 'pat_marca'],
                ['name' => 'cat_titulo'],
                ['name' => 'pat_quantidade'],
                ['name' => 'pat_conservacao'],
                ['name' => 'pat_usu_responsavel'],
                ['name' => 'acoes']
            ],
            'order' => [[0, 'asc']],
            'columnDefs' => [
                ['targets' => [0, 4, 5, 6, 7], 'className' => 'text-center'],
                ['targets' => [7], 'orderable' => false],
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
        $table->setAttrs(['id' => 'tabela-patrimonios']);
        $table->setSize('sm');
        $table->setFooter(false);

        $table->addHeader([
            'cols' => [
                ['value' => '#', 'attr' => ['class' => 'text-center']],
                ['value' => 'Descrição', 'attr' => ['class' => 'text-center']],
                ['value' => 'Marca', 'attr' => ['class' => 'text-center']],
                ['value' => 'Categoria', 'attr' => ['class' => 'text-center']],
                ['value' => 'Quantidade', 'attr' => ['class' => 'text-center']],
                ['value' => 'Conservação', 'attr' => ['class' => 'text-center']],
                ['value' => 'Responsável', 'attr' => ['class' => 'text-center']],
                ['value' => 'Ações', 'attr' => ['class' => 'text-center']]
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $patrimonioDAO = new Patrimonio();

        $where = ['', []];

        if (!empty($this->filters['pat_descricao'])) {
            $where[0] .= ' AND p.pat_descricao LIKE ?';
            $where[1][] = '%' . $this->filters['pat_descricao'] . '%';
        }

        if ($this->filters['pat_categoria_id'] > 0) {
            $where[0] .= ' AND p.pat_categoria_id = ?';
            $where[1][] = $this->filters['pat_categoria_id'];
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $registros = $patrimonioDAO->getArray($where, $orderBy ?? 'pat_id ASC', $limit, $offset);

        $data = [];
        $total = 0;

        if (!empty($registros)) {
            $total = $registros[0]['total'] ?? count($registros);

            foreach ($registros as $reg) {
                $buttons = L::buttonGroup([
                    L::linkButton('', "?posicao=form&pat_id={$reg['pat_id']}", 'Editar Patrimônio', 'fas fa-edit', 'outline-primary', 'sm'),
                    L::button('', "saida({$reg['pat_id']})", 'Saída de Patrimônio', 'fas fa-sign-out-alt', 'outline-info', 'sm'),
                    L::button('', "historico({$reg['pat_id']})", 'Histórico do Patrimônio', 'fas fa-file-alt', 'outline-success', 'sm')
                ]);

                $data[] = array(
                    $reg['pat_id'],
                    $reg['pat_descricao'],
                    $reg['pat_marca'],
                    $reg['cpa_titulo'],
                    $reg['pat_quantidade'],
                    $patrimonioDAO->getConservacao($reg['pat_conservacao']),
                    $reg['usu_nome'],
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
