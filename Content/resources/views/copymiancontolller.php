<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\FileTab; 

class MainController extends Controller
{
    public function main()
{
    $claims = [];
    $user = Session::get('user');

    if (!$user || !isset($user['code'])) {
        $claims = ['error' => 'User session expired. Please log in again.'];
        return view('main', compact('claims'));
    }

    $code = $user['code'];

    try {
        // Fetch API with long timeout
        $response = Http::timeout(1500)
            ->get("http://172.16.22.204/dashboardApi/clm/getSurvData.php?surv={$code}");

        if ($response->successful()) {
            $apiResponse = $response->json();

            if (!empty($apiResponse) && is_array($apiResponse)) {
                $flattenedClaims = [];

                foreach ($apiResponse as $status => $items) {
                    if (is_array($items)) {
                        foreach ($items as $item) {
                            if (is_string($item)) {
                                $item = json_decode($item, true);
                            }

                            if (is_array($item)) {
                                $deptCodeMap = [
                                    '11' => 'Fire',
                                    '12' => 'Marine',
                                    '13' => 'Motor',
                                    '14' => 'Miscellaneous',
                                ];

                                $claim = [
                                    'status'           => strtolower($status),
                                    'party_code'       => $item['PPS_PARTY_CODE'] ?? null,
                                    'client_name'      => $item['PPS_DESC'] ?? null,
                                    'mobile_no'        => $item['PPS_MOBILE_NO'] ?? null,
                                    'email_address'    => $item['PPS_EMAIL_ADDRESS'] ?? null,
                                    'intimation_date'  => $item['GIH_INTIMATIONDATE'] ?? null,
                                    'entry_no'         => $item['GIH_INTI_ENTRYNO'] ?? null,
                                    'document_no'      => $item['GIH_DOC_REF_NO'] ?? null,
                                    'policy_number'    => $item['GID_BASEDOCUMENTNO'] ?? null,
                                    'loss_claimed'     => $item['GIH_LOSSCLAIMED'] ?? null,
                                    'posting_tag'      => $item['GIH_POSTINGTAG'] ?? null,
                                    'surveyor_code'    => $item['PSR_SURV_CODE'] ?? null,
                                    'surveyor_name'    => $item['PSR_SURV_NAME'] ?? null,
                                    'report_date'      => $item['GUD_REPORT_DATE'] ?? null,
                                    'appointment_date' => $item['GUD_APPOINTMENTDATE'] ?? null,
                                    'city'             => $item['PCO_DESC'] ?? null,
                                    'issue_date'       => $item['GDH_ISSUEDATE'] ?? null,
                                    'expiry_date'      => $item['GDH_EXPIRYDATE'] ?? null,
                                    'sum_insured'      => $item['GDH_TOTALSI'] ?? null,
                                    'department'       => isset($item['PDP_DEPT_CODE'])
                                        ? ($deptCodeMap[$item['PDP_DEPT_CODE']] ?? 'Unknown')
                                        : 'Unknown',
                                    'loss_description' => $item['POC_LOSSDESC'] ?? null,
                                ];

                                // Add PLR Status
                                if (!empty($claim['document_no'])) {
                                    $file = FileTab::where('doc_no', $claim['document_no'])
                                        ->orderByDesc('id')
                                        ->first();
                                    $claim['plr_status'] = ($file && $file->plr_final === 'Y')
                                        ? 'Approved'
                                        : 'Pending';
                                } else {
                                    $claim['plr_status'] = 'N/A';
                                }

                                $flattenedClaims[] = $claim;
                            }
                        }
                    }
                }

                $claims = !empty($flattenedClaims)
                    ? $flattenedClaims
                    : ['error' => 'No records found for your account.'];
            } else {
                $claims = ['error' => 'No records found for your account.'];
            }
        } else {
            // API returned non-200 status
            \Log::error('API error', [
                'status' => $response->status(),
                'surv_code' => $code
            ]);
            $claims = ['error' => 'The server returned an unexpected error. Please try again later.'];
        }

    } catch (\Illuminate\Http\Client\ConnectionException $e) {
        // Timeout or network issue
        \Log::error('Connection failed', [
            'surv_code' => $code,
            'message' => $e->getMessage()
        ]);
        $claims = ['error' => 'Connection timed out. Please try again later.'];

    } catch (\Illuminate\Http\Client\RequestException $e) {
        // Any other HTTP client error
        \Log::error('HTTP request failed', [
            'surv_code' => $code,
            'message' => $e->getMessage()
        ]);
        $claims = ['error' => 'Unable to fetch data at the moment. Please try again later.'];

    } catch (\Exception $e) {
        // Fallback
        \Log::error('Unexpected error', [
            'surv_code' => $code,
            'message' => $e->getMessage()
        ]);
        $claims = ['error' => 'An unexpected error occurred. Please contact support if it persists.'];
    }

    return view('main', compact('claims'));
}


}
 

