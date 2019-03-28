<?php
/**
 * Created by PhpStorm.
 * User: shadi
 * Date: 3/22/17
 * Time: 9:50 PM
 */

namespace App\Lightair\Transformers;


abstract class Transformer
{
    public function transformCollection(array $items)
    {
        return array_map([$this, 'transform'], $items);
    }

    public abstract  function transform($item);
} 