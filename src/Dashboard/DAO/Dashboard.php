<?php

namespace App\Dashboard\DAO;

use App\SGC\DAO\LogPrograma;
use App\SGC\DAO\Programa;
use Funcoes\Lib\DAO;

class Dashboard extends DAO
{
    private LogPrograma $log;

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');

        $this->log = new LogPrograma();
    }

    public function inicio()
    {
        return '<hr><div class="card-deck p-0 m-0">';
    }

    private function obterPais($prg_codigo_pai = 0)
    {
        $html = '';

        if (intval($prg_codigo_pai) == 0) {
            return '';
        }

        $sair = false;

        $programaDAO = new Programa();

        while (!$sair) {
            $aPrograma = $programaDAO->get($prg_codigo_pai);

            if (empty($aPrograma)) {
                $sair = true;
            } else {
                $html = $aPrograma['prg_descricao'] . ' > ' . $html;

                if ($aPrograma['prg_codigo_pai'] > 0) {
                    $prg_codigo_pai = $aPrograma['prg_codigo_pai'];
                } else {
                    $sair = true;
                }
            }
        }

        if (!empty($html)) {
            $html = substr($html, 0, -2);
        }

        return $html;
    }

    public function montarMaisAcessados(string $usuario)
    {
        $where = array('');
        $where[0] = ' AND l.usu_login = ?';
        $where[1][] = $usuario;
        $array = $this->log->getMaisAcessados($where);

        $html = '';
        $html = '<div class="card ml-3">';
        $html .= '<h5 class="card-header text-white bg-secondary">Programas mais acessados</h5>';
        $html .= '<div class="card-body">';

        if ($array) {
            foreach ($array as $programa) {
                $html .= '<a class="text-reset list-group-item list-group-item-action" href="' . $programa['prg_url'] . '">';

                $caminho = $this->obterPais($programa['prg_codigo_pai']);

                $html .= '<h6 class="card-subtitle mb-2 font-weight-lighter">' . $caminho . '</h6>';
                $html .= '<p class="card-text p-0 mb-1"><i class="' . $programa['prg_icone'] . '"></i>&nbsp;&nbsp;&nbsp;' . $programa['prg_descricao'] . '</p>';
                $html .= '</a>';
            }
        }

        $html .= '</div></div>';

        return $html;
    }

    public function montarUltimosAcessos(string $usuario)
    {
        $where = array('');
        $where[0] = ' AND l.usu_login = ?';
        $where[1][] = $usuario;

        $offset = 0;
        $limit = 5;
        $array = [];
        $contagem = 0;
        $sair = false;

        while (!$sair) {
            $retorno = $this->log->getArray($where, 'log_datahora DESC', $limit, $offset);

            if (empty($retorno)) {
                $sair = true;
            } else {
                foreach ($retorno as $registro) {
                    if (!array_key_exists($registro['prg_codigo'], $array)) {
                        $array[$registro['prg_codigo']] = $registro;
                        $contagem += 1;

                        if ($contagem == 5) {
                            $sair = true;
                            break;
                        }
                    }
                }

                $offset += 5;
            }
        }

        $html = '';
        $html = '<div class="card mr-3">';
        $html .= '<h5 class="card-header text-white bg-secondary">Acessados recentemente</h5>';
        $html .= '<div class="card-body">';

        if ($array) {
            foreach ($array as $programa) {
                $html .= '<a class="text-reset list-group-item list-group-item-action" href="' . $programa['prg_url'] . '">';

                $caminho = $this->obterPais($programa['prg_codigo_pai']);

                $html .= '<h6 class="card-subtitle mb-2 font-weight-lighter">' . $caminho . '</h6>';
                $html .= '<p class="card-text p-0 mb-1"><i class="' . $programa['prg_icone'] . '"></i>&nbsp;&nbsp;&nbsp;' . $programa['prg_descricao'] . '</p>';
                $html .= '</a>';
            }
        }

        $html .= '</div></div>';

        return $html;
    }

    public function fechamento()
    {
        return '</div>';
    }
}
