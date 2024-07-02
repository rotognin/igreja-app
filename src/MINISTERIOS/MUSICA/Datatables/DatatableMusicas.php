<?php

namespace App\MINISTERIOS\MUSICA\Datatables;

use App\MINISTERIOS\MUSICA\DAO\Musicas;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\Datatables\Definitions;
use Funcoes\Layout\Layout as L;

class DatatableMusicas extends Definitions
{
    public function __construct($tableID = "")
    {
        parent::__construct($tableID);

        //Definição de filtros e valores padrão
        $this->filters = [
            'mus_nome' => '',
            'mus_situacao' => '',
            'mus_categoria_id' => ''
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
                ['name' => 'mus_id'],
                ['name' => 'mus_nome'],
                ['name' => 'mus_artista'],
                ['name' => 'mus_categoria_id'],
                ['name' => 'mus_situacao'],
                ['name' => 'acoes'],
            ],
            'order' => [[0, 'asc']],
            'columnDefs' => [
                ['targets' => [0, 4, 5], 'className' => 'text-center'],
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
        $table->setAttrs(['id' => 'tabela-musicas']);
        $table->setSize('sm');
        $table->setFooter(false);

        $table->addHeader([
            'cols' => [
                ['value' => 'Código', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Nome', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Artista', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Categoria', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Situação', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Ações', 'attrs' => ['class' => 'text-center']]
            ],
        ]);
    }

    public function getData($limit, $offset, $orderBy)
    {
        $musicasDAO = new Musicas();

        $where = ['', []];

        if ($this->filters['mus_nome'] != '') {
            $where[0] .= ' AND mus_nome LIKE ?';
            $where[1][] = '%' . $this->filters['mus_nome'] . '%';
        }

        if ($this->filters['mus_situacao'] != 'T') {
            $where[0] .= ' AND mus_situacao = ?';
            $where[1][] = $this->filters['mus_situacao'];
        }

        if ($this->filters['mus_categoria_id'] != '') {
            $where[0] .= ' AND mus_categoria_id = ?';
            $where[1][] = $this->filters['mus_categoria_id'];
        }

        if ($limit == -1) {
            $limit = 0;
            $offset = 0;
        }

        $registros = $musicasDAO->getArray($where, $orderBy ?? ' mus_id ASC ', $limit, $offset);

        $data = [];
        $total = 0;

        if (!empty($registros)) {
            $total = $registros[0]['total'] ?? count($registros);

            foreach ($registros as $reg) {
                $buttons = L::buttonGroup([
                    L::linkButton('', "?posicao=form&mus_id={$reg['mus_id']}", 'Editar Música', 'fas fa-edit', 'outline-info', 'sm'),
                    L::button('', "abrir('{$reg['mus_link']}')", 'Abrir Música', 'fas fa-play-circle', 'outline-success', 'sm')
                ]);

                $data[] = array(
                    $reg['mus_id'],
                    $reg['mus_nome'],
                    $reg['mus_artista'],
                    $reg['cam_descricao'],
                    $musicasDAO->getSituacao($reg['mus_situacao']),
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
