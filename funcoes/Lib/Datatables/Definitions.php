<?php

namespace Funcoes\Lib\Datatables;

use Funcoes\Layout\Datatable;

abstract class Definitions
{
    protected string $tableID;
    protected array $filters = [];

    //opções padrão do datatables
    protected array $options;

    public function __construct($tableID = "")
    {
        if ($tableID == "") {
            $tableID = uniqid();
        }
        $this->tableID = $tableID;

        $this->options  = [
            'serverSide' => true,
            'processing' => true,
            'ordering' => true,
            'order' => [[0, 'asc']],
            'filter' => false,
            'autoWidth' => true,
            'info' => true,
            'buttons' => ['copy', 'csv', 'excel'],
            'language' => [
                'url' => 'https://cdn.datatables.net/plug-ins/1.12.1/i18n/pt-BR.json',
            ],
            'initComplete' => "function() {
                this.api().buttons().container().appendTo(\"#{$this->tableID}.dataTables_wrapper .col-md-6:eq(1)\");
            }",
        ];
    }

    protected function loadFilters()
    {
        global $request;
        foreach ($this->filters as $field => $value) {
            $this->filters[$field] = $request->get($field, $value);
        }

        $this->parseURL();
    }

    public function addFilters($filters)
    {
        foreach ($filters as $def => $value) {
            $this->filters[$def] = $value;
        }

        $this->parseURL();
    }

    protected function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
    }

    protected function alterOption($option, $value)
    {
        if (!empty($option)) {
            $this->options[$option] = $value;
        }
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function parseURL()
    {
        return $this->options['ajax'] = "/geral/xhr/datatables-server.php?definitions=" . get_class($this) . "&" . http_build_query($this->filters);
    }

    public abstract function getData($limit, $offset, $orderBy);
    public abstract function tableConfig(Datatable $table);
}
