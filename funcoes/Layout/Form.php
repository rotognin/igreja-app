<?php

namespace Funcoes\Layout;

class Form
{
    protected string $title;
    protected string $form = "";
    protected string $onSubmit = "";
    protected string $actions = "";
    protected bool $collapsable = false;
    protected bool $collapsed = false;
    protected array $hidden = [];
    protected array $fields = [];
    protected bool $cardFormat = true;

    public function addHidden(string $element)
    {
        $this->hidden[] = $element;
    }

    public function setCardFormat(bool $cardFormat = true)
    {
        $this->cardFormat = $cardFormat;
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function setActions(string $actions)
    {
        $this->actions = $actions;
    }

    public function setCollapsable(bool $collapsable)
    {
        $this->collapsable = $collapsable;
    }

    public function setCollapsed(bool $collapsed)
    {
        $this->collapsed = $collapsed;
    }

    public function setForm($attrs, $onSubmit = "")
    {
        $this->form = $attrs;
        $this->onSubmit = $onSubmit;
    }

    public function html()
    {
        $formID = uniqid('form');

        $hidden = "";
        foreach ($this->hidden as $element) {
            $hidden .= $element;
        }

        $fields = "";
        foreach ($this->fields as $row) {
            if (empty($row)) {
                continue;
            }
            $fields .= "<div class=\"row\">";
            foreach ($row as $col) {
                $fields .= "<div class=\"col\">$col</div>";
            }
            $fields .= "</div>";
        }

        $collapseHTML = "";
        if ($this->collapsable) {
            $collapseHTML = <<<HTML
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            HTML;

            if ($this->collapsed) {
                $collapseHTML .= <<<Javascript
                <script>
                    $(function() {
                        $('#$formID').CardWidget('collapse');
                    });
                </script>
                Javascript;
            }
        }

        $collapsed = $this->collapsed ? 'style="display:none"' : '';

        $cardFooter = ($this->cardFormat) ? 'card-footer' : '';

        $actions = "";
        if (!empty($this->actions)) {
            $actions = <<<HTML
            <div class="$cardFooter" $collapsed>
                $this->actions
            </div>
            HTML;
        }

        $cardClass = ($this->cardFormat) ? 'card card-primary card-outline' : '';
        $cardHeader = '';
        $cardBody = '';

        if ($this->cardFormat) {
            $cardHeader = <<<HEADER
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">$this->title</h3>
                    $collapseHTML
                </div>
            HEADER;

            $cardBody = 'card-body';
        }

        $form = <<<HTML
        $hidden
        <div class="$cardClass" id="$formID">
            $cardHeader
            <div class="$cardBody" $collapsed>
                $fields
            </div>
            $actions
        </div>
        HTML;

        if (!empty($this->form)) {
            $form = <<<HTML
            <form $this->form onsubmit="$this->onSubmit" autocomplete="off" novalidate="novalidate">
                $form
            </form>
            HTML;
        }
        return $form;
    }
}
