<?php

namespace Funcoes\Helpers;

use DateTime;

class Format
{
    public static function datetime($source, $format = 'd/m/Y H:i:s'): string
    {
        if (!$source) {
            return '';
        }
        if (is_string($source)) {
            $source = strtotime($source);
        }
        return date($format, $source);
    }

    public static function date($source, $format = 'd/m/Y'): string
    {
        if (!$source) {
            return '';
        }
        if (is_string($source)) {
            $source = strtotime($source);
        }
        return date($format, $source);
    }

    public static function sqlDatetime($source, $sourceFormat = "d/m/Y H:i:s", $format = 'Y-m-d H:i:s',): string
    {
        if (!$source) {
            return '';
        }
        $dt = DateTime::createFromFormat($sourceFormat, $source);
        return $dt->format($format);
    }

    public static function cpf($cpf): string
    {
        if (strlen($cpf) != 11) {
            return $cpf;
        }

        $retorno = preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cpf);
        return $retorno;
    }

    public static function cnpj($cnpj): string
    {
        if (strlen($cnpj) != 14) {
            return $cnpj;
        }

        $retorno = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj);
        return $retorno;
    }

    public static function cep($cep)
    {
        if (strlen($cep) != 8) {
            return $cep;
        }

        $retorno = preg_replace("/(\d{5})(\d{3})/", "\$1-\$2", $cep);
        return $retorno;
    }

    public static function pmo($siglaCliente, $codigo, $ano, $revisao = '')
    {
        $sigla = trim($siglaCliente);
        $pmo = "C-$sigla-" . self::preencheZeros($codigo, 3) . "/$ano";
        if ($revisao != '') {
            $pmo .= "  Rev." . str_pad($revisao, 2, '0', STR_PAD_LEFT);
        }

        return $pmo;
    }

    public static function preencheZeros($nNumero, $nDigitos)
    {
        if ($nDigitos - strlen($nNumero) < 0) return $nNumero;
        $nNovoNumero = str_repeat("0", $nDigitos - strlen($nNumero));
        return $nNovoNumero . $nNumero;
    }

    public static function ajustarValor($valor = '')
    {
        if ($valor == '') {
            return $valor;
        }

        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);

        return $valor;
    }
}
