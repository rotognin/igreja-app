<?php

namespace Funcoes\Layout;

use Funcoes\Helpers\HTML;

class Table
{
    protected string $id = "";

    protected bool $card = true;
    protected bool $responsive = true;
    protected bool $striped = true;
    protected bool $bordered = false;
    protected bool $hasfooter = true;
    protected string $size = "md";

    protected array $attrs = [];
    protected array $header = [];
    protected array $rows = [];

    public function __construct($id = "")
    {
        if (empty($id)) {
            $id = "table-" . uniqid();
        }
        $this->id = $id;
    }

    public function setFooter(bool $hasfooter)
    {
        $this->hasfooter = $hasfooter;
    }

    public function setResponsive(bool $responsive)
    {
        $this->responsive = $responsive;
    }

    public function setStriped(bool $striped)
    {
        $this->striped = $striped;
    }

    public function setBordered(bool $bordered)
    {
        $this->bordered = $bordered;
    }

    public function setSize(string $size)
    {
        $this->size = $size;
    }

    public function setCard($card)
    {
        $this->card = $card;
    }

    public function setAttrs(array $attrs)
    {
        $this->attrs = $attrs;
    }

    public function addHeader(array $header)
    {
        $this->header[] = $header;
    }

    public function addRow(array $row)
    {
        $this->rows[] = $row;
    }

    public function html()
    {
        if (isset($this->attrs['id'])) {
            $this->id = $this->attrs['id'];
            unset($this->attrs['id']);
        }

        $responsive = $this->responsive ? "table-responsive" : "";
        $striped = $this->striped ? "table-striped" : "";
        $bordered = $this->bordered ? "table-bordered" : "";
        $size = "table-{$this->size}";

        $classes = "table $striped $bordered $size";
        if (!empty($this->attrs['class'])) {
            $classes = $this->attrs['class'];
            unset($this->attrs['class']);
        }

        $header = "";
        foreach ($this->header as $row) {
            $data = $row['attrs']['data'] ?? [];
            unset($row['attrs']['data']);
            $attrs = HTML::attrs($row['attrs'] ?? []);
            $data = HTML::dataAttrs($data);
            $header .= "<tr $attrs $data>";
            foreach ($row['cols'] as $col) {
                $data = $col['attrs']['data'] ?? [];
                unset($col['attrs']['data']);
                $attrs = HTML::attrs($col['attrs'] ?? []);
                $data = HTML::dataAttrs($data);
                $header .= "<th $attrs $data>{$col['value']}</th>";
            }
            $header .= "</tr>";
        }

        $rows = "";
        foreach ($this->rows as $row) {
            $data = $row['attrs']['data'] ?? [];
            unset($row['attrs']['data']);
            $attrs = HTML::attrs($row['attrs'] ?? []);
            $data = HTML::dataAttrs($data);
            $rows .= "<tr $attrs $data>";
            foreach ($row['cols'] as $col) {
                $data = $col['attrs']['data'] ?? [];
                unset($col['attrs']['data']);
                $attrs = HTML::attrs($col['attrs'] ?? []);
                $data = HTML::dataAttrs($data);
                $rows .= "<td $attrs>{$col['value']}</td>";
            }
            $rows .= "</tr>";
        }

        $data = $this->attrs['data'] ?? [];
        unset($this->attrs['data']);
        $attrs = HTML::attrs($this->attrs);
        $data = HTML::dataAttrs($data);

        $footer = '';

        if ($this->hasfooter) {
            $footer = "<tfoot>$header</tfoot>";
        }

        $table = <<<HTML
        <table class="$classes" $attrs $data>
            <thead>
                $header
            </thead>
            <tbody>
                $rows
            </tbody>
            $footer
        </table>
        HTML;

        if ($this->card) {
            return <<<HTML
            <div class="card table-card" id="$this->id">
                <div class="card-body p-0 $responsive">
                    $table
                </div>
            </div>
            HTML;
        }

        return <<<HTML
            <div class="$responsive" id="$this->id">
                $table
            </div>
        HTML;
    }
}
