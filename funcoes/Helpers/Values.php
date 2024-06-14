<?php

namespace Funcoes\Helpers;

class Values
{
    public static function simNao(string $valor = '', bool $upper = false): string
    {
        if ($valor == '') {
            return '';
        }

        $retorno = ($valor == 'S') ? 'Sim' : 'Não';

        return ($upper) ? mb_strtoupper($retorno) : $retorno;
    }

    /**
     * Verificar se uma variável é igual ao valor. Se for, retornar o que for passado em $retorno,
     * Caso contrário, retorna ela mesma
     */
    public static function seEntao(mixed $var, mixed $valor, mixed $retorno)
    {
        return ($var == $valor) ? $retorno : $var;
    }

    /**
     * Checar se em um array possui um valor passado. Retorna true caso um dos valores 
     * no array corresponder ao que foi passado.
     */
    public static function valorEspecifico(array $array, string|int $valor): bool
    {
        if (empty($array)) {
            return false;
        }

        $retorno = false;

        foreach ($array as $arr) {
            if ($arr == $valor) {
                $retorno = true;
            }
        }

        return $retorno;
    }
}
