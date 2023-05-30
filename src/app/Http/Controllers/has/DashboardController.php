<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/11/20
 * Time: 11:14 AM
 */

namespace App\Http\Controllers\has;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('dashboard.index');
    }
}