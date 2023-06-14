<?php

namespace Funcoes\Lib\Validacoes;

class Validacao
{
    public static function CNPJ(string $cnpj = ''): bool
    {
        if (empty($cnpj)) {
            return false;
        }

        $cnpj = preg_replace("/[^0-9]/", "", $cnpj);
        $cnpj = str_pad($cnpj, 14, '0', STR_PAD_LEFT);

        if (strlen($cnpj) != 14) {
            return false;
        }

        if (preg_match('/^(.)\1*$/', $cnpj)) {
            return false;
        }

        $j = 5;
        $k = 6;
        $soma1 = 0;
        $soma2 = 0;

        for ($i = 0; $i < 13; $i++) {

            $j = $j == 1 ? 9 : $j;
            $k = $k == 1 ? 9 : $k;

            $soma2 += ($cnpj[$i] * $k);

            if ($i < 12) {
                $soma1 += ($cnpj[$i] * $j);
            }

            $k--;
            $j--;
        }

        $digito1 = $soma1 % 11 < 2 ? 0 : 11 - $soma1 % 11;
        $digito2 = $soma2 % 11 < 2 ? 0 : 11 - $soma2 % 11;

        return (($cnpj[12] == $digito1) and ($cnpj[13] == $digito2));
    }

    public static function CPF(string $cpf): bool
    {
        if (empty($cpf)) {
            return false;
        }

        $cpf = preg_replace("/[^0-9]/", "", $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        if (strlen($cpf) != 11) {
            return false;
        }

        if (preg_match('/^(.)\1*$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    public static function identificarDocumento(string $documento = ''): string
    {
        if (self::CNPJ($documento)) {
            return 'CNPJ';
        } elseif (self::CPF($documento)) {
            return 'CPF';
        } else {
            return '';
        }
    }
}
