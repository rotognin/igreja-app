<?php

namespace App\SGC\Datatables;

use App\SGC\DAO\LogPrograma;
use Funcoes\Helpers\Format;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;

class DatatableSGC01 extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'usu_login' => '',
            'prg_codigo' => '',
            'prg_url' => '',
            'log_ip' => '',
            'log_navegador' => '',
            'log_datahora' => date('d/m/Y H:i', strtotime('-1 month')) . ' - ' . date('d/m/Y H:i'),
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
                ['name' => 'log_datahora'],
                ['name' => 'usu_login'],
                ['name' => 'log_ip'],
                ['name' => 'log_navegador'],
                ['name' => 'prg_codigo'],
            ],
            'order' => [[0, 'desc']],
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
        $table->addHeader([
            'cols' => [
                ['value' => 'Data', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Usuário'],
                ['value' => 'IP', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Navegador'],
                ['value' => 'Programa']
            ]
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $logDAO = new LogPrograma();

        list($inicio, $fim) = explode(' - ', $this->filters['log_datahora']);
        $inicio = Format::sqlDatetime($inicio, 'd/m/Y H:i');
        $fim = Format::sqlDatetime($fim, 'd/m/Y H:i');

        $where = [" AND l.log_datahora BETWEEN ? AND ?", [$inicio, $fim]];

        if (!empty($this->filters['usu_login'])) {
            $where[0] .= " AND l.usu_login = ?";
            $where[1][] = $this->filters['usu_login'];
        }

        if (!empty($this->filters['prg_codigo'])) {
            $where[0] .= " AND l.prg_codigo = ?";
            $where[1][] = $this->filters['prg_codigo'];
        }

        if (!empty($this->filters['prg_url'])) {
            $where[0] .= " AND p.prg_url LIKE ?";
            $where[1][] = "%{$this->filters['prg_url']}%";
        }

        if (!empty($this->filters['log_ip'])) {
            $where[0] .= " AND l.log_ip LIKE ?";
            $where[1][] = "%{$this->filters['log_ip']}%";
        }

        if (!empty($this->filters['log_navegador'])) {
            $where[0] .= " AND l.log_navegador LIKE ?";
            $where[1][] = "%{$this->filters['log_navegador']}%";
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $registros = $logDAO->getArray($where, $orderBy ?? 'l.log_datahora desc', $limit, $offset);

        $data = [];
        $total = 0;

        if (!empty($registros)) {
            $total = $registros[0]['total'] ?? count($registros);

            foreach ($registros as $reg) {
                $data[] = [
                    Format::datetime($reg['log_datahora']),
                    "<small class='text-muted'>{$reg['usu_login']}</small><br>{$reg['usu_nome']}",
                    $reg['log_ip'],
                    $reg['log_navegador'],
                    "<small class='text-muted'>{$reg['prg_codigo']} - {$reg['prg_url']}</small><br>{$reg['prg_descricao']}"
                ];
            }
        }

        return [
            'data' => $data,
            'total' => $total,
        ];
    }
}
