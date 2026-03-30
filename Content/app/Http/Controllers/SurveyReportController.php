<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class SurveyReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Session::get('user');
        $code = $user['code'] ?? '2950003';

        $datefrom = $request->input('datefrom', '01-Jan-2025');
        $dateto   = $request->input('dateto', now()->format('d-M-Y'));

        $url = "http://172.16.22.204/dashboardApi/clm/getSurvFeeReportDtl.php?datefrom={$datefrom}&dateto={$dateto}&surv={$code}";
        $response = Http::get($url);

        $data = $response->json();


        // Debug: dump the API response and stop execution
        // dd($data);

        return view('surveyReport', compact('data', 'code', 'datefrom', 'dateto'));
    }
}
