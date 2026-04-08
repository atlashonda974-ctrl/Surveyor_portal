<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\FileTab;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str; 
class AdminController extends Controller
{
    /**
     * Show the admin files view
     */
    public function showFiles()
    {
        return view('admin');
    }


     public function getSurvEmail($survCode=null)
    {
        $email = User::select('email')->where('role', 'Surveyor')->get();
        return $email;
    }

    
/**
 * Get all files data 
 */
public function getFiles(Request $request)
{
    try {
        $zone = Session::get('user')['zone'];  
        $brCodeString = Session::get('user')['br_code'] ?? null;
        
        // Log::info('Session User Info:', [
        //     'zone' => $zone,
        //     'br_code' => $brCodeString,
        //     'full_user' => Session::get('user')
        // ]);
        
    
        $branchCodes = [];
        $branchFilterEnabled = false;
        
        if ($brCodeString && trim($brCodeString) !== '') {
            $branchCodes = array_map('trim', explode(',', $brCodeString));
            $branchCodes = array_filter($branchCodes, function($code) {
                return !empty($code);
            });
            
            if (!empty($branchCodes)) {
                $branchFilterEnabled = true;
                $branchCodes = array_map('strtoupper', $branchCodes);
                // Log::info('Branch filtering ENABLED. Codes:', $branchCodes);
            } else {
                // Log::info('Branch filtering DISABLED - empty branch codes after processing');
            }
        } else {
            // Log::info('Branch filtering DISABLED - no branch code in session');
        }
        
        
        $apiUrl = "http://116.58.66.124/dashboardApi/clm/getSurvData.php?surv=All&zone={$zone}";
        
      
        $response = Http::timeout(1500)->get($apiUrl);
        
        if (!$response->successful()) {
            // Log::error('API call failed with status: ' . $response->status());
            return response()->json(['success' => false, 'error' => 'Failed to fetch data from API'], 500);
        }

        $apiResponse = $response->json();
        
        $deptCodeMap = [
            '11' => 'Fire',
            '12' => 'Marine',
            '13' => 'Motor',
            '14' => 'Miscellaneous',
        ];

        $allApiData = [];

        if (is_array($apiResponse)) {
            $allItems = array_merge(
                $apiResponse['pending'] ?? [],
                $apiResponse['done'] ?? [],
                $apiResponse['all'] ?? []
            );
            
            // Log::info('Total items from API: ' . count($allItems));
            
            $skippedByBranch = 0;
            $matchedByBranch = 0;
            $noBranchCodeInData = 0;
            
            foreach ($allItems as $item) {
                if (!is_array($item) || !isset($item['GIH_DOC_REF_NO'])) {
                    continue;
                }
                
                $docNo = $item['GIH_DOC_REF_NO'];
                $apiBranchCode = isset($item['PLC_LOC_CODE']) ? strtoupper(trim($item['PLC_LOC_CODE'])) : null;
                
                if ($branchFilterEnabled) {
                    if ($apiBranchCode) {
                        $branchMatch = in_array($apiBranchCode, $branchCodes);
                        
                        if (!$branchMatch) {
                            $skippedByBranch++;
                            continue;
                        }
                        $matchedByBranch++;
                    } else {
                        $noBranchCodeInData++;
                    }
                }
                
                $allApiData[$docNo] = $item;
            }
            
            // Log::info("Branch filtering results:");
            // Log::info("  - Branch filtering enabled: " . ($branchFilterEnabled ? 'YES' : 'NO'));
            // Log::info("  - Total processed: " . count($allItems));
            // Log::info("  - Matched by branch: " . $matchedByBranch);
            // Log::info("  - No branch code in data: " . $noBranchCodeInData);
            // Log::info("  - Skipped by branch: " . $skippedByBranch);
            // Log::info("  - Final records: " . count($allApiData));
        }

        // =========== GET LAST ACTIVITY FROM BOTH TABLES ===========
       
        $fileActivities = DB::table('filestab')
            ->select('doc_no', DB::raw('MAX(updated_at) as last_file_activity'))
            ->whereNotNull('updated_at')
            ->where('updated_at', '!=', '0000-00-00 00:00:00')
            ->groupBy('doc_no')
            ->get()
            ->keyBy('doc_no');
        
       
      
        try {
            $mailActivities = DB::table('email_logs')
                ->select('uw_doc', DB::raw('MAX(updated_at) as last_mail_activity'))
                ->whereNotNull('updated_at')
                ->where('updated_at', '!=', '0000-00-00 00:00:00')
                ->groupBy('uw_doc')
                ->get()
                ->keyBy('uw_doc');
            // Log::info('Using email_logs table. Found ' . $mailActivities->count() . ' records');
        } catch (\Exception $e) {
            // Log::warning('email_logs table not found, trying mail_logs: ' . $e->getMessage());
            
            
            try {
                $mailActivities = DB::table('mail_logs')
                    ->select('uw_doc', DB::raw('MAX(updated_at) as last_mail_activity'))
                    ->whereNotNull('updated_at')
                    ->where('updated_at', '!=', '0000-00-00 00:00:00')
                    ->groupBy('uw_doc')
                    ->get()
                    ->keyBy('uw_doc');
                // Log::info('Using mail_logs table. Found ' . $mailActivities->count() . ' records');
            } catch (\Exception $e2) {
                // Log::warning('mail_logs table also not found: ' . $e2->getMessage());
                // If no email logs table exists, create empty collection
                $mailActivities = collect([]);
                // Log::info('No email logs table found, using empty collection');
            }
        }
        // ========================================================================
        
        // Get database files
        $dbFiles = FileTab::select('doc_no', 'plr_final', 'rep_tag', 'estimate_amount', 'created_at', 'updated_at')
                         ->orderBy('created_at', 'desc')
                         ->get()
                         ->groupBy('doc_no');

        // Merge API + DB data
        $merged = [];
        
        foreach ($allApiData as $docNo => $apiData) {
            $files = $dbFiles[$docNo] ?? collect([]);
            
            // Determine current status
            $currentStatus = null;
            $currentRepTag = null;
            $estimateAmount = null;

            if ($files->isNotEmpty()) {
                $hasFinalReport = $files->where('rep_tag', 'F/R')->isNotEmpty();
                $hasPrelimReport = $files->where('rep_tag', 'P/R')->isNotEmpty();
                
                if ($hasFinalReport) {
                    $latestFinal = $files->where('rep_tag', 'F/R')->sortByDesc('created_at')->first();
                    $currentStatus = $latestFinal->plr_final;
                    $currentRepTag = 'F/R';
                    $estimateAmount = $latestFinal->estimate_amount;
                } 
                else if ($hasPrelimReport) {
                    $latestPrelim = $files->where('rep_tag', 'P/R')->sortByDesc('created_at')->first();
                    $currentStatus = $latestPrelim->plr_final;
                    $currentRepTag = 'P/R';
                    $estimateAmount = $latestPrelim->estimate_amount;
                }
                else {
                    $latestFile = $files->sortByDesc('created_at')->first();
                    
                    if ($latestFile->plr_final === 'Y') {
                        $currentStatus = 'Y';
                        $currentRepTag = 'P/R';
                        $estimateAmount = $latestFile->estimate_amount;
                    } 
                    else if ($latestFile->plr_final === 'N') {
                        $currentStatus = 'N';
                        $currentRepTag = 'P/R';
                        $estimateAmount = $latestFile->estimate_amount;
                    }
                    else if (in_array($latestFile->plr_final, ['I', 'R', 'V'])) {
                        $currentStatus = $latestFile->plr_final;
                        $currentRepTag = null;
                        $estimateAmount = $latestFile->estimate_amount;
                    }
                }
                
                if ($hasPrelimReport && !$hasFinalReport) {
                    $approvedPrelim = $files->where('rep_tag', 'P/R')->where('plr_final', 'Y')->first();
                    if ($approvedPrelim) {
                        $currentStatus = 'Y';
                        $currentRepTag = 'P/R';
                    }
                }
            }

          
            $lastActivity = null;
            
           
            $fileTime = isset($fileActivities[$docNo]) 
                ? $fileActivities[$docNo]->last_file_activity 
                : null;
            
           
            $mailTime = isset($mailActivities[$docNo]) 
                ? $mailActivities[$docNo]->last_mail_activity 
                : null;
            
            
            if ($fileTime && $mailTime) {
                $fileTimestamp = strtotime($fileTime);
                $mailTimestamp = strtotime($mailTime);
                
                if ($fileTimestamp > $mailTimestamp) {
                    $lastActivity = $fileTime;
                } else {
                    $lastActivity = $mailTime;
                }
            } elseif ($fileTime) {
                $lastActivity = $fileTime;
            } elseif ($mailTime) {
                $lastActivity = $mailTime;
            }
            
           
            if (!$lastActivity && $files->isNotEmpty()) {
                $latestFile = $files->sortByDesc('created_at')->first();
                if ($latestFile && $latestFile->created_at) {
                    $lastActivity = $latestFile->created_at;
                }
            }
            // =================================================

            $mergedData = [
                'doc_no'           => $docNo,
                'client_name'      => isset($apiData['PPS_DESC']) ? $apiData['PPS_DESC'] : 'NA',
                'mobile_no'        => isset($apiData['PPS_MOBILE_NO']) ? $apiData['PPS_MOBILE_NO'] : 'NA',
                'email_address'    => isset($apiData['PPS_EMAIL_ADDRESS']) ? $apiData['PPS_EMAIL_ADDRESS'] : 'NA',
                'intimation_date'  => isset($apiData['GIH_INTIMATIONDATE']) ? $apiData['GIH_INTIMATIONDATE'] : null,
                'appointment_date' => isset($apiData['GUD_APPOINTMENTDATE']) ? $apiData['GUD_APPOINTMENTDATE'] : null,
                'city'             => isset($apiData['PCO_DESC']) ? $apiData['PCO_DESC'] : 'NA',
                'policy_number'    => isset($apiData['GID_BASEDOCUMENTNO']) ? $apiData['GID_BASEDOCUMENTNO'] : 'NA',
                'issue_date'       => isset($apiData['GDH_ISSUEDATE']) ? $apiData['GDH_ISSUEDATE'] : null,
                'expiry_date'      => isset($apiData['GDH_EXPIRYDATE']) ? $apiData['GDH_EXPIRYDATE'] : null,
                'sum_insured'      => isset($apiData['GDH_TOTALSI']) ? $apiData['GDH_TOTALSI'] : 'NA',
                'surveyor_name'    => isset($apiData['PSR_SURV_NAME']) ? $apiData['PSR_SURV_NAME'] : 'NA',
                'surveyor_code'    => isset($apiData['PSR_SURV_CODE']) ? $apiData['PSR_SURV_CODE'] : 'NA',
                'department'       => isset($apiData['PDP_DEPT_CODE']) && isset($deptCodeMap[$apiData['PDP_DEPT_CODE']])
                    ? $deptCodeMap[$apiData['PDP_DEPT_CODE']]
                    : 'Unknown',
                'loss_description' => isset($apiData['POC_LOSSDESC']) ? $apiData['POC_LOSSDESC'] : 'NA',
                'loss_claimed'     => isset($apiData['GIH_LOSSCLAIMED']) ? $apiData['GIH_LOSSCLAIMED'] : 'NA',
                'branch_code'      => isset($apiData['PLC_LOC_CODE']) ? $apiData['PLC_LOC_CODE'] : 'NOT_FOUND',
              
                'plr_final'        => $currentStatus,
                'rep_tag'          => $currentRepTag,
                'estimate_amount'  => $estimateAmount,
             
                'last_activity'    => $lastActivity,
            ];
            
            // Format dates
            foreach (['intimation_date', 'appointment_date', 'issue_date', 'expiry_date'] as $field) {
                $value = isset($mergedData[$field]) ? trim($mergedData[$field]) : '';
                if ($value === '' || strtoupper($value) === 'NA' || $value === null) {
                    $mergedData[$field] = null;
                } else {
                    try {
                        $mergedData[$field] = \Carbon\Carbon::parse($value)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $mergedData[$field] = null;
                    }
                }
            }

          
            $mergedData['days_passed'] = !empty($mergedData['appointment_date'])
                ? \Carbon\Carbon::parse($mergedData['appointment_date'])->diffInDays(now())
                : null;

            $merged[] = $mergedData;
        }
        
        // Log::info('Final merged records: ' . count($merged));
        
        $survAllEmails = User::select('email', 'code')->get();
        
   
        return response()->json([
            'success' => true,
            'data' => $merged,
            'emails' => $survAllEmails,
            'total' => count($merged),
            'debug_info' => [
                'branch_filter_enabled' => $branchFilterEnabled,
                'user_branch_codes' => $branchCodes,
                'filtered_records' => count($merged),
                'user_zone' => $zone,
                'user_branch_string' => $brCodeString,
                'showing_all_data' => !$branchFilterEnabled,
                'last_activity_stats' => [
                    'filestab_records' => $fileActivities->count(),
                    'email_logs_records' => $mailActivities->count()
                ]
            ]
        ]);

    } catch (\Exception $e) {
        // Log::error("Failed to fetch files: " . $e->getMessage());
        // Log::error("Stack trace: " . $e->getTraceAsString());
        return response()->json([
            'success' => false, 
            'error' => 'Unable to load data: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Unapprove PLR
     */
    // public function unapprovePlr(Request $request)
    // {
    //     try {
    //         $docNo = $request->input('doc_no');
            
    //         if (!$docNo) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Document number is required'
    //             ], 400);
    //         }

    //         $file = FileTab::where('doc_no', $docNo)->orderByDesc('id')->first();
            
    //         if (!$file) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'File not found'
    //             ], 404);
    //         }
            
    //         // Update the plr_final to null to mark as unapproved
    //         $file->plr_final = null;
    //         $file->save();

    //         Log::info("PLR unapproved for doc_no: {$docNo}, File ID: {$file->id}");

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'PLR has been unapproved successfully',
    //             'doc_no' => $docNo
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error("Error unapproving PLR: " . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error unapproving PLR: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

//     public function unapprovePlr(Request $request)
// {
//     try {
//         $docNo = $request->input('doc_no');
        
//         if (!$docNo) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Document number is required'
//             ], 400);
//         }

//         // Get the LATEST record
//         $latestFile = FileTab::where('doc_no', $docNo)
//                            ->orderByDesc('created_at')
//                            ->first();
        
//         if (!$latestFile) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'File not found'
//             ], 404);
//         }
        
//         // Check report type
//         $reportType = $latestFile->rep_tag;
//         $reportTypeName = $reportType === 'F/R' ? 'Final Report' : 'Preliminary Report';
        
//         // Only unapprove if it's currently approved
//         if ($latestFile->plr_final === 'Y') {
           
//             $updatedCount = FileTab::where('doc_no', $docNo)
//                 ->where('rep_tag', $reportType)
//                 ->update([
//                     'plr_final' => 'N',
//                     'updated_at' => now()
//                 ]);

//             // Log::info("Report unapproved for doc_no: {$docNo}, Report Type: {$reportType}, Updated: {$updatedCount} records");

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Report has been unapproved successfully',
//                 'doc_no' => $docNo,
//                 'report_type' => $reportTypeName,
//                 'records_updated' => $updatedCount
//             ]);
//         } else {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Report is already unapproved'
//             ]);
//         }
//     } catch (\Exception $e) {
//         Log::error("Error unapproving PLR: " . $e->getMessage());
//         return response()->json([
//             'success' => false,
//             'message' => 'Error unapproving PLR: ' . $e->getMessage()
//         ], 500);
//     }
// }
public function unapprovePlr(Request $request)
{
    try {
        $docNo = $request->input('doc_no');
        
        if (!$docNo) {
            return response()->json([
                'success' => false,
                'message' => 'Document number is required'
            ], 400);
        }

     
        $latestFile = FileTab::where('doc_no', $docNo)
                           ->orderByDesc('created_at')
                           ->first();
        
        if (!$latestFile) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }
        
       
        $reportType = $latestFile->rep_tag;
        $reportTypeName = $reportType === 'F/R' ? 'Final Report' : 'Preliminary Report';
        
       
        if ($latestFile->plr_final === 'Y') {
           
            $updatedCount = FileTab::where('doc_no', $docNo)
                ->where('rep_tag', $reportType)
                ->update([
                    'plr_final' => 'N',
                    'updated_by' => session('user')['name'] ?? 'System',
                    'updated_at' => now()
                ]);

            // Log::info("Report unapproved for doc_no: {$docNo}, Report Type: {$reportType}, Updated: {$updatedCount} records");

            return response()->json([
                'success' => true,
                'message' => 'Report has been unapproved successfully',
                'doc_no' => $docNo,
                'report_type' => $reportTypeName,
                'records_updated' => $updatedCount
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Report is already unapproved'
            ]);
        }
    } catch (\Exception $e) {
        Log::error("Error unapproving PLR: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error unapproving PLR: ' . $e->getMessage()
        ], 500);
    }
}
/**
 * Mark document as In Process (when admin sends reminder/revision)
 */
public function markAsInProcess(Request $request)
{
    try {
        $docNo = $request->input('doc_no');
        $action = $request->input('action', 'I'); 
        
        if (!$docNo) {
            return response()->json([
                'success' => false,
                'message' => 'Document number is required'
            ], 400);
        }

    
        $latestFile = FileTab::where('doc_no', $docNo)->orderByDesc('id')->first();
        
        if (!$latestFile) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }
        
     
        $reportType = $latestFile->rep_tag; 
        
     
        $updatedCount = FileTab::where('doc_no', $docNo)
            ->where('rep_tag', $reportType)
            ->update([
                'plr_final' => $action,
                'updated_at' => now()
            ]);

        // Log::info("Document marked as {$action} for doc_no: {$docNo}, Report Type: {$reportType}, Updated: {$updatedCount} records");

        return response()->json([
            'success' => true,
            'message' => "Document status updated to {$this->getStatusName($action)} for {$reportType}",
            'doc_no' => $docNo,
            'status' => $action,
            'report_type' => $reportType,
            'records_updated' => $updatedCount
        ]);
    } catch (\Exception $e) {
        // Log::error("Error marking as in process: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

private function getStatusName($status)
{
    $statusNames = [
        'Y' => 'Approved',
        'N' => 'Unapproved',
        'I' => 'In Process',
        'R' => 'Reminder Sent',
        'V' => 'Revision Requested'
    ];
    
    return $statusNames[$status] ?? 'Unknown';
}

    /**
     * Add Surveyor Page
     */
    public function addSurveyor()
    {
        $surveyors = DB::table('users')
            ->where('role', 'Surveyor')
            ->orderBy('id', 'desc')
            ->get();
        
        return view('add-surveyor', compact('surveyors'));
    }

    /**
     * Store Surveyor
     */
    // public function storeSurveyor(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'name' => 'required|string|max:255',
    //             'email' => 'required|email|unique:users,email',
    //             'mob_no' => 'required|string|min:10|max:15',
    //             'code' => 'nullable|string|max:50',
    //         ]);

    //         // Generate random password
    //         $plainPassword = $this->generateRandomPassword(12);

    //         // Create surveyor
    //         $surveyor = User::create([
    //             'name' => $request->name,
    //             'email' => $request->email,
    //             'mob_no' => $request->mob_no,
    //             'code' => $request->code,
    //             'password' => Hash::make($plainPassword),
    //             'role' => 'Surveyor'
    //         ]);

    //         // Send welcome email with credentials
    //         $this->sendWelcomeEmail($surveyor, $plainPassword);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Surveyor added successfully! Welcome email sent with credentials.'
    //         ]);

    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation failed',
    //             'errors' => $e->errors()
    //         ], 422);
    //     } catch (\Exception $e) {
    //         Log::error('Error storing surveyor: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to add surveyor: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function storeSurveyor(Request $request)
{
    try {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mob_no' => 'required|string|min:10|max:15',
            'code' => 'nullable|string|max:50',
        ]);

       
        $plainPassword = $this->generateRandomPassword(12);

        $surveyor = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mob_no' => $request->mob_no,
            'code' => $request->code,
            'password' => Hash::make($plainPassword),
            'role' => 'Surveyor'
        ]);

       
        $this->sendWelcomeEmail($surveyor, $plainPassword, false);

        return response()->json([
            'success' => true,
            'message' => 'Surveyor added successfully! Welcome email sent with credentials.'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        // Log::error('Error storing surveyor: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to add surveyor: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Update Surveyor
     */
    public function updateSurveyor(Request $request, $id)
    {
        try {
            $surveyor = DB::table('users')
                ->where('id', $id)
                ->where('role', 'Surveyor')
                ->first();

            if (!$surveyor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Surveyor not found'
                ], 404);
            }

            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'mob_no' => 'required|string|max:15',
                'code' => 'nullable|string|max:255',
            ];

            if ($request->filled('password')) {
                $rules['password'] = 'required|string|min:8|confirmed';
            }

            $validated = $request->validate($rules);

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'mob_no' => $request->mob_no,
                'code' => $request->code,
                'updated_at' => now(),
            ];

            if ($request->filled('password')) {
                $data['password'] = bcrypt($request->password);
            }

            DB::table('users')
                ->where('id', $id)
                ->where('role', 'Surveyor')
                ->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Surveyor updated successfully'
            ]);
        } catch (\Exception $e) {
            // Log::error('Error updating surveyor: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating surveyor'
            ], 500);
        }
    }

    /**
     * Generate random password
     */
    private function generateRandomPassword($length = 12)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%*&';
        $password = '';
        
      
        $password .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[rand(0, 25)]; 
        $password .= 'abcdefghijklmnopqrstuvwxyz'[rand(0, 25)]; 
        $password .= '0123456789'[rand(0, 9)]; 
        $password .= '!@#$%*&'[rand(0, 6)]; 
        

        for ($i = 4; $i < $length; $i++) {
            $password .= $chars[rand(0, strlen($chars) - 1)];
        }
        
   
        return str_shuffle($password);
    }

    /**
     * Send welcome email to new surveyor
     */
    private function sendWelcomeEmail($surveyor, $plainPassword)
    {
        try {
            ini_set("SMTP", "vqs3572.pair.com");
            
            $emailFrom = 'sajjad.kanwal@ail.atlas.pk';
            $emailCC = 'owais.zahid@ail.atlas.pk';
            ini_set("sendmail_from", $emailFrom);

            $headers = "From: AIL - Admin Portal <$emailFrom>\r\n";
            $headers .= "Cc: $emailCC\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
 $userName = session('user.name', 'System Administrator');
            $portalLink = url('/login');

            $message = '<html><body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">';
            $message .= '<div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">';
            $message .= '<h2 style="color: #0062cc; border-bottom: 2px solid #0062cc; padding-bottom: 10px;">Welcome to AIL Surveyor Portal</h2>';
            $message .= '<p>Dear <strong>' . htmlspecialchars($surveyor->name) . '</strong>,</p>';
            $message .= '<p>Your surveyor account has been successfully created. Please find your login credentials below:</p>';
            
            $message .= '<div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;">';
            $message .= '<table style="width: 100%; border-collapse: collapse;">';
            $message .= '<tr><td style="padding: 8px 0; font-weight: bold; width: 150px;">Username:</td><td style="padding: 8px 0;">' . htmlspecialchars($surveyor->email) . '</td></tr>';
            $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Password:</td><td style="padding: 8px 0; font-family: monospace; background: #fff; padding: 8px; border-radius: 4px;">' . htmlspecialchars($plainPassword) . '</td></tr>';
            $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Portal Link:</td><td style="padding: 8px 0;"><a href="' . $portalLink . '" style="color: #0062cc; text-decoration: none;">' . $portalLink . '</a></td></tr>';
            $message .= '</table>';
            $message .= '</div>';
            
            $message .= '<p>If you have any questions or need assistance, please don\'t hesitate to contact the admin team.</p>';
               $message .= '<p style="margin-top: 30px;">Best regards,<br><strong>' . htmlspecialchars($userName) . '</strong></p>';
            $message .= '</div>';
            $message .= '</body></html>';

            $subject = "Welcome to AIL Surveyor Portal - Login Credentials";

            $mailResult = mail($surveyor->email, $subject, $message, $headers);

            if (!$mailResult) {
                // Log::warning('Failed to send welcome email to: ' . $surveyor->email);
            }

            return $mailResult;

        } catch (\Exception $e) {
            // Log::error('Error sending welcome email: ' . $e->getMessage());
            return false;
        }
    }

