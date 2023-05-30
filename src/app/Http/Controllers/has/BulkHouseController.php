<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/17/20
 * Time: 10:45 AM
 */

namespace App\Http\Controllers\has;

use App\Entities\Colony\Colony;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Managers\FlashMessageManager;
use Datatables;
use Illuminate\Support\Facades\DB;
use App\Traits\Security\HasPermission;

/**
 * Class BulkHouseController
 * @package App\Http\Controllers\has
 */
class BulkHouseController extends Controller
{
    use HasPermission;

    private $flashMessageManager;

    public function __construct(FlashMessageManager $flashMessageManager)
    {
        $this->flashMessageManager = $flashMessageManager;
    }

    public function index(Request $request)
    {
        $colonies = Colony::all();

        return view('bulkhouse.index', compact('colonies'));
    }

    public function store(Request $request)
    {
        $params = $this->bulkHouseEntry($request);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if($params['o_status_code'] != 1){
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    private function bulkHouseEntry(Request $request)
    {
        $bulkHouseInformation = $request->post();

        DB::beginTransaction();
        try {
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');
            $params = [
                'p_HOUSE_NAME_START_WITH' => $bulkHouseInformation['house_name_start_with'],
                'p_HOUSE_NUMBER_START_FROM' => (int) $bulkHouseInformation['house_number_start_from'],
                'p_WATER_TAP' => (int) $bulkHouseInformation['water_tap'],
                'p_HOUSE_SIZE' => (int) $bulkHouseInformation['house_size'],
                'p_DOUBLE_GAS_YN' => $bulkHouseInformation['double_gas_yn'],
                'p_COLONY_ID' => (int) $bulkHouseInformation['colony_id'],
                'p_BUILDING_ID' => (int) $bulkHouseInformation['building_id'],
                'p_HOUSE_TYPE_ID' => (int) $bulkHouseInformation['house_type_id'],
                'p_NO_OF_HOUSE' => (int) $bulkHouseInformation['no_of_house'],
                'p_INSERT_BY' => auth()->id(),
                'o_status_code' => &$statusCode,
                'o_status_message' => &$statusMessage
            ];

            DB::executeProcedure('gen_house_list', $params);

            if($params['o_status_code'] != 1) {
                DB::rollBack();
                return $params;
            }

            DB::commit();
            return $params;
        }
        catch (\Exception $e) {
            DB::rollBack();
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
            //return [  "exception" => true, "class" => 'error', "message" => $e->getMessage()];
        }

    }
}
