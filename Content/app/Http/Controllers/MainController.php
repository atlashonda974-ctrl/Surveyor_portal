<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\FileTab;

class MainController extends Controller
{
    public function main(Request $request)
    {
        $user = Session::get('user');

        if (!$user || !isset($user['code'])) {
            return view('main', [
                'claims' => ['error' => 'User session expired. Please log in again.'],
                'stats' => $this->getEmptyStats(),
                'shouldShowPasswordAlert' => false,
                'intg_tag' => null,
                'selectedYear' => '2025' 
            ]);
        }

        $code = $user['code'];
     
        $selectedYear = $request->input('year', '2025'); 
        
        if (!in_array($selectedYear, ['2025', '2026'])) {
            $selectedYear = '2025'; 
        }
        
        $claims = [];
        $stats = $this->getEmptyStats();

        try {
            $response = Http::timeout(1500)
                ->get("http://172.16.22.204/dashboardApi/clm/getSurvData2.php?surv={$code}&year={$selectedYear}");

            if ($response->successful()) {
                $apiResponse = $response->json();

                if (!empty($apiResponse) && is_array($apiResponse)) {
                    $claims = $this->processApiResponse($apiResponse);
                    $stats = $this->calculateStats($claims);
                } else {
                    $claims = ['error' => 'No records found for your account.'];
                }
            } else {
                \Log::error('API error', [
                    'status' => $response->status(),
                    'surv_code' => $code,
                    'year' => $selectedYear
                ]);
                $claims = ['error' => 'The server returned an unexpected error. Please try again later.'];
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Log::error('Connection failed', [
                'surv_code' => $code,
                'year' => $selectedYear,
                'message' => $e->getMessage()
            ]);
            $claims = ['error' => 'Connection timed out. Please try again later.'];
        } catch (\Exception $e) {
            \Log::error('Unexpected error', [
                'surv_code' => $code,
                'year' => $selectedYear,
                'message' => $e->getMessage()
            ]);
            $claims = ['error' => 'An unexpected error occurred. Please contact support if it persists.'];
        }

        return view('main', [
            'claims' => $claims,
            'stats' => $stats,
            'shouldShowPasswordAlert' => $this->hasPassed30Days($user['updated_at'] ?? null),
            'intg_tag' => $user['intg_tag'] ?? null,
            'selectedYear' => $selectedYear
        ]);
    }

//    private function processApiResponse(array $apiResponse): array
// {
//     $flattenedClaims = [];
//     $deptCodeMap = [
//         '11' => 'Fire',
//         '12' => 'Marine',
//         '13' => 'Motor',
//         '14' => 'Miscellaneous',
//     ];

//     foreach ($apiResponse as $status => $items) {
//         if (!is_array($items)) continue;

//         foreach ($items as $item) {
//             if (is_string($item)) {
//                 $item = json_decode($item, true);
//             }

//             if (!is_array($item)) continue;

//             // Convert string numbers to float
//             $sumInsured = $item['GDH_TOTALSI'] ?? null;
//             if ($sumInsured !== null) {
//                 $sumInsured = is_numeric($sumInsured) ? (float)$sumInsured : 0;
//             }

//             $claim = [
//                 'status'           => strtolower($status),
//                 'party_code'       => $item['PPS_PARTY_CODE'] ?? null,
//                 'client_name'      => $item['PPS_DESC'] ?? null,
//                 'mobile_no'        => $item['PPS_MOBILE_NO'] ?? null,
//                 'email_address'    => $item['PPS_EMAIL_ADDRESS'] ?? null,
//                 'intimation_date'  => $item['GIH_INTIMATIONDATE'] ?? null,
//                 'entry_no'         => $item['GIH_INTI_ENTRYNO'] ?? null,
//                 'document_no'      => $item['GIH_DOC_REF_NO'] ?? null,
//                 'policy_number'    => $item['GID_BASEDOCUMENTNO'] ?? null,
//                 'loss_claimed'     => isset($item['GIH_LOSSCLAIMED']) ? 
//                     (is_numeric($item['GIH_LOSSCLAIMED']) ? (float)$item['GIH_LOSSCLAIMED'] : 0) : 0,
//                 'posting_tag'      => $item['GIH_POSTINGTAG'] ?? null,
//                 'surveyor_code'    => $item['PSR_SURV_CODE'] ?? null,
//                 'surveyor_name'    => $item['PSR_SURV_NAME'] ?? null,
//                 'report_date'      => $item['GUD_REPORT_DATE'] ?? null,
//                 'appointment_date' => $item['GUD_APPOINTMENTDATE'] ?? null,
//                 'city'             => $item['PCO_DESC'] ?? null,
//                 'issue_date'       => $item['GDH_ISSUEDATE'] ?? null,
//                 'expiry_date'      => $item['GDH_EXPIRYDATE'] ?? null,
//                 'sum_insured'      => $sumInsured,
//                 'department'       => isset($item['PDP_DEPT_CODE'])
//                     ? ($deptCodeMap[$item['PDP_DEPT_CODE']] ?? 'Unknown')
//                     : 'Unknown',
//                 'loss_description' => $item['POC_LOSSDESC'] ?? null,
//                 'plr_status'       => 'Pending', // Default to Pending instead of N/A
//                 'report_type'      => 'P/R', // Default to P/R
//                 'fees_paid'        => isset($item['fees_paid']) ? 
//                     (is_numeric($item['fees_paid']) ? (float)$item['fees_paid'] : 0) : 0,
//                 'pending_fees'     => isset($item['pending_fees']) ? 
//                     (is_numeric($item['pending_fees']) ? (float)$item['pending_fees'] : 0) : 0,
//                 'SETTLEMENT_DATE' => $item['SETTLEMENT_DATE'] ?? null,
//                 'estimate_amount' => 0,
//             ];

//             if (!empty($claim['document_no'])) {
//                 // Get the latest file for this document
//                 $latestFile = FileTab::where('doc_no', $claim['document_no'])
//                     ->orderByDesc('id')
//                     ->first();
                    
//                 if ($latestFile) {
//                     $claim['estimate_amount'] = isset($latestFile->estimate_amount) ? 
//                         (int)$latestFile->estimate_amount : 0;
                    
//                     // Get ALL files for this document to check history
//                     $allFiles = FileTab::where('doc_no', $claim['document_no'])
//                         ->orderBy('id')
//                         ->get();
                        
//                     $hasApprovedPR = false;
//                     $currentPhase = 'P/R'; // Default to P/R
                    
//                     // Check if ANY file was ever approved
//                     foreach ($allFiles as $file) {
//                         if ($file->plr_final === 'Y') {
//                             $hasApprovedPR = true;
//                             break;
//                         }
//                     }
                    
//                     // Determine report type based on approval history
//                     if ($hasApprovedPR) {
//                         // P/R was approved at some point - now in F/R phase
//                         $currentPhase = 'F/R';
//                     } else {
//                         // Still in P/R phase (no approvals yet)
//                         $currentPhase = 'P/R';
//                     }
                    
//                     $claim['report_type'] = $currentPhase;
                    
//                     // Determine PLR status from latest file
//                     if ($latestFile->plr_final === 'Y') {
//                         $claim['plr_status'] = 'Approved';
//                     } else {
//                         $claim['plr_status'] = 'Pending';
//                     }
//                 } else {
//                     // No files uploaded yet
//                     $claim['plr_status'] = 'Pending';
//                     $claim['report_type'] = 'P/R';
//                 }
//             }

//             $flattenedClaims[] = $claim;
//         }
//     }

//     return $flattenedClaims;
// }


private function processApiResponse(array $apiResponse): array
{
    $flattenedClaims = [];
    $deptCodeMap = [
        '11' => 'Fire',
        '12' => 'Marine',
        '13' => 'Motor',
        '14' => 'Miscellaneous',
    ];

    foreach ($apiResponse as $status => $items) {
        if (!is_array($items)) continue;

        foreach ($items as $item) {
            if (is_string($item)) {
                $item = json_decode($item, true);
            }

            if (!is_array($item)) continue;

            // Convert string numbers to float
            $sumInsured = $item['GDH_TOTALSI'] ?? null;
            if ($sumInsured !== null) {
                $sumInsured = is_numeric($sumInsured) ? (float)$sumInsured : 0;
            }

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
                'loss_claimed'     => isset($item['GIH_LOSSCLAIMED']) ? 
                    (is_numeric($item['GIH_LOSSCLAIMED']) ? (float)$item['GIH_LOSSCLAIMED'] : 0) : 0,
                'posting_tag'      => $item['GIH_POSTINGTAG'] ?? null,
                'surveyor_code'    => $item['PSR_SURV_CODE'] ?? null,
                'surveyor_name'    => $item['PSR_SURV_NAME'] ?? null,
                'report_date'      => $item['GUD_REPORT_DATE'] ?? null,
                'appointment_date' => $item['GUD_APPOINTMENTDATE'] ?? null,
                'city'             => $item['PCO_DESC'] ?? null,
                'issue_date'       => $item['GDH_ISSUEDATE'] ?? null,
                'expiry_date'      => $item['GDH_EXPIRYDATE'] ?? null,
                'sum_insured'      => $sumInsured,
                'department'       => isset($item['PDP_DEPT_CODE'])
                    ? ($deptCodeMap[$item['PDP_DEPT_CODE']] ?? 'Unknown')
                    : 'Unknown',
                'loss_description' => $item['POC_LOSSDESC'] ?? null,
                'plr_status'       => 'Pending',
                'plr_status_detail' => 'Pending PLR (Doc Not Uploaded)',
                'report_type'      => 'P/R',
                'fees_paid'        => isset($item['fees_paid']) ? 
                    (is_numeric($item['fees_paid']) ? (float)$item['fees_paid'] : 0) : 0,
                'pending_fees'     => isset($item['pending_fees']) ? 
                    (is_numeric($item['pending_fees']) ? (float)$item['pending_fees'] : 0) : 0,
                'SETTLEMENT_DATE' => $item['SETTLEMENT_DATE'] ?? null,
                'estimate_amount' => 0,
                'days_since_pr_upload' => 0,
                'is_pr_overdue' => false,
                'pr_upload_date' => null,
            ];

            if (!empty($claim['document_no'])) {
                // Get ALL files for this document
                $allFiles = FileTab::where('doc_no', $claim['document_no'])
                    ->orderBy('id')
                    ->get();
                    
                if ($allFiles->count() > 0) {
                    $latestFile = $allFiles->last();
                    $claim['estimate_amount'] = isset($latestFile->estimate_amount) ? 
                        (int)$latestFile->estimate_amount : 0;
                    
                    // Get the first file (oldest) as P/R upload date
                    $prUploadFile = $allFiles->first();
                    
                    if ($prUploadFile) {
                        // Use datetime_field as upload date, fallback to created_at if not available
                        $claim['pr_upload_date'] = $prUploadFile->datetime_field ?? $prUploadFile->created_at;
                        
                        // Calculate days since P/R upload
                        if ($claim['pr_upload_date']) {
                            try {
                                $uploadDate = new \DateTime($claim['pr_upload_date']);
                                $currentDate = new \DateTime();
                                $interval = $currentDate->diff($uploadDate);
                                $claim['days_since_pr_upload'] = $interval->days;
                                // Highlight after 2 or more days (>= 2)
                                $claim['is_pr_overdue'] = $interval->days >= 2;
                            } catch (\Exception $e) {
                                $claim['days_since_pr_upload'] = 0;
                                $claim['is_pr_overdue'] = false;
                            }
                        }
                    }
                    
                    // Check if ANY file was ever approved
                    $hasApprovedPR = false;
                    $approvedFileIndex = null;
                    
                    foreach ($allFiles as $index => $file) {
                        if ($file->plr_final === 'Y') {
                            $hasApprovedPR = true;
                            $approvedFileIndex = $index;
                            break;
                        }
                    }
                    
                    // Determine report type and status based on approval history
                    if ($hasApprovedPR) {
                        // P/R was approved at some point - now in F/R phase
                        $claim['report_type'] = 'F/R';
                        
                        // Check if there are any files uploaded AFTER the approved P/R
                        $hasFilesAfterApproval = false;
                        foreach ($allFiles as $index => $file) {
                            if ($index > $approvedFileIndex) {
                                $hasFilesAfterApproval = true;
                                break;
                            }
                        }
                        
                        if (!$hasFilesAfterApproval) {
                            // No files after approved P/R = F/R not uploaded yet
                            $claim['plr_status'] = 'Pending';
                            $claim['plr_status_detail'] = 'Pending F/R Upload';
                        } else {
                            // There are files after approved P/R
                            if ($latestFile->plr_final === 'Y') {
                                // Latest file (F/R) is approved
                                $claim['plr_status'] = 'Approved';
                                $claim['plr_status_detail'] = 'Approved';
                            } else {
                                // Latest file (F/R) is not approved yet
                                $claim['plr_status'] = 'Pending';
                                $claim['plr_status_detail'] = 'Pending F/R Approval';
                            }
                        }
                    } else {
                        // Still in P/R phase (no approvals yet)
                        $claim['report_type'] = 'P/R';
                        
                        if ($latestFile->plr_final === 'Y') {
                            $claim['plr_status'] = 'Approved';
                            $claim['plr_status_detail'] = 'Approved';
                        } else {
                            $claim['plr_status'] = 'Pending';
                            
                            // Check if P/R is overdue
                            if ($claim['is_pr_overdue']) {
                                $overdueDays = $claim['days_since_pr_upload'] - 2;
                                $claim['plr_status_detail'] = 'Pending P/R Approval (Overdue ' . $overdueDays . ' day(s))';
                            } else {
                                $claim['plr_status_detail'] = 'Pending P/R Approval';
                            }
                        }
                    }
                } else {
                    // No files uploaded yet
                    $claim['plr_status'] = 'Pending';
                    $claim['plr_status_detail'] = 'Pending PLR (Doc Not Uploaded)';
                    $claim['report_type'] = 'P/R';
                }
            } else {
                // No document number
                $claim['plr_status'] = 'Pending';
                $claim['plr_status_detail'] = 'Pending PLR (No Doc)';
                $claim['report_type'] = 'P/R';
            }

            $flattenedClaims[] = $claim;
        }
    }

    return $flattenedClaims;
}

