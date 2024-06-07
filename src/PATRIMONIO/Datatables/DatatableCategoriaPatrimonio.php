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
            'order' => [[1, 'asc']],
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
        $table->setAttrs(['id' => 'tabela-ministerios']);
        $table->setSize('sm');
        $table->setFooter(false);

        $table->addHeader([
            'cols' => [
                ['value' => 'Código', 'attr' => ['class' => 'text-center']],
                ['value' => 'Nome', 'attr' => ['class' => 'text-center']],
                ['value' => 'Sigla', 'attr' => ['class' => 'text-center']],
                ['value' => 'Ativo', 'attr' => ['class' => 'text-center']],
                ['value' => 'Ações', 'attr' => ['class' => 'text-center']]
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $ministeriosDAO = new Ministerios();

        $where = ['', []];

        if (!empty($this->filters['min_nome'])) {
            $where[0] .= ' AND min_nome LIKE ?';
            $where[1][] = '%' . $this->filters['min_nome'] . '%';
        }

        if (!empty($this->filters['min_sigla'])) {
            $where[0] .= ' AND min_sigla = ?';
            $where[1][] = $this->filters['min_sigla'];
        }

        if ($this->filters['min_ativo'] != 'T') {
            $where[0] .= ' AND min_ativo = ?';
            $where[1][] = $this->filters['min_ativo'];
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        //$total = $ministeriosDAO->total($where);
        $registros = $ministeriosDAO->getArray($where, $orderBy ?? 'min_id ASC', $limit, $offset);

        $data = [];
        $total = 0;

        if (!empty($registros)) {
            $total = $registros[0]['total'] ?? count($registros);

            foreach ($registros as $reg) {
                $buttons = L::buttonGroup([
                    L::linkButton('', "?posicao=form&min_id={$reg['min_id']}", _('Editar Ministério'), 'fas fa-edit', 'outline-secondary', 'sm'),
                    L::button('', "excluirMinisterio({$reg['min_id']})", _('Excluir Ministério'), 'fas fa-trash', 'outline-danger', 'sm')
                ]);

                $data[] = array(
                    $reg['min_id'],
                    $reg['min_nome'],
                    $reg['min_sigla'],
                    ($reg['min_ativo'] == 'S') ? 'Sim' : 'Não',
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
