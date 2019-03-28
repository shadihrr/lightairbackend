<?php
/**
 * Created by PhpStorm.
 * User: safam
 * Date: 7/21/2017
 * Time: 12:39 PM
 */

namespace App\Lightair\Transformers;

use Illuminate\Support\Facades\URL;

class reportTransformer extends Transformer
{
    public function transform($report)
    {

        if ($report instanceof \stdClass){
            return [
                'id' => $report->id,
                'name' => $report->desc,
                'charity' => $report->charity,
                'skipped' => (boolean)$report->skipped,
                'date' => $report->created_at
            ];
        }
        return [
            'id' => $report['id'],
            'name' => $report['desc'],
            'charity' => $report['charity'],
            'skipped' => (boolean)$report['skipped'],
            'date' => $report['created_at']

        ];
    }
}