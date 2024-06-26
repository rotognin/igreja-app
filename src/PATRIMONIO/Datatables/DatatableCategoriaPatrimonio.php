<?php

namespace App\PATRIMONIO\Datatables;

use App\PATRIMONIO\DAO\CategoriaPatrimonio;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;

class DatatableCategoriaPatrimonio extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'cpa_titulo' => '',
            'cpa_ativo' => ''
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
                ['name' => 'cpa_id'],
                ['name' => 'cpa_titulo'],
                ['name' => 'cpa_descricao'],
                ['name' => 'cpa_ativo'],
                ['name' => 'acoes']
            ],
            'order' => [[0, 'asc']],
            'columnDefs' => [
                ['targets' => [0, 3, 4], 'className' => 'text-center'],
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
        $table->setAttrs(['id' => 'tabela-categorias']);
        $table->setSize('sm');
        $table->setFooter(false);

        $table->addHeader([
            'cols' => [
                ['value' => 'Código', 'attr' => ['class' => 'text-center']],
                ['value' => 'Título', 'attr' => ['class' => 'text-center']],
                ['value' => 'Descrição', 'attr' => ['class' => 'text-center']],
                ['value' => 'Ativo', 'attr' => ['class' => 'text-center']],
                ['value' => 'Ações', 'attr' => ['class' => 'text-center']]
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $categoriaDAO = new CategoriaPatrimonio();

        $where = ['', []];

        if (!empty($this->filters['cpa_titulo'])) {
            $where[0] .= ' AND cpa_titulo LIKE ?';
            $where[1][] = '%' . $this->filters['cpa_titulo'] . '%';
        }

        if ($this->filters['cpa_ativo'] != 'T') {
            $where[0] .= ' AND cpa_ativo = ?';
            $where[1][] = $this->filters['cpa_ativo'];
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $registros = $categoriaDAO->getArray($where, $orderBy ?? 'cpa_id ASC', $limit, $offset);

        $data = [];
        $total = 0;

        if (!empty($registros)) {
            $total = $registros[0]['total'] ?? count($registros);

            foreach ($registros as $reg) {
                // Checar se a categoria está atrelada em algum Patrimônio. Se tiver, não poderá deixar excluir

                $buttons = L::buttonGroup([
                    L::linkButton('', "?posicao=form&cpa_id={$reg['cpa_id']}", 'Editar Categoria', 'fas fa-edit', 'outline-secondary', 'sm'),
                    L::button('', "excluirCategoria({$reg['cpa_id']})", 'Excluir Categoria', 'fas fa-trash', 'outline-danger', 'sm')
                ]);

                $data[] = array(
                    $reg['cpa_id'],
                    $reg['cpa_titulo'],
                    $reg['cpa_descricao'],
                    ($reg['cpa_ativo'] == 'S') ? 'Sim' : 'Não',
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
