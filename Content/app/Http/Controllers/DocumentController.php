<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\FileTab;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\EmailLog;
use ZipArchive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Log;
class DocumentController extends Controller
{
  
//    public function showUploadForm($document_no)
// {
//     $today = date('Y-m-d');
    
//     // Get the latest file record to check status
//     $latestFile = \App\Models\FileTab::where('doc_no', $document_no)
//                                    ->orderByDesc('id')
//                                    ->first();

//     // Get all files for this document to determine status
//     $files = \App\Models\FileTab::where('doc_no', $document_no)
//                                ->orderByDesc('id')
//                                ->get();
    
//     // Determine current status
//     $isPlrApproved = false;
//     $plr_final_status = null;
//     $hasReminder = false;
//     $needsRevision = false;
    
//     if ($files->isNotEmpty()) {
//         // Check the latest record's status
//         $latestRecord = $files->first();
//         $plr_final_status = $latestRecord->plr_final;
        
//         // Check if there's an approved PLR
//         $approvedPLR = $files->where('rep_tag', 'P/R')
//                            ->where('plr_final', 'Y')
//                            ->isNotEmpty();
        
//         // Check if there's a final report uploaded
//         $hasFinalReport = $files->where('rep_tag', 'F/R')->isNotEmpty();
        
//         // If there's an approved PLR AND no final report yet, PLR is approved
//         if ($approvedPLR && !$hasFinalReport) {
//             $isPlrApproved = true;
//         }
        
//         // Check for reminder status
//         $hasReminder = $files->where('plr_final', 'R')->isNotEmpty();
        
//         // Check for revision status
//         $needsRevision = $files->where('plr_final', 'V')->isNotEmpty();
        
//         // If latest status is 'R' (reminder) or 'V' (revision), show those
//         if ($plr_final_status === 'R') {
//             $hasReminder = true;
//         } else if ($plr_final_status === 'V') {
//             $needsRevision = true;
//         }
//     }
    
//     // Fetch claim details from your existing API
//     $user = Session::get('user');
//     $insured_name = 'N/A';
//     $policy_name = 'N/A';
//     $loss_cause = 'N/A';
    
//     if ($user && isset($user['code'])) {
//         try {
//             $currentYear = date('Y');
//             $response = Http::timeout(1500)
//                 ->get("http://172.16.22.204/dashboardApi/clm/getSurvData2.php?surv={$user['code']}&year={$currentYear}");
            
//             if ($response->successful()) {
//                 $apiResponse = $response->json();
                
//                 // Search for the specific claim by document_no
//                 foreach ($apiResponse as $status => $items) {
//                     if (!is_array($items)) continue;
                    
//                     foreach ($items as $item) {
//                         if (is_string($item)) {
//                             $item = json_decode($item, true);
//                         }
                        
//                         if (is_array($item) && isset($item['GIH_DOC_REF_NO']) && 
//                             $item['GIH_DOC_REF_NO'] == $document_no) {
                            
//                             // Extract the needed fields
//                             $insured_name = $item['PPS_DESC'] ?? 'N/A';
//                             $policy_name = $item['GID_BASEDOCUMENTNO'] ?? 'N/A';
//                             $loss_cause = $item['POC_LOSSDESC'] ?? 'N/A';
                            
//                             break 2; // Break out of both loops
//                         }
//                     }
//                 }
//             }
//         } catch (\Exception $e) {
//             // Log error but continue with default values
//             \Log::error('Error fetching claim details: ' . $e->getMessage());
//         }
//     }
    
//     // Also check for admin remarks/messages
//     $adminMessages = [];
//     if ($latestFile && !empty($latestFile->app_rem)) {
//         $remarks = explode("\n", $latestFile->app_rem);
//         foreach ($remarks as $remark) {
//             $remark = trim($remark);
//             if (!empty($remark)) {
//                 $adminMessages[] = $remark;
//             }
//         }
//     }
    
//     return view('upload-document', compact(
//         'document_no', 
//         'today', 
//         'isPlrApproved', 
//         'plr_final_status',
//         'hasReminder',
//         'needsRevision',
//         'insured_name', 
//         'policy_name', 
//         'loss_cause',
//         'adminMessages'
//     ));
// }
public function showUploadForm($document_no)
{
    $today = date('Y-m-d');
    
    // Get the latest file record
    $latestFile = \App\Models\FileTab::where('doc_no', $document_no)
                                   ->orderByDesc('id')
                                   ->first();

    // Get all files for this document
    $files = \App\Models\FileTab::where('doc_no', $document_no)
                               ->orderByDesc('id')
                               ->get();
    
    // Determine current status
    $isPlrApproved = false;
    $isFrApproved = false;
    $plr_final_status = null;
    $hasReminder = false;
    $needsRevision = false;
    $requiresAction = false;
    $actionIsForFR = false;
    
    if ($files->isNotEmpty()) {
        $latestRecord = $files->first();
        $plr_final_status = $latestRecord->plr_final;
        
        // Check if P/R is approved using Query Builder
        $isPlrApproved = FileTab::where('doc_no', $document_no)
            ->where('rep_tag', 'P/R')
            ->where('plr_final', 'Y')
            ->exists();
        
        // Check if F/R is approved
        $isFrApproved = FileTab::where('doc_no', $document_no)
            ->where('rep_tag', 'F/R')
            ->where('plr_final', 'Y')
            ->exists();
        
        // Check if there's any F/R uploaded
        $hasFinalReport = FileTab::where('doc_no', $document_no)
            ->where('rep_tag', 'F/R')
            ->exists();
        
        // Check for reminders/revisions
        $hasReminder = $files->where('plr_final', 'R')->isNotEmpty();
        $needsRevision = $files->where('plr_final', 'V')->isNotEmpty();
        
        // Check if action is required
        if (in_array($plr_final_status, ['R', 'V'])) {
            $requiresAction = true;
            
            // Determine if action is for F/R or P/R
            // 1. If P/R is approved, action is likely for F/R
            if ($isPlrApproved) {
                $actionIsForFR = true;
            }
            
            // 2. If current file is F/R, action is for F/R
            if ($latestRecord && $latestRecord->rep_tag === 'F/R') {
                $actionIsForFR = true;
            }
            
            // 3. Check latest non-action F/R
            if ($hasFinalReport) {
                $latestNonActionFile = FileTab::where('doc_no', $document_no)
                    ->whereNotIn('plr_final', ['R', 'V'])
                    ->orderByDesc('id')
                    ->first();
                
                if ($latestNonActionFile && $latestNonActionFile->rep_tag === 'F/R') {
                    $actionIsForFR = true;
                }
            }
        }
    }
    
    // Fetch claim details from API
    $user = Session::get('user');
    $insured_name = 'N/A';
    $policy_name = 'N/A';
    $loss_cause = 'N/A';
    
    if ($user && isset($user['code'])) {
        try {
            $currentYear = date('Y');
            $response = Http::timeout(1500)
                ->get("http://172.16.22.204/dashboardApi/clm/getSurvData2.php?surv={$user['code']}&year={$currentYear}");
            
            if ($response->successful()) {
                $apiResponse = $response->json();
                
                // Search for the specific claim
                foreach ($apiResponse as $status => $items) {
                    if (!is_array($items)) continue;
                    
                    foreach ($items as $item) {
                        if (is_string($item)) {
                            $item = json_decode($item, true);
                        }
                        
                        if (is_array($item) && isset($item['GIH_DOC_REF_NO']) && 
                            $item['GIH_DOC_REF_NO'] == $document_no) {
                            
                            $insured_name = $item['PPS_DESC'] ?? 'N/A';
                            $policy_name = $item['GID_BASEDOCUMENTNO'] ?? 'N/A';
                            $loss_cause = $item['POC_LOSSDESC'] ?? 'N/A';
                            break 2;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching claim details: ' . $e->getMessage());
        }
    }
    
    // Check for admin remarks
    $adminMessages = [];
    if ($latestFile && !empty($latestFile->app_rem)) {
        $remarks = explode("\n", $latestFile->app_rem);
        foreach ($remarks as $remark) {
            $remark = trim($remark);
            if (!empty($remark)) {
                $adminMessages[] = $remark;
            }
        }
    }
    
    return view('upload-document', compact(
        'document_no', 
        'today', 
        'isPlrApproved',
        'isFrApproved',
        'plr_final_status',
        'hasReminder',
        'needsRevision',
        'requiresAction',
        'actionIsForFR',
        'insured_name', 
        'policy_name', 
        'loss_cause',
        'adminMessages'
    ));
}
  
//    public function upload(Request $request, $document_no)
// {
//     // Increase max execution time and memory limit
//     ini_set('max_execution_time', 300); // 5 minutes
//     ini_set('memory_limit', '512M');
    
//     // Check if PLR is already approved for this document
//     $isPlrApproved = FileTab::where('doc_no', $document_no)
//         ->where('rep_tag', 'P/R')
//         ->where('plr_final', 'Y')
//         ->exists();
    
//     // Get report type from form
//     $reportType = $request->report_type;
    
//     // Validate: If PLR is approved and user tries to upload P/R, show error
//     if ($isPlrApproved && $reportType === 'P/R') {
//         return redirect()->back()
//             ->with('error', 'Preliminary Report is already approved. Please upload Final Report (F/R).')
//             ->withInput();
//     }
    
//     // Validate: If PLR is not approved and user tries to upload F/R, show error
//     if (!$isPlrApproved && $reportType === 'F/R') {
//         return redirect()->back()
//             ->with('error', 'Preliminary Report must be approved first before uploading Final Report.')
//             ->withInput();
//     }
    
//     // Validate request
//     $request->validate([
//         'document.*' => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
//         'remarks.*'  => 'nullable|string|max:500',
//         'upload_date.*' => 'required|date',
//         'estimate_amount.*' => 'nullable|numeric|min:0|max:999999999.99',
//         'report_type' => 'required|in:P/R,F/R',
//     ]);

//     $documents = $request->file('document');
//     $remarks   = $request->remarks;
//     $dates     = $request->upload_date;
//     $estimate_amounts = $request->estimate_amount;
//     $created_by = Auth::user()->id ?? 0;

//     $totalEstimate = 0;
//     $documentsWithEstimate = 0;
    
//     // Determine plr_final status
//     $plrFinalStatus = 'N'; // Default for new uploads
    
//     // If this is a final report AND PLR was approved, reset to 'N' for final report review
//     if ($reportType === 'F/R' && $isPlrApproved) {
//         $plrFinalStatus = 'N'; // Reset to Unapproved for admin to review final report
//     }
//     // If this is a preliminary report, set to 'N' by default
//     elseif ($reportType === 'P/R') {
//         $plrFinalStatus = 'N'; // Unapproved for initial review
//     }

//     foreach ($documents as $index => $file) {
//         $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
//         $filePath = storage_path('app/public/uploads/documents/' . $fileName);
        
//         $file->move(storage_path('app/public/uploads/documents/'), $fileName);
        
//         // Process estimate amount
//         $estimateAmount = null;
//         if (isset($estimate_amounts[$index]) && $estimate_amounts[$index] !== '' && is_numeric($estimate_amounts[$index])) {
//             $estimateAmount = floatval($estimate_amounts[$index]);
//             $totalEstimate += $estimateAmount;
//             $documentsWithEstimate++;
//         }
        
//         // Create record in database with the determined plr_final status
//         FileTab::create([
//             'datetime_field'  => $dates[$index],
//             'doc_no'          => $document_no,
//             'remarks'         => $remarks[$index] ?? '',
//             'estimate_amount' => $estimateAmount,
//             'created_by'      => $created_by,
//             'file_path'       => $fileName,
//             'plr_final'       => $plrFinalStatus, // Set status based on logic above
//             'rep_tag'         => $reportType,
//         ]);
//     }
    
//     // Email configuration
//     ini_set("SMTP", "vqs3572.pair.com");
    
//     $emailUsr   = 'owais.zahid@ail.atlas.pk';
//     $emailUsrCC = 'owais.zahid@ail.atlas.pk, owais.zahid@ail.atlas.pk';
//     ini_set("sendmail_from", $emailUsr);
    
//     $boundary = md5(time());
    
//     // === EMAIL HEADERS ===
//     $headers  = "From: AIL - Surveyor <{$emailUsr}>\r\n";
//     $headers .= "Cc: {$emailUsrCC}\r\n";
//     $headers .= "MIME-Version: 1.0\r\n";
//     $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n\r\n";
    
//     // === EMAIL BODY (Styled + Responsive) ===
//     $bodyContent = '
//     <html>
//     <head>
//       <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
//       <style>
//         body {
//           margin: 0;
//           padding: 0;
//           background-color: #f4f6f8;
//           font-family: "Segoe UI", Arial, sans-serif;
//           color: #333;
//         }
//         table {
//           border-collapse: collapse;
//           width: 100%;
//         }
//         .email-wrapper {
//           width: 100%;
//           background-color: #f4f6f8;
//           padding: 20px 0;
//         }
//         .email-container {
//           max-width: 600px;
//           margin: 0 auto;
//           background-color: #ffffff;
//           border-radius: 8px;
//           box-shadow: 0 2px 6px rgba(0,0,0,0.05);
//           overflow: hidden;
//         }
//         .header {
//           background-color: #007bff;
//           color: #ffffff;
//           text-align: center;
//           font-size: 18px;
//           font-weight: 600;
//           padding: 16px 0;
//         }
//         .content {
//           padding: 25px 30px;
//           line-height: 1.6;
//           font-size: 15px;
//         }
//         .doc-info {
//           background-color: #eef3fa;
//           border-left: 4px solid #007bff;
//           padding: 10px 15px;
//           font-size: 14px;
//           margin-bottom: 20px;
//         }
//         .doc-details {
//           background-color: #f8f9fa;
//           padding: 15px;
//           border-radius: 5px;
//           margin-bottom: 20px;
//           font-size: 14px;
//           border: 1px solid #e9ecef;
//         }
//         .doc-details p {
//           margin: 5px 0;
//         }
//         .footer {
//           border-top: 1px solid #eaeaea;
//           color: #666;
//           padding: 20px 30px;
//           font-size: 14px;
//         }
//         .footer strong {
//           color: #333;
//         }
//       </style>
//     </head>
//     <body>
//       <table class="email-wrapper">
//         <tr>
//           <td align="center">
//             <table class="email-container">
//               <tr>
//                 <td class="header">
//                   Atlas Insurance Claims Management
//                 </td>
//               </tr>
//               <tr>
//                 <td class="content">
//                   <div class="doc-info">
//                     <strong>Document No:</strong> ' . htmlspecialchars($document_no) . '
//                   </div>
                  
//                   <div class="doc-details">
//                     <p><strong>Upload Summary:</strong></p>
//                     <p>Report Type: ' . ($reportType === 'P/R' ? 'Preliminary Report (P/R)' : 'Final Report (F/R)') . '</p>
//                     <p>Total Documents Uploaded: ' . count($documents) . '</p>';
    
//     // Add estimate amounts summary if any
//     if ($documentsWithEstimate > 0) {
//         $bodyContent .= '
//                     <p>Documents with Estimate Amount: ' . $documentsWithEstimate . '</p>
//                     <p>Total Estimated Amount: Rs' . number_format($totalEstimate, 2) . '</p>';
//     } else {
//         $bodyContent .= '
//                     <p>No estimate amounts provided.</p>';
//     }
    
//     $bodyContent .= '
//                   </div>
                  
//                   <p>Dear Sir/Ma\'am,</p>
//                   <p>Documents have been successfully uploaded for the above-mentioned document number.</p>
//                   <p>Kindly review the uploaded files at your earliest convenience.</p>
                 
//                 </td>
//               </tr>
//               <tr>
//                 <td class="footer">
//                   <p><strong>Best Regards,</strong><br>
//                   Atlas Insurance Claims Management Team</p>
//                 </td>
//               </tr>
//             </table>
//           </td>
//         </tr>
//       </table>
//     </body>
//     </html>';
    
//     // === BUILD MESSAGE BODY (Simplified - No attachments) ===
//     $message  = "--{$boundary}\r\n";
//     $message .= "Content-Type: text/html; charset=UTF-8\r\n";
//     $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
//     $message .= $bodyContent . "\r\n";
    
//     $message .= "--{$boundary}--";
    
//     // === SUBJECT ===
//     $reportTypeName = $reportType === 'P/R' ? 'Preliminary Report' : 'Final Report';
//     $subject = "{$reportTypeName} Documents Uploaded - {$document_no}";
    
//     // Send email
//     $mail_result = mail($emailUsr, $subject, $message, $headers);
    
//     // Redirect back with success message
//     $statusMessage = $reportType === 'F/R' && $isPlrApproved ? 
//         'Status reset to Unapproved for admin review.' : 
//         'Awaiting admin approval.';
    
//     return redirect()->route('home')
//         ->with('success', "{$reportTypeName} documents uploaded successfully. {$statusMessage}")
//         ->with('mail_status', $mail_result ? 'Mail sent successfully' : 'Mail sending failed');
// }


   public function upload(Request $request, $document_no)
{
    // Increase execution limits
    ini_set('max_execution_time', 300);
    ini_set('memory_limit', '512M');
    
    // First, check if final report is already approved
    $isFrApproved = FileTab::where('doc_no', $document_no)
        ->where('rep_tag', 'F/R')
        ->where('plr_final', 'Y')
        ->exists();
    
    if ($isFrApproved) {
        return redirect()->back()
            ->with('error', 'Final Report has already been approved for this document. No further uploads required.')
            ->withInput();
    }
    
    // Get the latest file record
    $latestFile = FileTab::where('doc_no', $document_no)
        ->orderByDesc('id')
        ->first();
    
    // Check current status
    $currentStatus = $latestFile ? $latestFile->plr_final : null;
    $requiresAction = in_array($currentStatus, ['R', 'V']);
    
    // Check if PLR is approved
    $isPlrApproved = FileTab::where('doc_no', $document_no)
        ->where('rep_tag', 'P/R')
        ->where('plr_final', 'Y')
        ->exists();
    
    // Determine if action is for F/R or P/R
    $actionIsForFR = false;
    
    if ($requiresAction) {
        // If P/R is approved, action is likely for F/R
        if ($isPlrApproved) {
            $actionIsForFR = true;
        }
        
        // If current file is F/R, action is for F/R
        if ($latestFile && $latestFile->rep_tag === 'F/R') {
            $actionIsForFR = true;
        }
    }
    
    // Get report type from form
    $reportType = $request->report_type;
    
    // **VALIDATION LOGIC**
    
    // If action is required (R or V)
    if ($requiresAction) {
        // Action is for F/R but user tries to upload P/R
        if ($actionIsForFR && $reportType === 'P/R') {
            return redirect()->back()
                ->with('error', 'This action is for Final Report. Please upload Final Report (F/R).')
                ->withInput();
        }
        
        // Action is for P/R but user tries to upload F/R
        if (!$actionIsForFR && $reportType === 'F/R') {
            return redirect()->back()
                ->with('error', 'This action is for Preliminary Report. Please upload Preliminary Report (P/R).')
                ->withInput();
        }
    } else {
        // No action required - normal flow
        
        // PLR is approved but user tries to upload P/R
        if ($isPlrApproved && $reportType === 'P/R') {
            return redirect()->back()
                ->with('error', 'Preliminary Report is already approved. Please upload Final Report (F/R).')
                ->withInput();
        }
        
        // PLR is NOT approved but user tries to upload F/R
        if (!$isPlrApproved && $reportType === 'F/R') {
            return redirect()->back()
                ->with('error', 'Preliminary Report must be approved first before uploading Final Report.')
                ->withInput();
        }
    }
    
    // Validate request
    $request->validate([
        'document.*' => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
        'remarks.*'  => 'nullable|string|max:500',
        'upload_date.*' => 'required|date',
        'estimate_amount.*' => 'nullable|numeric|min:0|max:999999999.99',
        'report_type' => 'required|in:P/R,F/R',
    ]);

    $documents = $request->file('document');
    $remarks   = $request->remarks;
    $dates     = $request->upload_date;
    $estimate_amounts = $request->estimate_amount;
    $created_by = Auth::user()->id ?? 0;

    $totalEstimate = 0;
    $documentsWithEstimate = 0;
    
    // Determine plr_final status
    $plrFinalStatus = 'N'; // Default for all uploads
    
    foreach ($documents as $index => $file) {
        $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
        $filePath = storage_path('app/public/uploads/documents/' . $fileName);
        
        $file->move(storage_path('app/public/uploads/documents/'), $fileName);
        
        // Process estimate amount
        $estimateAmount = null;
        if (isset($estimate_amounts[$index]) && $estimate_amounts[$index] !== '' && is_numeric($estimate_amounts[$index])) {
            $estimateAmount = floatval($estimate_amounts[$index]);
            $totalEstimate += $estimateAmount;
            $documentsWithEstimate++;
        }
        
        // Create record in database
        FileTab::create([
            'datetime_field'  => $dates[$index],
            'doc_no'          => $document_no,
            'remarks'         => $remarks[$index] ?? '',
            'estimate_amount' => $estimateAmount,
            'created_by'      => $created_by,
            'file_path'       => $fileName,
            'plr_final'       => $plrFinalStatus,
            'rep_tag'         => $reportType,
            'created_at'      => now(), // Laravel timestamps
    'updated_at'      => now(),
        ]);
    }
    
    // Email configuration
    ini_set("SMTP", "vqs3572.pair.com");
    
    $emailUsr   = 'owais.zahid@ail.atlas.pk';
    $emailUsrCC = 'owais.zahid@ail.atlas.pk, owais.zahid@ail.atlas.pk';
    ini_set("sendmail_from", $emailUsr);
    
    $boundary = md5(time());
    
    // === EMAIL HEADERS ===
    $headers  = "From: AIL - Surveyor <{$emailUsr}>\r\n";
    $headers .= "Cc: {$emailUsrCC}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n\r\n";
        $userName = session('user.name', 'System Administrator');
    // === EMAIL BODY ===
    $uploadContext = '';
    if ($requiresAction) {
        if ($actionIsForFR) {
            $uploadContext = 'Response to Final Report ' . ($currentStatus === 'R' ? 'Reminder' : 'Revision');
        } else {
            $uploadContext = 'Response to Preliminary Report ' . ($currentStatus === 'R' ? 'Reminder' : 'Revision');
        }
    } else {
        if ($isPlrApproved && $reportType === 'F/R') {
            $uploadContext = 'Initial Final Report Submission';
        } else {
            $uploadContext = 'Initial Preliminary Report Submission';
        }
    }
    
    $bodyContent = '
    <html>
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <style>
        body {
          margin: 0;
          padding: 0;
          background-color: #f4f6f8;
          font-family: "Segoe UI", Arial, sans-serif;
          color: #333;
        }
        table {
          border-collapse: collapse;
          width: 100%;
        }
        .email-wrapper {
          width: 100%;
          background-color: #f4f6f8;
          padding: 20px 0;
        }
        .email-container {
          max-width: 600px;
          margin: 0 auto;
          background-color: #ffffff;
          border-radius: 8px;
          box-shadow: 0 2px 6px rgba(0,0,0,0.05);
          overflow: hidden;
        }
        .header {
          background-color: #007bff;
          color: #ffffff;
          text-align: center;
          font-size: 18px;
          font-weight: 600;
          padding: 16px 0;
        }
        .content {
          padding: 25px 30px;
          line-height: 1.6;
          font-size: 15px;
        }
        .doc-info {
          background-color: #eef3fa;
          border-left: 4px solid #007bff;
          padding: 10px 15px;
          font-size: 14px;
          margin-bottom: 20px;
        }
        .doc-details {
          background-color: #f8f9fa;
          padding: 15px;
          border-radius: 5px;
          margin-bottom: 20px;
          font-size: 14px;
          border: 1px solid #e9ecef;
        }
        .doc-details p {
          margin: 5px 0;
        }
        .footer {
          border-top: 1px solid #eaeaea;
          color: #666;
          padding: 20px 30px;
          font-size: 14px;
        }
        .footer strong {
          color: #333;
        }
      </style>
    </head>
    <body>
      <table class="email-wrapper">
        <tr>
          <td align="center">
            <table class="email-container">
              <tr>
                <td class="header">
                  Atlas Insurance Claims Management
                </td>
              </tr>
              <tr>
                <td class="content">
                  <div class="doc-info">
                    <strong>Document No:</strong> ' . htmlspecialchars($document_no) . '
                  </div>
                  
                  <div class="doc-details">
                    <p><strong>Upload Summary:</strong></p>
                    <p>Report Type: ' . ($reportType === 'P/R' ? 'Preliminary Report (P/R)' : 'Final Report (F/R)') . '</p>
                    <p>Upload Context: ' . $uploadContext . '</p>
                    
                    <p>Total Documents Uploaded: ' . count($documents) . '</p>';
    
    if ($documentsWithEstimate > 0) {
        $bodyContent .= '
                    <p>Documents with Estimate Amount: ' . $documentsWithEstimate . '</p>
                    <p>Total Estimated Amount: Rs' . number_format($totalEstimate, 2) . '</p>';
    } else {
        $bodyContent .= '
                    <p>No estimate amounts provided.</p>';
    }
    
    $bodyContent .= '
                  </div>
                  
                  <p>Dear Sir/Ma\'am,</p>
                  <p>Documents have been successfully uploaded for the above-mentioned document number.</p>
                  <p>Kindly review the uploaded files at your earliest convenience.</p>
                  
                  <p style="margin-top: 30px;">Best regards,<br><strong>' . htmlspecialchars($userName) . '</strong></p>
                 
                </td>
              </tr>
              
            </table>
          </td>
        </tr>
      </table>
    </body>
    </html>';
    
    // === BUILD MESSAGE BODY ===
    $message  = "--{$boundary}\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\r\n";
    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $message .= $bodyContent . "\r\n";
    $message .= "--{$boundary}--";
    
    // === SUBJECT ===
    $reportTypeName = $reportType === 'P/R' ? 'Preliminary Report' : 'Final Report';
    $subject = "{$reportTypeName} - {$uploadContext} - {$document_no}";
    
    // Send email
    $mail_result = mail($emailUsr, $subject, $message, $headers);
    
    // Success message
    $statusMessage = '';
    if ($requiresAction) {
        if ($actionIsForFR) {
            $statusMessage = 'Final Report uploaded in response to ' . ($currentStatus === 'R' ? 'reminder' : 'revision request') . '. Awaiting admin review.';
        } else {
            $statusMessage = 'Preliminary Report uploaded in response to ' . ($currentStatus === 'R' ? 'reminder' : 'revision request') . '. Awaiting admin review.';
        }
    } else {
        if ($isPlrApproved && $reportType === 'F/R') {
            $statusMessage = 'Final Report uploaded successfully. Awaiting admin approval.';
        } else {
            $statusMessage = 'Documents uploaded successfully. Awaiting admin approval.';
        }
    }
    
    return redirect()->route('home')
        ->with('success', "{$reportTypeName} documents uploaded successfully. {$statusMessage}")
        ->with('mail_status', $mail_result ? 'Mail sent successfully' : 'Mail sending failed');
}


public function downloadFile($filename)
{
    $path = storage_path('app/public/uploads/documents/' . $filename);

    if (!file_exists($path)) {
        abort(404, 'File not found');
    }

    return response()->file($path);
}
// public function approvePlr($docNo)  /// upadteed by time , user
// {
//     try {
//         // Get the latest uploaded record for this document number
//         $file = \App\Models\FileTab::where('doc_no', $docNo)
//                                    ->orderByDesc('id')
//                                    ->first();

//         if (!$file) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Document not found'
//             ]);
//         }

//         // Approve only the latest entry
//         $file->plr_final = 'Y';
//         $file->save();

//         return response()->json([
//             'success' => true,
//             'message' => "PLR approved for document ID: {$file->id}, uploaded on: {$file->datetime_field}"
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => $e->getMessage()
//         ]);
//     }
// }

public function approvePlr($docNo)
{
    try {
        // Check if user is authenticated
        $userName = Session::get('user')['name'];
        
        // Get the LATEST record for this document
        $latestFile = FileTab::where('doc_no', $docNo)
                           ->orderByDesc('created_at')
                           ->first();

        if (!$latestFile) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ]);
        }

        // Check report type
        $reportType = $latestFile->rep_tag;
        $reportTypeName = $reportType === 'F/R' ? 'Final Report' : 'Preliminary Report';
        
        // Update ALL records of the SAME report type
        $updatedCount = FileTab::where('doc_no', $docNo)
            ->where('rep_tag', $reportType)
            ->update([
                'plr_final' => 'Y',
                'updated_by' => $userName,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => "{$reportTypeName} approved successfully",
            'data' => [
                'document_no' => $docNo,
                'report_type' => $reportTypeName,
                'approved_by' => $userName,
                'records_updated' => $updatedCount,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('PLR Approval Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Send PLR Revision Email
 * 
 */
// public function sendRevisionEmail(Request $request, $docNo)
// {
//     try {
//         // Validate the request
//         $request->validate([
//             'sender'   => 'required|email',
//             'receiver' => 'required|email',
//             'sub'      => 'required|string|max:255',
//             'body'     => 'required|string',
//         ]);

//         // Save email log in DB
//         EmailLog::create([
//             'uw_doc'     => $docNo,
//             'sender'     => $request->sender,
//             'receiver'   => $request->receiver,
//             'sub'        => $request->sub,
//             'body'       => $request->body,
//             'rep_name'   => 'PLR',
//             'route'      => 'PLR REVISION',
//             'created_by' => session('user')['name'] ?? 'System',
//         ]);

//         // Configure SMTP server
//         ini_set("SMTP", "vqs3572.pair.com");
//         ini_set("sendmail_from", $request->sender);

//         $boundary = md5(time());

//         // === EMAIL HEADERS ===
//         $headers  = "From: AIL - System <{$request->sender}>\r\n";
//         if ($request->has('cc') && !empty($request->cc)) {
//             $headers .= "Cc: {$request->cc}\r\n";
//         }
//         $headers .= "MIME-Version: 1.0\r\n";
//         $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n\r\n";

//         // === EMAIL BODY (Styled HTML - Same as sendReminder) ===
//         $bodyContent = '
// <html>
// <head>
//   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
//   <style>
//     body {
//       margin: 0;
//       padding: 0;
//       background-color: #f4f6f8;
//       font-family: "Segoe UI", Arial, sans-serif;
//       color: #333;
//     }
//     table {
//       border-collapse: collapse;
//       width: 100%;
//     }
//     .email-wrapper {
//       width: 100%;
//       background-color: #f4f6f8;
//       padding: 20px 0;
//     }
//     .email-container {
//       max-width: 600px;
//       margin: 0 auto;
//       background-color: #ffffff;
//       border-radius: 8px;
//       box-shadow: 0 2px 6px rgba(0,0,0,0.05);
//       overflow: hidden;
//     }
//     .header {
//       background-color: #007bff;
//       text-align: center;
//       padding: 18px 0 12px 0;
//     }
//     .header img {
//       max-width: 140px;
//       height: auto;
//       display: block;
//       margin: 0 auto 6px auto;
//     }
//     .header-title {
//       color: #ffffff;
//       font-size: 18px;
//       font-weight: 600;
//       margin: 0;
//     }
//     .content {
//       padding: 25px 30px;
//       line-height: 1.6;
//       font-size: 15px;
//     }
//     .doc-info {
//       background-color: #eef3fa;
//       border-left: 4px solid #007bff;
//       padding: 10px 15px;
//       font-size: 14px;
//       margin-bottom: 20px;
//     }
//     .footer {
//       border-top: 1px solid #eaeaea;
//       color: #666;
//       padding: 20px 30px;
//       font-size: 14px;
//     }
//     .footer strong {
//       color: #333;
//     }
//   </style>
// </head>
// <body>
//   <table class="email-wrapper">
//     <tr>
//       <td align="center">
//         <table class="email-container">
//           <tr>
//             <td class="header">
//               <p class="header-title">Atlas Insurance Claims Management</p>
//             </td>
//           </tr>
//           <tr>
//             <td class="content">';

//         // Document info
//         if (!empty($docNo)) {
//             $bodyContent .= '
//               <div class="doc-info">
//                 <strong>Document No:</strong> ' . htmlspecialchars($docNo) . '
//               </div>';
//         }

//         // Main message
//         $bodyContent .= '
//               <div>' . nl2br(htmlspecialchars($request->body)) . '</div>
//             </td>
//           </tr>
//           <tr>
//             <td class="footer">
//               <p><strong>Best Regards,</strong><br>
//               Atlas Insurance Claims Management Team</p>
//             </td>
//           </tr>
//         </table>
//       </td>
//     </tr>
//   </table>
// </body>
// </html>';

//         // === BUILD MESSAGE BODY ===
//         $message  = "--$boundary\r\n";
//         $message .= "Content-Type: text/html; charset=UTF-8\r\n";
//         $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
//         $message .= $bodyContent . "\r\n";
//         $message .= "--$boundary--";

//         // Send email
//         $sent = mail($request->receiver, $request->sub, $message, $headers);

//         if (!$sent) {
//             throw new \Exception("Mail sending failed. Please check SMTP settings.");
//         }

//         return response()->json([
//             'success' => true,
//             'message' => 'Revision email sent successfully!',
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => $e->getMessage(),
//         ], 500);
//     }
// }


public function sendRevisionEmail(Request $request, $docNo)
{
    try {
        // Validate the request
        $request->validate([
            'sender'   => 'required|email',
            'receiver' => 'required|email',
            'sub'      => 'required|string|max:255',
            'body'     => 'required|string',
            'cc' => 'nullable|string'
        ]);

        // Get current logged-in user's email from session
        $currentUserEmail = session('user')['email'] ?? null;
        
        // Get the sender email from request (which should be the current user's email)
        $senderEmail = $request->sender;
        
        // Process CC emails - this time with proper handling for multiple emails
        $allCCEmails = [];
        
        // Always include current user's email in CC
        if ($currentUserEmail) {
            $allCCEmails[] = $currentUserEmail;
        }
        
        // Add provided CC emails if they exist
        if ($request->has('cc') && !empty($request->cc)) {
            // Split by comma and clean up
            $additionalCCs = explode(',', $request->cc);
            
            foreach ($additionalCCs as $email) {
                $email = trim($email);
                // Validate each email and add if not already in list
                if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    if (!in_array($email, $allCCEmails) && $email !== $senderEmail) {
                        $allCCEmails[] = $email;
                    }
                }
            }
        }
        
        $ccString = !empty($allCCEmails) ? implode(', ', $allCCEmails) : null;

        // Save email log in DB - using the correct column name email_cc
        EmailLog::create([
            'uw_doc'     => $docNo,
            'sender'     => $senderEmail,
            'receiver'   => $request->receiver,
            'email_cc'   => $ccString, // Use the correct column name: email_cc
            'sub'        => $request->sub,
            'body'       => $request->body,
            'rep_name'   => 'PLR',
            'route'      => 'PLR REVISION',
            'created_by' => session('user')['name'] ?? 'System',
        ]);

        // Configure SMTP server
        ini_set("SMTP", "vqs3572.pair.com");
        ini_set("sendmail_from", $senderEmail);

        $boundary = md5(time());

        // === EMAIL HEADERS ===
        $headers  = "From: AIL - System <{$senderEmail}>\r\n";
        
        // Add CC header if we have CC recipients
        if (!empty($ccString)) {
            $headers .= "Cc: {$ccString}\r\n";
        }
        
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n\r\n";
$userName = session('user.name', 'System Administrator');
        // === EMAIL BODY (Styled HTML - Same as sendReminder) ===
        $bodyContent = '
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #f4f6f8;
      font-family: "Segoe UI", Arial, sans-serif;
      color: #333;
    }
    table {
      border-collapse: collapse;
      width: 100%;
    }
    .email-wrapper {
      width: 100%;
      background-color: #f4f6f8;
      padding: 20px 0;
    }
    .email-container {
      max-width: 600px;
      margin: 0 auto;
      background-color: #ffffff;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      overflow: hidden;
    }
    .header {
      background-color: #007bff;
      text-align: center;
      padding: 18px 0 12px 0;
    }
    .header img {
      max-width: 140px;
      height: auto;
      display: block;
      margin: 0 auto 6px auto;
    }
    .header-title {
      color: #ffffff;
      font-size: 18px;
      font-weight: 600;
      margin: 0;
    }
    .content {
      padding: 25px 30px;
      line-height: 1.6;
      font-size: 15px;
    }
    .doc-info {
      background-color: #eef3fa;
      border-left: 4px solid #007bff;
      padding: 10px 15px;
      font-size: 14px;
      margin-bottom: 20px;
    }
    .footer {
      border-top: 1px solid #eaeaea;
      color: #666;
      padding: 20px 30px;
      font-size: 14px;
    }
    .footer strong {
      color: #333;
    }
  </style>
</head>
<body>
  <table class="email-wrapper">
    <tr>
      <td align="center">
        <table class="email-container">
          <tr>
            <td class="header">
              <p class="header-title">Atlas Insurance Claims Management</p>
            </td>
          </tr>
          <tr>
            <td class="content">';

        // Document info
        if (!empty($docNo)) {
            $bodyContent .= '
              <div class="doc-info">
                <strong>Document No:</strong> ' . htmlspecialchars($docNo) . '
              </div>';
        }

       
        // Main message
$bodyContent .= '
    <div>' . nl2br(htmlspecialchars($request->body)) . '</div>
    
    <p style="margin-top: 30px;">Best regards,<br><strong>' . htmlspecialchars($userName) . '</strong></p>
</td>
</tr>

</table>
</td>
</tr>
</table>
</body>
</html>';

        // === BUILD MESSAGE BODY ===
        $message  = "--$boundary\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $bodyContent . "\r\n";
        $message .= "--$boundary--";

        // Send email
        $sent = mail($request->receiver, $request->sub, $message, $headers);

        if (!$sent) {
            throw new \Exception("Mail sending failed. Please check SMTP settings.");
        }

        return response()->json([
            'success' => true,
            'message' => 'Revision email sent successfully!',
            'cc_recipients' => $allCCEmails // Return CC recipients for debugging
        ]);

    } catch (\Exception $e) {
        // Log the error
        \Log::error('Revision email failed: ' . $e->getMessage(), [
            'doc_no' => $docNo,
            'sender' => $request->sender ?? 'unknown',
            'receiver' => $request->receiver ?? 'unknown',
            'cc_input' => $request->cc ?? 'none'
        ]);
        
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}



 public function getDocuments($document_no)
{
    try {
        $docs = FileTab::where('doc_no', $document_no)
            ->orderBy('datetime_field', 'desc')
            ->get()
            ->map(function ($doc) {
                $filename = $doc->file_path; // Already only filename

                return [
                    'date'     => $doc->datetime_field ? date('d-m-Y', strtotime($doc->datetime_field)) : '-',
                    'remarks'  => $doc->remarks ?? '-',
                    'estimate_amount' => $doc->estimate_amount ? '₹ ' . number_format($doc->estimate_amount, 2) : '-',
                    'url'      => $filename ? route('documents.download', ['filename' => $filename]) : '#',
                    'fileName' => $filename ?? 'file.pdf',
                ];
            });

        return response()->json($docs);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


public function downloadAllAsZip($document_no)
{
    try {
        
        $files = FileTab::where('doc_no', $document_no)->get();

        if ($files->isEmpty()) {
            return response()->json(['error' => 'No files found for this document'], 404);
        }

        $zipFileName = "documents_{$document_no}_" . time() . ".zip";
        $tempDir = storage_path('app/temp');
        $zipFilePath = $tempDir . '/' . $zipFileName;

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return response()->json(['error' => 'Failed to create ZIP file'], 500);
        }

        $addedCount = 0;

        foreach ($files as $file) {
            $fileName = $file->file_path;
            $storagePath = storage_path('app/public/uploads/documents/' . $fileName);

            if (file_exists($storagePath)) {
                $zip->addFile($storagePath, $fileName);
                $addedCount++;
            } else {
                Log::warning("File not found for ZIP: " . $storagePath);
            }
        }

        $zip->close();

        if ($addedCount === 0) {
            @unlink($zipFilePath);
            return response()->json(['error' => 'No valid files found to download'], 404);
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);

    } catch (\Exception $e) {
        Log::error('ZIP Download Error: ' . $e->getMessage());
        return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
    }
}

/**
 * Simple test function to check if PLR approval is working
 */
public function testPlrApproval()
{
    try {
        // Get the first available document
        $file = \App\Models\FileTab::orderBy('id')->first();
        
        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'No documents found in database'
            ]);
        }
        
        // Test data before approval
        $docNo = $file->doc_no;
        $beforeStatus = $file->plr_final;
        $beforeUpdatedBy = $file->updated_by;
        $beforeUpdatedAt = $file->updated_at;
        
        // Simulate a user for testing
        $user = \App\Models\User::first();
        if ($user) {
            Auth::login($user);
        }
        
        // Call the approvePlr function
        $approvalResult = $this->approvePlr($docNo);
        $approvalData = $approvalResult->getData();
        
        // Get updated record
        $updatedFile = \App\Models\FileTab::where('doc_no', $docNo)
                                        ->orderByDesc('id')
                                        ->first();
        
        return response()->json([
            'test' => 'PLR Approval Function Test',
            'document_number' => $docNo,
            'before_approval' => [
                'plr_final' => $beforeStatus,
                'updated_by' => $beforeUpdatedBy,
                'updated_at' => $beforeUpdatedAt
            ],
            'after_approval' => [
                'plr_final' => $updatedFile->plr_final,
                'updated_by' => $updatedFile->updated_by,
                'updated_at' => $updatedFile->updated_at
            ],
            'approval_result' => [
                'success' => $approvalData->success ?? false,
                'message' => $approvalData->message ?? 'No message'
            ],
            'test_passed' => ($updatedFile->plr_final == 'Y' && !empty($updatedFile->updated_by)),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'test' => 'PLR Approval Function Test',
            'success' => false,
            'error' => $e->getMessage(),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ], 500);
    }
}




}