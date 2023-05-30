<?php

namespace App\Http\Controllers\Report;

use App\Services\Report\OraclePublisher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OraclePublisherController extends Controller
{
    /** @var OraclePublisher  */
    private $oraclePublisher;

    private $fileName;

    /**
     * OraclePublisherController constructor.
     * @param OraclePublisher $oraclePublisher
     */
    public function __construct(OraclePublisher $oraclePublisher)
    {
        $this->oraclePublisher = $oraclePublisher;
    }

    /**
     * Render pdf
     *
     * @param Request $request
     * @return mixed
     */
    public function render($title = "report", Request $request) {

        $xdo = $request->get('xdo');
        $type= $request->get('type');
        $this->fileName = $request->get('filename').".".$type;
        $params = $request->all();
        unset($params['type']);
        unset($params['xdo']);
        unset($params['filename']);
        try {
            $reportContent = $this->oraclePublisher
                ->setXdo($xdo)
                ->setType($type)
                ->setParams($params)->generate();

            if ($type == 'pdf')
                return $this->renderPdf($reportContent);

            if ($type == 'xlsx')
                return $this->downloadExcel($reportContent);

            return $reportContent;

            //Todo: do staff for other type of file
            exit;
        }
        catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Render pdf
     *
     * @param $reportContent
     * @return \Illuminate\Http\Response
     */
    private function renderPdf($reportContent) {
        $filename = $this->fileName?:'file'.".pdf";

        return response()->make($reportContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"'
        ]);
    }

    private function downloadExcel($reportContent) {
        $filename = $this->fileName?:'file'.".xlsx";
        return response()->make($reportContent, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"'
        ]);
    }

    private function downloadDoc($reportContent) {
        $filename = $this->fileName?:'file'.".xlsx";
        return response()->make($reportContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"'
        ]);
    }
}
