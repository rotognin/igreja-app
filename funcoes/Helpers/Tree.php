<?php

namespace Funcoes\Helpers;

class Tree
{
    public static function buildTree(array $elements, $IDField, $parentIDField, $parentId = 0)
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element[$parentIDField] == $parentId) {
                $children = self::buildTree($elements, $IDField, $parentIDField, $element[$IDField]);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }
}
