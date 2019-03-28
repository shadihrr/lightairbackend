<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Lightair\Transformers\reportTransformer;
use App\Report;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReportController extends ApiController
{
    protected $reportTransformer;


    function __construct(reportTransformer $reportTransformer)
    {
        $this->reportTransformer = $reportTransformer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reports = DB::table('reports')
            ->paginate(10);
        return $this->respondWithPagination($reports, $this->reportTransformer->transformCollection($reports->all()));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // TODO add validator
        $report = new Report();
        $report->desc = $request->desc;
        $report->charity = $request->charity;
        $report->skipped = $request->skipped;
        $report->save();
        return $this->respond(['data' => ['status' => 'ok', 'report' => $this->reportTransformer->transform($report)]]);

    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $report = Report::find($id);
        if (!$report) {
            return $this->respondNotFound("No Report!");
        }

        return $this->respond(["data" => ['report' => $this->reportTransformer->transform($report)]]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // TODO add validator
        $report = Report::find($id);
        if (!$report) {
            return $this->respondNotFound("No Report!");
        }
        $report->desc = $request->desc;
        $report->charity = $request->charity;
        $report->skipped = $request->skipped;
        $report->save();
        return $this->respond(['data' => ['status' => 'ok', 'report' => $this->reportTransformer->transform($report)]]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $report = Report::find($id);
        if (!$report) {
            return $this->respondNotFound("No Report");
        }

        $report->delete();
        return $this->respond(["data" => ['status' => 'ok', 'message' => 'removed']]);
    }

}
