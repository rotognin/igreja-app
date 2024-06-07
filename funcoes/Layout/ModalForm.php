<?php

namespace Funcoes\Layout;

class ModalForm extends Form
{
    private string $modalID = "";
    private string $modalSize = "";

    public function __construct(string $modalID)
    {
        $this->modalID = $modalID;
    }

    public function setModalSize(string $size)
    {
        $this->modalSize = $size;
    }

    public function html()
    {
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

        $actions = "";
        if (!empty($this->actions)) {
            $actions = <<<HTML
            <div class="modal-footer">
                $this->actions
            </div>
            HTML;
        }

        $header = '';

        if ($this->title) {
            $header = <<<HTML
                <div class="modal-header">
                    <h5 class="modal-title" id="{$this->modalID}-label">{$this->title}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            HTML;
        }

        $form = <<<HTML
        $hidden
        <div class="modal fade" id="{$this->modalID}" tabindex="-1" role="dialog" aria-labelledby="{$this->modalID}-label" aria-hidden="true">
            <div class="modal-dialog {$this->modalSize}" role="document">
                <div class="modal-content">
                    $header 
                    <div class="modal-body">
                        $fields
                    </div>
                    $actions   
                </div>
            </div>
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
