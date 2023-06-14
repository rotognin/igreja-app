<?php

namespace Funcoes\Layout;

use App\SGC\DAO\Menu;

class NavMenu
{
    public static function render()
    {
        global $request;
        global $activeUser;
        $menuDAO = new Menu();

        $programas = $menuDAO->getArray([" AND prg_ativo = 'S'"]);

        $html = "";

        if (!empty($programas)) {
            $tree = \Funcoes\Helpers\Tree::buildTree($programas, 'prg_codigo', 'prg_codigo_pai');
            $html = <<<HTML
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar nav-compact flex-column" data-widget="treeview" role="menu" data-accordion="false">
            HTML;

            foreach ($tree as $menu) {
                if (!empty($menu['aca_acao']) && !$activeUser->checkAction($menu['aca_acao'])) {
                    continue;
                }
                $caret = !empty($menu['children']) ? '<i class="right fas fa-angle-left"></i>' : '';

                $childrenHTML = self::renderChildren($menu['children'] ?? []);

                if (empty($childrenHTML) && empty($menu['prg_url'])) {
                    continue;
                }

                $active = !empty($menu['prg_url']) && strpos($request->server('SCRIPT_NAME'), $menu['prg_url']) !== false ? 'active' : '';

                $html .= <<<HTML
                <li class="nav-item">
                    <a href="{$menu['prg_url']}" class="d-flex align-items-center nav-link $active" title="{$menu['prg_descricao']}">
                        <i class="nav-icon {$menu['prg_icone']}"></i>
                        <p class="d-inline-block text-truncate" style="padding-left: 5px; white-space:nowrap;max-width: 75%">
                            {$menu['prg_descricao']}
                            $caret
                        </p>
                    </a>
                    $childrenHTML
                </li>
                HTML;
            }

            $html .= <<<HTML
                </ul>
            </nav>
            HTML;
        }

        return $html;
    }

    private static function renderChildren($children)
    {
        global $request;
        global $activeUser;

        $html = "";

        if (!empty($children)) {
            $prgHTML = "";

            foreach ($children as $child) {
                if (!empty($child['aca_acao']) && !$activeUser->checkAction($child['aca_acao'])) {
                    continue;
                }

                $caret = !empty($child['children']) ? '<i class="right fas fa-angle-left"></i>' : '';

                $childrenHTML = self::renderChildren($child['children'] ?? []);

                if (empty($childrenHTML) && empty($child['prg_url'])) {
                    continue;
                }

                $active = !empty($child['prg_url']) && strpos($request->server('SCRIPT_NAME'), $child['prg_url']) !== false ? 'active' : '';

                $prgHTML .= <<<HTML
                <li class="nav-item">
                    <a href="{$child['prg_url']}" class="d-flex align-items-center nav-link $active" title="{$child['prg_descricao']}">
                        <i class="nav-icon {$child['prg_icone']}"></i>
                        <p class="d-inline-block text-truncate" style="padding-left: 5px; white-space:nowrap;max-width: 75%">
                            {$child['prg_descricao']}
                            $caret
                        </p>
                    </a>
                    $childrenHTML
                </li>
                HTML;
            }

            if (!empty($prgHTML)) {
                $html .= <<<HTML
                <ul class="nav nav-treeview">
                    $prgHTML
                </ul>
                HTML;
            }
        }

        return $html;
    }
}
