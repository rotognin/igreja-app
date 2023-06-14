<?php

namespace App\SGC\Datatables;

use App\SGC\DAO\Usuario;
use App\SGC\DAO\UsuarioPapel;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;

class DatatableUsuarios extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'usu_login' => '',
            'usu_nome' => '',
            'usu_celular_whatsapp' => '',
            'usu_ativo' => 'S',
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
                ['name' => 'usu_login'],
                ['name' => 'usu_nome'],
                ['name' => 'usu_celular'],
                ['name' => 'usu_celular_whatsapp'],
                ['name' => 'usu_ramal'],
                ['name' => 'usu_email'],
                ['name' => 'emp_codigo'],
                ['name' => 'usu_ativo'],
                ['name' => 'acoes'],
            ],
            'order' => [[0, 'asc']],
            'columnDefs' => [
                ['targets' => [0, 3, 4, 6, 7, 8], 'className' => 'text-center'],
                ['targets' => [8], 'orderable' => false],
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
        $table->addHeader([
            'cols' => [
                ['value' => 'Login', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Nome'],
                ['value' => 'Celular', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Whatsapp', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Ramal', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Email'],
                ['value' => 'Empresa padrão', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Ativo', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Ações', 'attrs' => ['class' => 'text-center']],
            ],
        ]);

        $table->setFooter(false);
        $table->setSize('sm');
    }

    public function getData($limit, $offset, $orderBy)
    {
        $dao = new Usuario();
        $usuPapelDAO = new UsuarioPapel();

        $where = ['', []];

        if (!empty($this->filters['usu_login'])) {
            $where[0] .= " AND u.usu_login LIKE ?";
            $where[1][] = "%{$this->filters['usu_login']}%";
        }

        if (!empty($this->filters['usu_nome'])) {
            $where[0] .= " AND u.usu_nome LIKE ?";
            $where[1][] = "%{$this->filters['usu_nome']}%";
        }
        if (!empty($this->filters['usu_celular_whatsapp'])) {
            $where[0] .= " AND u.usu_celular_whatsapp = ?";
            $where[1][] = $this->filters['usu_celular_whatsapp'];
        }
        if (!empty($this->filters['usu_ativo'])) {
            $where[0] .= " AND u.usu_ativo = ?";
            $where[1][] = $this->filters['usu_ativo'];
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $total = $dao->total($where);
        $registros = $dao->getArray($where, $orderBy ?? 'u.usu_login asc', $limit, $offset);

        $data = [];

        if (!empty($registros)) {
            foreach ($registros as $reg) {
                $papeis = "";
                $aUsuPapeis = $usuPapelDAO->getArrayCompleta([' AND x.usupap_usu_login = ?', [$reg['usu_login']]]);
                if (!empty($aUsuPapeis)) {
                    foreach ($aUsuPapeis as $papel) {
                        $papeis .= '<span class="badge badge-info">' . $papel['pap_descricao'] . '</span>&nbsp;&nbsp;';
                    }

                    $papeis = '<br>' . $papeis;
                }

                $data[] = [
                    $reg['usu_login'],
                    $reg['usu_nome'] . $papeis,
                    $reg['usu_celular'],
                    $reg['usu_celular_whatsapp'] == 'S' ? 'Sim' : 'Não',
                    $reg['usu_ramal'],
                    $reg['usu_email'],
                    "{$reg['emp_codigo']} - {$reg['emp_nome']}",
                    $reg['usu_ativo'] == 'S' ? 'Sim' : 'Não',
                    L::linkButton('', "?posicao=form&usu_login={$reg['usu_login']}", 'Editar usuário', 'fas fa-edit', 'outline-secondary', 'sm'),
                ];
            }
        }

        return [
            'data' => $data,
            'total' => $total,
        ];
    }
}
