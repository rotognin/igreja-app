<?php

namespace Funcoes\Layout;

use Funcoes\Lib\Datatables\Definitions;

class Datatable extends Table
{
    private Definitions $defs;

    public function __construct($defs, $id = "")
    {
        parent::__construct($id);
        $this->defs = new $defs($this->id);
        $this->defs->tableConfig($this);
    }

    public function addFilters($filters = [])
    {
        if (!empty($filters)) {
            $this->defs->addFilters($filters);
        }
    }

    public function html()
    {
        $html = parent::html();
        $opts = $this->defs->getOptions();

        $onInit = $opts['initComplete'] ?? '';
        unset($opts['initComplete']);

        $drawCallback = $opts['drawCallback'] ?? '0';
        unset($opts['drawCallback']);

        $infoCallback = $opts['infoCallback'] ?? '0';
        unset($opts['infoCallback']);

        $options = json_encode($this->defs->getOptions());

        return <<<HTML
        $html
        <script>
            $(function() {
                const options = $options;
                options.initComplete = $onInit;
                options.drawCallback = $drawCallback;
                options.infoCallback = $infoCallback;

                if (options.drawCallback == '0') {
                    delete options.drawCallback;
                }

                if (options.infoCallback == '0') {
                    delete options.infoCallback;
                }

                const dtable = $('#{$this->id} table').DataTable(options);
                $("#{$this->id} table").css('width', '100%');
            });
        </script>
        HTML;
    }
}