/**
 * Resend welcome email to surveyor
 */
public function resendWelcomeEmail($id)
{
    try {
       
        $surveyor = User::where('id', $id)
                       ->where('role', 'Surveyor')
                       ->first();
        
        if (!$surveyor) {
            return response()->json([
                'success' => false,
                'message' => 'Surveyor not found'
            ], 404);
        }
        
    
        $tempPassword = $this->generateRandomPassword(12);
        
     
        $surveyor->password = Hash::make($tempPassword);
        $surveyor->save();
        
      
        $emailSent = $this->sendWelcomeEmail($surveyor, $tempPassword, true);
        
        if ($emailSent) {
            // Log the activity
            // Log::info('Welcome email resent to surveyor: ' . $surveyor->email . ' (ID: ' . $surveyor->id . ')');
            
            return response()->json([
                'success' => true,
                'message' => 'Welcome email resent successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Email could not be sent, but password was updated'
            ]);
        }
        
    } catch (\Exception $e) {
        // \Log::error('Error resending welcome email to surveyor ID ' . $id . ': ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while resending the email: ' . $e->getMessage()
        ], 500);
    }
}
  

// public function getApprovalData(Request $request)
// {
//     try {
//         $docNo = trim($request->query('doc_no'));
        
//         if (!$docNo) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Document number is required'
//             ], 400);
//         }
        
