<?php

namespace Funcoes\Layout;

class Layout
{
    public static function pageTitle($col1, $col2 = "", $row1 = "")
    {
        if (!empty($col2)) {
            $col2 = <<<HTML
            <div class="col text-right">
                $col2
            </div>
            HTML;
        }

        if (!empty($row1)) {
            $row1 = <<<HTML
            <div class="row mb-2">
                $row1
            </div>
            HTML;
        }

        return
            <<<HTML
            <div class="content-header" style="padding-bottom: 0px">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col">
                            $col1
                        </div>
                        $col2
                    </div>
                    $row1
                </div>
            </div>
            HTML;
    }

    public static function button($text, $onClick, $title = '', $icon = '', $type = 'default', $size = 'sm', $attrs = '', $addClass = '')
    {
        if (!empty($icon)) {
            $icon = "<i class=\"$icon\"></i>";
        }
        return
            <<<HTML
            <button class="btn btn-$type btn-$size $addClass" onclick="$onClick" title="$title" $attrs>
                $icon
                $text 
            </button>
            HTML;
    }

    public static function linkButton($text, $href, $title = "", $icon = '', $type = 'default', $size = 'sm', $addClass = '', $events = '', $attrs = '')
    {
        if (!empty($icon)) {
            $icon = "<i class=\"$icon\"></i>";
        }
        return
            <<<HTML
            <a class="btn btn-$type btn-$size $addClass" href="$href" title="$title" $events $attrs>
                $icon
                $text 
            </a>
            HTML;
    }

    public static function backButton($text = "Voltar", $icon = "fas fa-angle-left", $type = "default", $size = 'sm')
    {
        return self::button($text, 'window.history.back()', '', $icon, $type, $size);
    }

    public static function buttonGroup(array $buttons = [], bool $space = false)
    {
        $html = "";
        foreach ($buttons as $button) {
            $html .= $button;
            if ($space) {
                $html .= '&nbsp;';
            }
        }
        return
            <<<HTML
            <div class="btn-group">
                $html
            </div>
            HTML;
    }

    public static function submit($text, $icon = 'fas fa-check', $type = 'primary', $size = 'sm')
    {
        if (!empty($icon)) {
            $icon = "<i class=\"$icon\"></i>";
        }
        return
            <<<HTML
            <button class="btn btn-$type btn-$size" type="submit">
                $icon
                $text 
            </button>
            HTML;
    }

    public static function alert($type, $msg)
    {
        switch ($type) {
            case 'error':
                $icon = 'fas fa-circle-exclamation';
                break;
            case 'warning':
                $icon = 'fas fa-triangle-exclamation';
                break;
            case 'success':
                $icon = 'fas fa-circle-check';
                break;
            case 'info':
                $icon = 'fa fa-circle-info';
                break;
            default:
                $icon = "";
        }

        if ($icon) {
            $icon = "<i class=\"$icon mr-2\"></i>";
        }

        return <<<HTML
        <div class="alert alert-$type" role="alert">
            <div class="d-flex align-items-center">
                $icon <span>$msg</span>
            </div>
        </div>
        HTML;
    }

    public static function tab($text, $href, $title = "", $icon = '', $info = '', $id = '')
    {
        if (!empty($icon)) {
            $icon = "<i class=\"$icon\"></i>";
        }

        $id_info = '';
        $script = '';

        if (!empty($id)) {
            $id_info = 'id=' . $id;
            $script = <<<SCRIPT
                <script>
                    $('a#{$id}').click(function(){
                        displayOverlay('Aguarde...');
                    });
                </script>
            SCRIPT;
        }

        return
            <<<HTML
            <li class="nav-item">
                <a class="nav-link $info" href="$href" title="$title" {$id_info}>
                    $icon
                    $text 
                </a>
            </li>
            {$script}
            HTML;
    }

    public static function tabs(array $tabs = [])
    {
        if (empty($tabs)) {
            return '';
        }

        $html = '';

        foreach ($tabs as $tab) {
            $html .= $tab;
        }

        return
            <<<HTML
            <ul class="nav nav-tabs" style="padding-top: 10px">
                $html
            </ul>
            HTML;
    }
}
