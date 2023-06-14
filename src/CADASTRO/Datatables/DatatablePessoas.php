<?php

namespace App\CADASTRO\Datatables;

use App\CADASTRO\DAO\Pessoas;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;

class DatatablePessoas extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'pes_nome' => '',
            'pes_bairro' => ''
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
                ['name' => 'pes_id'],
                ['name' => 'pes_nome'],
                ['name' => 'pes_telefone'],
                ['name' => 'pes_bairro'],
                ['name' => 'pes_cidade'],
                ['name' => 'pes_estado'],
                ['name' => 'pes_familia_id'],
                ['name' => 'acoes']
            ],
            'order' => [[1, 'asc']],
            'columnDefs' => [
                ['targets' => [0, 5, 7], 'className' => 'text-center'],
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
        $table->setAttrs(['id' => 'tabela-pessoas']);
        $table->setSize('sm');
        $table->setFooter(false);

        $table->addHeader([
            'cols' => [
                ['value' => _('Código')],
                ['value' => _('Nome')],
                ['value' => _('Telefone')],
                ['value' => _('Bairro')],
                ['value' => _('Cidade')],
                ['value' => _('Estado')],
                ['value' => _('Família')],
                ['value' => _('Ações')]
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $pessoasDAO = new Pessoas();

        $where = ['', []];

        if (!empty($this->filters['pes_nome'])) {
            $where[0] .= ' AND pes_nome LIKE ?';
            $where[1][] = '%' . $this->filters['pes_nome'] . '%';
        }

        if (!empty($this->filters['pes_bairro'])) {
            $where[0] .= ' AND pes_bairro LIKE ?';
            $where[1][] = '%' . $this->filters['pes_bairro'] . '%';
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $total = $pessoasDAO->total($where);
        $registros = $pessoasDAO->getArray($where, $orderBy ?? 'pes_nome ASC', $limit, $offset);

        $data = [];

        if ($total > 0) {
            foreach ($registros as $reg) {
                $buttons = L::buttonGroup([
                    L::linkButton('', "?posicao=form&pes_id={$reg['pes_id']}", _('Editar Pessoa'), 'fas fa-edit', 'outline-secondary', 'sm'),
                    L::button('', "excluirPessoa({$reg['pes_id']})", _('Excluir Pessoa'), 'fas fa-trash', 'outline-danger', 'sm')
                ]);

                $data[] = array(
                    $reg['pes_id'],
                    $reg['pes_nome'],
                    $reg['pes_telefone'],
                    $reg['pes_bairro'],
                    $reg['pes_cidade'],
                    $reg['pes_estado'],
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
