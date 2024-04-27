<?php

namespace Funcoes\Layout;

use App\SGC\DAO\Usuario;

class EstablishmentSelector
{
    public static function render(): string
    {
        global $session;
        global $activeUser;

        $usuarioDAO = new Usuario();
        $empresas = $usuarioDAO->getEmpresas($activeUser->data['usu_login']);

        if (empty($empresas)) {
            return '';
        }

        $dropdownItems = "";
        foreach ($empresas as $empresa) {
            $dropdownItems .= "<a class='dropdown-item' href='/sgc/trocarEmpresa.php?emp_codigo={$empresa['emp_codigo']}'>{$empresa['emp_codigo']} - {$empresa['emp_nome']}</a>";
        }

        $padrao = '<i>Sem empresa</i>';
        if ($empresa = $session->get('establishment', [])) {
            $padrao = "{$empresa['emp_codigo']} - {$empresa['emp_nome']} &nbsp;&nbsp;";
        }

        return <<<HTML
        <li class="nav-item dropdown">
            <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                <i class="far fa-building"></i> $padrao <i class="fas fa-caret-down"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">Selecionar empresa</span>
                <div class="dropdown-divider"></div>
                $dropdownItems
            </div>
        </li>
        HTML;
    }
}