    private function calculateStats(array $claims): array
    {
        if (isset($claims['error'])) {
            return $this->getEmptyStats();
        }

        $stats = [
            'totalSurveyorAssigned' => count($claims),
            'totalFeesPaid' => 0,
            'totalPendingFees' => 0,
            'totalPendingClaims' => 0,
        ];

        foreach ($claims as $claim) {
            $stats['totalFeesPaid'] += $claim['fees_paid'] ?? 0;
            $stats['totalPendingFees'] += $claim['pending_fees'] ?? 0;
            
            if (($claim['status'] ?? 'pending') === 'pending') {
                $stats['totalPendingClaims']++;
            }
        }

        return $stats;
    }

    private function hasPassed30Days(?string $userDate): bool
    {
        if (is_null($userDate)) {
            return true;
        }

        try {
            $givenDate = new \DateTime($userDate);
            $currentDate = new \DateTime();
            $difference = $currentDate->diff($givenDate);
            
            return $difference->days >= 25 && $difference->invert == 1;
        } catch (\Exception $e) {
            return true;
        }
    }

    private function getEmptyStats(): array
    {
        return [
            'totalSurveyorAssigned' => 0,
            'totalFeesPaid' => 0,
            'totalPendingFees' => 0,
            'totalPendingClaims' => 0,
        ];
    }
}