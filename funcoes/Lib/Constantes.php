<?php

namespace Funcoes\Lib;

class Constantes
{
    private static array $uf = [
        'AC' => 'Acre',
        'AL' => 'Alagoas',
        'AP' => 'Amapá',
        'AM' => 'Amazonas',
        'BA' => 'Bahia',
        'CE' => 'Ceara',
        'DF' => 'Distrito Federal',
        'ES' => 'Espírito Santo',
        'GO' => 'Goiás',
        'MA' => 'Maranhão',
        'MT' => 'Mato Grosso',
        'MS' => 'Mato Grosso do Sul',
        'MG' => 'Minas Gerais',
        'PA' => 'Pará',
        'PB' => 'Paraíba',
        'PR' => 'Paraná',
        'PE' => 'Pernambuco',
        'PI' => 'Piauí',
        'RJ' => 'Rio de Janeiro',
        'RN' => 'Rio Grande do Norte',
        'RS' => 'Rio Grande do Sul',
        'RO' => 'Rondônia',
        'RR' => 'Roraima',
        'SC' => 'Santa Catarina',
        'SP' => 'São Paulo',
        'SE' => 'Sergipe',
        'TO' => 'Tocantins',
        'EX' => 'Exterior'
    ];

    public static function obterEstado(string $uf = '', bool $siglas = false): string | array
    {
        if ($uf != '') {
            return self::$uf[$uf];
        }

        if ($siglas) {
            $arraySiglas = array();

            foreach (self::$uf as $sigla => $estado) {
                $arraySiglas[$sigla] = $sigla;
            }

            return $arraySiglas;
        } else {
            return self::$uf;
        }
    }
}
