<?php

namespace App\CADASTRO\Datatables;

use App\CADASTRO\DAO\Membros;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;

class DatatableMembros extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'mem_nome' => '',
            'mem_bairro' => ''
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
                ['name' => 'mem_id'],
                ['name' => 'mem_nome'],
                ['name' => 'mem_telefone'],
                ['name' => 'mem_email'],
                ['name' => 'mem_familia_id'],
                ['name' => 'acoes']
            ],
            'order' => [[1, 'asc']],
            'columnDefs' => [
                ['targets' => [0, 5], 'className' => 'text-center'],
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
        $table->setAttrs(['id' => 'tabela-membros']);
        $table->setSize('sm');
        $table->setFooter(false);

        $table->addHeader([
            'cols' => [
                ['value' => _('Código')],
                ['value' => _('Nome')],
                ['value' => _('Telefone')],
                ['value' => _('E-mail')],
                ['value' => _('Família')],
                ['value' => _('Ações')]
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $membrosDAO = new Membros();

        $where = ['', []];

        if (!empty($this->filters['mem_nome'])) {
            $where[0] .= ' AND mem_nome LIKE ?';
            $where[1][] = '%' . $this->filters['mem_nome'] . '%';
        }

        if (!empty($this->filters['mem_bairro'])) {
            $where[0] .= ' AND mem_bairro LIKE ?';
            $where[1][] = '%' . $this->filters['mem_bairro'] . '%';
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $total = $membrosDAO->total($where);
        $registros = $membrosDAO->getArray($where, $orderBy ?? 'mem_nome ASC', $limit, $offset);

        $data = [];

        if ($total > 0) {
            foreach ($registros as $reg) {
                $buttons = L::buttonGroup([
                    L::linkButton('', "?posicao=form&mem_id={$reg['mem_id']}", _('Editar Membro'), 'fas fa-edit', 'outline-secondary', 'sm'),
                    L::button('', "excluirMembro({$reg['mem_id']})", _('Excluir Membro'), 'fas fa-trash', 'outline-danger', 'sm')
                ]);

                $data[] = array(
                    $reg['mem_id'],
                    $reg['mem_nome'],
                    $reg['mem_telefone'],
                    $reg['mem_email'],
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
