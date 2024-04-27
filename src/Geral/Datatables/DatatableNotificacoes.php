<?php

namespace App\Geral\Datatables;

use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;

class DatatableNotificacoes extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'not_mensagem' => '',
            'not_data_inc_inicio' => '',
            'not_data_inc_fim' => '',
        ];

        //Definições das opções do datatable dando merge com as opções padrão
        $this->setOptions([
            'columns' => [
                ['name' => 'not_mensagem'],
                ['name' => 'not_data_inc'],
                ['name' => 'acoes'],
            ],
            'order' => [[1, 'desc']],
            'columnDefs' => [
                ['targets' => [1, 2], 'className' => 'text-center'],
                ['targets' => [2], 'orderable' => false],
            ],
        ]);

        //carregar os filtros a partir da requisição
        $this->loadFilters();
    }

    public function tableConfig(Datatable $table)
    {
        $table->addHeader([
            'cols' => [
                ['value' => 'Mensagem'],
                ['value' => 'Data', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Ações', 'attrs' => ['class' => 'text-center']],
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $notificacaoDAO = new \Funcoes\DAO\Notificacao();
        global $activeUser;
        $registros = $notificacaoDAO->getArray([" AND n.not_tipo_destino = ? AND n.not_id_destino = ?", [$activeUser->getTipoNotificavel(), $activeUser->getID()]], $limit, $offset, $orderBy ?? 'not_data_inc desc');

        $data = [];
        $total = 0;

        if (!empty($registros)) {
            $total = $registros[0]['total'];

            foreach ($registros as $reg) {
                $bold = empty($reg['not_data_lida']) ? 'font-weight-bold' : '';
                $buttons = [];
                if (empty($reg['not_data_lida'])) {
                    $buttons[] = L::button('', "ler({$reg['not_id']}, this)", 'Marcar como lida', 'fas fa-check', 'success', 'sm');
                }
                $buttons[] = L::linkButton('', "/geral/lerNotificacao.php?not_id={$reg['not_id']}", 'Ver', 'fas fa-eye', 'default', 'sm');
                $data[] = [
                    "<span class=\"$bold\"><i class=\"{$reg['not_icone']} mr-2 text-lg\"></i>{$reg['not_mensagem']}</span>",
                    "<span class=\"$bold\">" . \Funcoes\Helpers\Format::datetime($reg['not_data_inc'], 'd/m/Y H:i') . "</span>",
                    L::buttonGroup($buttons),
                ];
            }
        }

        return [
            'data' => $data,
            'total' => $total,
        ];
    }
}