//         // Log::info('Fetching approval data for document: ' . $docNo);
        
      
//         $data = DB::table('filestab')
//             ->where('doc_no', $docNo)  // Changed from uw_doc to doc_no
//             ->select('plr_final', 'remarks', 'app_rem')
//             ->first();
        
//         if (!$data) {
//             // Log::info('No data found in filestab for doc_no: ' . $docNo);
//             return response()->json([
//                 'success' => true,
//                 'plr_final' => null,
//                 'remarks' => '',
//                 'app_rem' => ''
//             ]);
//         }
        
//         // Log::info('Data found - app_rem: ' . ($data->app_rem ?: 'empty'));
        
//         return response()->json([
//             'success' => true,
//             'plr_final' => $data->plr_final,
//             'remarks' => $data->remarks,
//             'app_rem' => $data->app_rem ?? ''
//         ]);
        
//     } catch (\Exception $e) {
//         // Log::error('Error getting approval data: ' . $e->getMessage());
//         // Log::error('SQL Error: ' . $e->getMessage());
//         // Log::error('Trace: ' . $e->getTraceAsString());
        
//         return response()->json([
//             'success' => false,
//             'message' => 'Failed to get approval data: ' . $e->getMessage()
//         ], 500);
//     }
// }
public function getApprovalData(Request $request)
{
    try {
        $docNo = trim($request->query('doc_no'));
        
        if (!$docNo) {
            return response()->json([
                'success' => false,
                'message' => 'Document number is required'
            ], 400);
        }

        $allRecords = DB::table('filestab')
            ->where('doc_no', $docNo)
            ->orderBy('created_at', 'asc')
            ->get();
   
        if ($allRecords->isEmpty()) {
            return response()->json([
                'success' => true,
                'has_uploads' => false,           
                'plr_final' => null,
                'report_type' => null,
                'message' => 'No files uploaded yet - no buttons should show'
            ]);
        }
        
     
        $latestRecord = $allRecords->last();
        $currentReportType = $latestRecord->rep_tag;
        
  
        $hasAdminActionForCurrentType = DB::table('filestab')
            ->where('doc_no', $docNo)
            ->where('rep_tag', $currentReportType)
            ->where(function($query) {
                $query->whereNotNull('updated_by')
                      ->orWhere('plr_final', 'Y')
                      ->orWhere('plr_final', 'I')
                      ->orWhere('plr_final', 'R')
                      ->orWhere('plr_final', 'V');
            })
            ->exists();
        
      
        $hadReminderOrRevisionForCurrentType = DB::table('filestab')
            ->where('doc_no', $docNo)
            ->where('rep_tag', $currentReportType)
            ->whereIn('plr_final', ['R', 'V'])
            ->exists();
        
        
        $hasAdminRemarksForCurrentType = DB::table('filestab')
            ->where('doc_no', $docNo)
            ->where('rep_tag', $currentReportType)
            ->whereNotNull('app_rem')
            ->where('app_rem', '!=', '')
            ->exists();
        
        
        $isInitialState = false;
        
        if ($latestRecord->plr_final === 'N' && 
            !$hasAdminActionForCurrentType && 
            !$hasAdminRemarksForCurrentType) {
            $isInitialState = true;
        }
        
        
        $isPostActionState = false;
        
        if ($latestRecord->plr_final === 'N' && $hadReminderOrRevisionForCurrentType) {
            $isPostActionState = true;
        }
        
       
        $returnStatus = $latestRecord->plr_final;
        $shouldShowButtons = true;  
        
        if ($isInitialState) {
            $returnStatus = null; 
        } elseif ($isPostActionState) {
            $returnStatus = null;  
        }
        
        $latestWithRemark = DB::table('filestab')
    ->where('doc_no', $docNo)
    ->whereNotNull('app_rem')
    ->where('app_rem', '!=', '')
    ->orderByDesc('id')
    ->value('app_rem');

$allRemarks = $latestWithRemark ?? '';
        
        return response()->json([
            'success' => true,
            'has_uploads' => true,              
            'plr_final' => $returnStatus,
            'remarks' => $latestRecord->remarks ?? '',
            'app_rem' => $allRemarks ?: '',
            'is_initial_state' => $isInitialState,
            'is_post_action_state' => $isPostActionState,
            'actual_status' => $latestRecord->plr_final,
            'report_type' => $currentReportType,
            'has_admin_action' => $hasAdminActionForCurrentType,
            'should_show_buttons' => true,
            'message' => $isPostActionState ? 'Post-action state - show both buttons' : 
                        ($isInitialState ? 'Initial state - show both buttons' : 'Normal state')
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error getting approval data: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to get approval data: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Save only remarks (separate from approval status)
     */
   
// public function saveRemarks(Request $request)
// {
//     try {
//         $request->validate([
//             'uw_doc' => 'required|string',  
//             'app_rem' => 'required|string|max:1000'
//         ]);

//         $docNo = trim($request->uw_doc);  // This is doc_no in the database
//         $newRemark = trim($request->app_rem);
        
//         if (empty($newRemark)) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Remark cannot be empty.'
//             ]);
//         }

//         // Check if document exists in filestab table
//         $document = DB::table('filestab')
//             ->where('doc_no', $docNo)  // Use doc_no column
//             ->first();

//         if (!$document) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Document not found in database. Doc No: ' . $docNo
//             ]);
//         }

//         // Add timestamp to the new remark
//         $timestamp = now()->format('Y-m-d H:i:s');
//         $remarkWithTime = "[{$timestamp}] {$newRemark}";
        
//         // Get current remarks from the 'app_rem' column
//         $currentRemarks = $document->app_rem ?? '';
        
//         // Log::info('Saving remarks for doc: ' . $docNo);
//         // Log::info('Current remarks: ' . $currentRemarks);
//         // Log::info('New remark: ' . $remarkWithTime);
        
//         // Add new remark with timestamp
//         if (!empty($currentRemarks) && trim($currentRemarks) !== '') {
//             // Append with newline for better separation
//             $updatedRemarks = $currentRemarks . "\n" . $remarkWithTime;
//         } else {
//             $updatedRemarks = $remarkWithTime;
//         }
        
//         // Update the 'app_rem' column
//         $updated = DB::table('filestab')
//             ->where('doc_no', $docNo)  // Use doc_no column
//             ->update([
//                 'app_rem' => $updatedRemarks,
//                 'updated_at' => now()
//             ]);

//         if ($updated) {
//             // Also update plr_final if it's not set
//             if (!$document->plr_final || $document->plr_final === 'null') {
//                 DB::table('filestab')
//                     ->where('doc_no', $docNo)
//                     ->update(['plr_final' => 'I']); // Set to In Process
//             }
            
//             return response()->json([
//                 'success' => true,
//                 'message' => 'New remark added successfully!',
//                 'data' => [
//                     'doc_no' => $docNo,
//                     'app_rem' => $updatedRemarks,
//                     'timestamp' => $timestamp
//                 ]
//             ]);
//         }

//         return response()->json([
//             'success' => false,
//             'message' => 'Failed to save new remark. No rows were updated.'
//         ]);

//     } catch (\Exception $e) {
//         // Log::error('Error saving remarks: ' . $e->getMessage());
//         // Log::error('Trace: ' . $e->getTraceAsString());
        
//         return response()->json([
//             'success' => false,
//             'message' => 'Error: ' . $e->getMessage()
//         ], 500);
//     }
// }


public function saveRemarks(Request $request)
{
    try {
        $request->validate([
            'uw_doc'  => 'required|string',
            'app_rem' => 'required|string|max:1000'
        ]);

        $docNo    = trim($request->uw_doc);
        $newRemark = trim($request->app_rem);

        if (empty($newRemark)) {
            return response()->json(['success' => false, 'message' => 'Remark cannot be empty.']);
        }

        
        $document = DB::table('filestab')
            ->where('doc_no', $docNo)
            ->orderByDesc('id')
            ->first();

        if (!$document) {
            return response()->json(['success' => false, 'message' => 'Document not found. Doc No: ' . $docNo]);
        }

        $timestamp       = now()->format('Y-m-d H:i:s');
        $remarkWithTime  = "[{$timestamp}] {$newRemark}";
        $currentRemarks  = $document->app_rem ?? '';

        $updatedRemarks = !empty(trim($currentRemarks))
            ? $currentRemarks . "\n" . $remarkWithTime
            : $remarkWithTime;

        
        $updated = DB::table('filestab')
            ->where('id', $document->id)
            ->update([
                'app_rem'    => $updatedRemarks,
                'updated_by' => session('user')['name'] ?? 'admin',
                'updated_at' => now()
            ]);

        if ($updated) {
            
            if (!$document->plr_final || $document->plr_final === 'null') {
                DB::table('filestab')
                    ->where('id', $document->id)
                    ->update(['plr_final' => 'I']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Remark added successfully!',
                'data'    => ['doc_no' => $docNo, 'app_rem' => $updatedRemarks, 'timestamp' => $timestamp]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No rows were updated.']);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
    }
}



}





