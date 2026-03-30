<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
class ReminderController extends Controller
{

  
    // Show the reminder form
    public function showForm($toRole)
    {
        $defaultBody = "This is a reminder regarding your pending report.";

        return view('reminder.form', [
            'toRole' => $toRole,
            'defaultBody' => $defaultBody
        ]);
    }

    
//         public function sendReminder(Request $request)
//     {
//         try {
//             // Validation
//             $validated = $request->validate([
//                 'uw_doc' => 'nullable|string|max:255',
//                 'sender' => 'required|email',
//                 'receiver_email' => 'required|email',
//                 'receiver_role' => 'required|string',
//                 'sub' => 'required|string|max:500',
//                 'body' => 'required|string',
//                 'cc' => 'nullable|email'
//             ]);

//             // Get current user role (normalize to lowercase)
//            $currentUser = session('user');

// // Normalize role: lowercase + trim
// $senderRole = strtolower(trim($currentUser['role'] ?? ''));

// if ($senderRole === 'admin') {
//     $route = 'surveyor'; // Admin sends to Surveyor
// } elseif ($senderRole === 'surveyor') {
//     $route = 'client';   // Surveyor sends to Client
// } else {
//     $route = 'unknown';  // fallback in case of unexpected role
// }

            

//             // Configure SMTP
// ini_set("SMTP", "vqs3572.pair.com");
// ini_set("sendmail_from", $request->sender);

// $boundary = md5(time());

// // === EMAIL HEADERS ===
// $headers  = "From: AIL - System <{$request->sender}>\r\n";
// if ($request->has('cc') && !empty($request->cc)) {
//     $headers .= "Cc: {$request->cc}\r\n";
// }
// $headers .= "MIME-Version: 1.0\r\n";
// $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n\r\n";

// // === EMAIL BODY (Styled HTML) ===
// $bodyContent = '
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
//             <td class="content">
// ';

// // Document info
// if (!empty($request->uw_doc)) {
//     $bodyContent .= '
//               <div class="doc-info">
//                 <strong>Document No:</strong> ' . htmlspecialchars($request->uw_doc) . '
//               </div>';
// }

// // Main message
// $bodyContent .= '
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



// // === BUILD MESSAGE BODY ===
// $message  = "--$boundary\r\n";
// $message .= "Content-Type: text/html; charset=UTF-8\r\n";
// $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
// $message .= $bodyContent . "\r\n";
// $message .= "--$boundary--";


//             // Send the email
//             $sent = mail($request->receiver_email, $request->sub, $message, $headers);

//             if (!$sent) {
//                 throw new \Exception("Mail sending failed. Please check SMTP settings or try again later.");
//             }

//             // Save to DB with correct route
//             $emailLog = new EmailLog();
//             $emailLog->uw_doc = $request->uw_doc ?? null;
//             $emailLog->curdatetime = now();
//             $emailLog->sender = $request->sender; 
//             $emailLog->receiver = $request->receiver_email; 
//             $emailLog->sub = $request->sub;
//             $emailLog->body = $request->body;
//             $emailLog->rep_name = 'PLR';
//             $emailLog->created_by = Auth::id() ?? null;
//             $emailLog->route = $route; // This will be 'surveyor' for admin, 'client' for surveyor
//             $emailLog->save();

//             // // Log for debugging
//             // \Log::info('Reminder sent', [
//             //     'sender_role' => $senderRole,
//             //     'route_saved' => $route,
//             //     'doc_no' => $request->uw_doc
//             // ]);

//             // Return JSON response for AJAX
//             if ($request->expectsJson() || $request->ajax()) {
//                 return response()->json([
//                     'success' => true,
//                     'message' => 'Reminder sent successfully!',
//                     'email_log_id' => $emailLog->id,
//                     'route' => $route,
//                     'sender_role' => $senderRole // For debugging
//                 ], 200);
//             }

//             return redirect()->back()->with('success', 'Reminder sent successfully!');

//         } catch (\Illuminate\Validation\ValidationException $e) {
//             if ($request->expectsJson() || $request->ajax()) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Validation failed.',
//                     'errors' => $e->errors()
//                 ], 422);
//             }
            
//             return redirect()->back()->withErrors($e->errors())->withInput();

//         } catch (\Exception $e) {
//             $errorMessage = $e->getMessage();
            
//             \Log::error('Reminder email failed: ' . $errorMessage, [
//                 'user_id' => Auth::id(),
//                 'user_role' => Auth::user()->role ?? 'unknown',
//                 'uw_doc' => $request->uw_doc,
//                 'receiver' => $request->receiver_email,
//                 'trace' => $e->getTraceAsString()
//             ]);

//             if ($request->expectsJson() || $request->ajax()) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Failed to send reminder: ' . $errorMessage
//                 ], 500);
//             }

//             return redirect()->back()->with('error', 'Failed to send reminder: ' . $errorMessage);
//         }
//     }

    public function sendReminder(Request $request)
{
    try {
        // Validation - remove 'sender' from required fields
        $validated = $request->validate([
            'uw_doc' => 'nullable|string|max:255',
            'receiver_email' => 'required|email',
            'receiver_role' => 'required|string',
            'sub' => 'required|string|max:500',
            'body' => 'required|string',
            'cc' => 'nullable|string' // Change from 'email' to 'string' to allow comma-separated emails
        ]);

        // Get current user from session
        $currentUser = session('user');
        
        if (!$currentUser || !isset($currentUser['email'])) {
            throw new \Exception("User session not found or email missing. Please log in again.");
        }

        // Get sender email from session user
        $senderEmail = $currentUser['email'];
        
        // Normalize role: lowercase + trim
        $senderRole = strtolower(trim($currentUser['role'] ?? ''));

        // Determine route based on sender role
        if ($senderRole === 'admin') {
            $route = 'surveyor'; // Admin sends to Surveyor
        } elseif ($senderRole === 'surveyor') {
            $route = 'client';   // Surveyor sends to Client
        } else {
            $route = 'unknown';  // fallback in case of unexpected role
        }

        // Process CC emails
        $allCCEmails = [];
        
        // Add sender's email to CC list
        $allCCEmails[] = $senderEmail;
        
        // Process additional CC emails from request
        if ($request->has('cc') && !empty($request->cc)) {
            // Split by comma and clean up each email
            $additionalCCs = explode(',', $request->cc);
            
            foreach ($additionalCCs as $email) {
                $email = trim($email);
                // Validate each email individually
                if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    // Add to list if not already present (including sender's email check)
                    if (!in_array($email, $allCCEmails) && $email !== $senderEmail) {
                        $allCCEmails[] = $email;
                    }
                }
            }
        }
        
        // Create comma-separated CC string for email headers
        $ccString = implode(', ', $allCCEmails);

        // Configure SMTP
        ini_set("SMTP", "vqs3572.pair.com");
        ini_set("sendmail_from", $senderEmail);

        $boundary = md5(time());

        // === EMAIL HEADERS ===
        $headers = "From: AIL - System <{$senderEmail}>\r\n";
        
        // Add CC header if we have CC emails
        if (!empty($allCCEmails)) {
            $headers .= "Cc: {$ccString}\r\n";
        }
        
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n\r\n";
 $userName = session('user.name', 'System Administrator');
        // === EMAIL BODY (Styled HTML) ===
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
        if (!empty($request->uw_doc)) {
            $bodyContent .= '
                      <div class="doc-info">
                        <strong>Document No:</strong> ' . htmlspecialchars($request->uw_doc) . '
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
        $message = "--$boundary\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $bodyContent . "\r\n";
        $message .= "--$boundary--";

        // Send the email
        $sent = mail($request->receiver_email, $request->sub, $message, $headers);

        if (!$sent) {
            throw new \Exception("Mail sending failed. Please check SMTP settings or try again later.");
        }

        // Save to DB with correct route
        $emailLog = new EmailLog();
        $emailLog->uw_doc = $request->uw_doc ?? null;
        $emailLog->curdatetime = now();
        $emailLog->sender = $senderEmail;
        $emailLog->receiver = $request->receiver_email; 
        $emailLog->sub = $request->sub;
        $emailLog->body = $request->body;
        $emailLog->rep_name = 'PLR';
        $emailLog->created_by = Auth::id() ?? null;
        $emailLog->route = $route;
        $emailLog->email_cc = $ccString; // Save all CC emails
        $emailLog->save();

        // Return JSON response for AJAX
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Reminder sent successfully!',
                'email_log_id' => $emailLog->id,
                'route' => $route,
                'sender_role' => $senderRole,
                'sender_email' => $senderEmail,
                'cc_emails' => $allCCEmails,
                'cc_string' => $ccString
            ], 200);
        }

        return redirect()->back()->with('success', 'Reminder sent successfully!');

    } catch (\Illuminate\Validation\ValidationException $e) {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }
        
        return redirect()->back()->withErrors($e->errors())->withInput();

    } catch (\Exception $e) {
        $errorMessage = $e->getMessage();
        
        \Log::error('Reminder email failed: ' . $errorMessage, [
            'user_id' => Auth::id(),
            'user_email' => $senderEmail ?? 'unknown',
            'user_role' => $senderRole ?? 'unknown',
            'uw_doc' => $request->uw_doc,
            'receiver' => $request->receiver_email,
            'cc_attempted' => $request->cc ?? 'none',
            'trace' => $e->getTraceAsString()
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reminder: ' . $errorMessage
            ], 500);
        }

        return redirect()->back()->with('error', 'Failed to send reminder: ' . $errorMessage);
    }
}

    // reminder history for a specific document 
    public function getReminderHistory($uwDoc)
    {
        try {
            $reminders = EmailLog::where('uw_doc', $uwDoc)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $reminders
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reminder history.'
            ], 500);
        }
    }
   public function send()
{
    // Fetch all logs regardless of route
    $logs = EmailLog::orderBy('id', 'desc')->get();

    return response()->json($logs);
}
public function debugAllLogs()
{
    // Fetch all logs
    $logs = EmailLog::orderBy('id', 'asc')->get();

    // Group logs by route
    $grouped = $logs->groupBy('route');

    return response()->json([
        'all_logs' => $logs,
        'grouped_by_route' => $grouped,
    ]);
}
// public function getLogsByDocument($doc_no)
// {
//     try {
//         \Log::info('getLogsByDocument called for: ' . $doc_no);
        
//         if (empty($doc_no) || $doc_no == 'undefined') {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Invalid document number',
//                 'logs' => []
//             ], 400);
//         }
        
//         \Log::info('Searching for document: ' . $doc_no);
        
//         // Status mapping function with meaningful names
//         $getStatusText = function($plr_final, $rep_tag) {
//             if ($plr_final === null || $plr_final === '' || $plr_final === 'null') {
//                 return 'Not Uploaded Yet';
//             }
            
//             // Map rep_tag to meaningful names
//             $repTagNames = [
//                 'P/R' => 'Preliminary Report',
//                 'F/R' => 'Final Report',
//                 null => 'Report',
//                 '' => 'Report'
//             ];
            
//             $repName = $repTagNames[$rep_tag] ?? 'Report';
            
//             // Map plr_final to meaningful status
//             $statusMap = [
//                 'Y' => 'Approved',
//                 'N' => 'Pending Approval',
//                 'I' => 'In Process',
//                 'R' => 'Reminder Sent',
//                 'V' => 'Revision Requested'
//             ];
            
//             $statusName = $statusMap[$plr_final] ?? 'Unknown';
            
//             return $repName . ' ' . $statusName;
//         };
        
//         $allLogs = [];
        
//         // 1. GET CURRENT FILE STATUS (MOST RECENT)
//         $latestFile = DB::table('filestab')
//             ->where('doc_no', $doc_no)
//             ->orderBy('created_at', 'desc')
//             ->first();
        
//         if ($latestFile) {
//             // Check if it's a file upload or just status
//             $hasFileUpload = !empty($latestFile->file_path);
            
//             // Map rep_tag to meaningful name
//             $repTagDisplay = '';
//             if ($latestFile->rep_tag) {
//                 $repTagNames = [
//                     'P/R' => 'Preliminary Report',
//                     'F/R' => 'Final Report'
//                 ];
//                 $repTagDisplay = $repTagNames[$latestFile->rep_tag] ?? $latestFile->rep_tag;
//             }
            
//             // Determine activity type
//             $activityType = 'Current Status';
//             $route = 'Current Status';
            
//             if ($hasFileUpload) {
//                 $activityType = 'Document Upload';
//                 $route = 'Document Upload';
//             }
            
//             // Get status text - THIS IS THE REAL CURRENT STATUS
//             $statusText = $getStatusText($latestFile->plr_final, $latestFile->rep_tag);
            
//             $currentStatusLog = [
//                 'id' => 'file_' . $latestFile->id,
//                 'type' => 'current_status',
//                 'uw_doc' => $doc_no,
//                 'curdatetime' => $latestFile->updated_at ?: $latestFile->created_at,
//                 'sender' => 'System',
//                 'receiver' => 'System',
//                 'cc_email' => null,
//                 'sub' => $activityType,
//                 'body' => $hasFileUpload 
//                     ? ($latestFile->remarks ?: 'Document uploaded') . ' (Status: ' . $statusText . ')'
//                     : 'Status updated to: ' . $statusText,
//                 'route' => $route,
//                 'rep_tag_display' => $repTagDisplay,
//                 'plr_final' => $latestFile->plr_final, // THIS IS THE REAL STATUS
//                 'remarks' => $latestFile->remarks,
//                 'source' => 'filestab',
//                 'is_current' => true,
//                 'sort_date' => $latestFile->updated_at ?: $latestFile->created_at
//             ];
            
//             // Add current status as FIRST entry
//             $allLogs[] = $currentStatusLog;
//         }
        
//         // 2. GET ALL EMAIL LOGS (HISTORICAL RECORD)
//         $emailLogs = EmailLog::where('uw_doc', $doc_no)
//             ->orderBy('curdatetime', 'desc')
//             ->get();
        
//         foreach ($emailLogs as $email) {
//             // Determine email type
//             $subject = strtolower($email->sub);
//             $body = strtolower($email->body ?? '');
            
//             $emailType = 'Email';
//             $route = 'Email';
            
//             if (str_contains($subject, 'reminder') || str_contains($body, 'reminder')) {
//                 $emailType = 'Reminder';
//                 $route = 'Reminder Email';
//             } elseif (str_contains($subject, 'revision') || str_contains($body, 'revision') || str_contains($subject, 'plr')) {
//                 $emailType = 'Revision Request';
//                 $route = 'PLR Revision';
//             } elseif (str_contains($subject, 'approve') || str_contains($body, 'approve')) {
//                 $emailType = 'Approval Notice';
//                 $route = 'Approval Email';
//             }
            
//             // Try to extract report type from email body
//             $repTagDisplay = '';
//             if (str_contains($body, 'preliminary') || str_contains($subject, 'preliminary')) {
//                 $repTagDisplay = 'Preliminary Report';
//             } elseif (str_contains($body, 'final') || str_contains($subject, 'final')) {
//                 $repTagDisplay = 'Final Report';
//             }
            
//             // DON'T set plr_final for email logs - they're just communication history
//             $allLogs[] = [
//                 'id' => 'email_' . $email->id,
//                 'type' => 'email',
//                 'uw_doc' => $doc_no,
//                 'curdatetime' => $email->curdatetime,
//                 'sender' => $email->sender,
//                 'receiver' => $email->receiver,
//                 'cc_email' => $email->email_cc ?? null,
//                 'sub' => $emailType,
//                 'body' => $email->sub . ': ' . ($email->body ? substr($email->body, 0, 100) . '...' : ''),
//                 'route' => $route,
//                 'rep_tag_display' => $repTagDisplay,
//                 'plr_final' => null, // Don't show status for emails - they're just communication history
//                 'source' => 'email_logs',
//                 'is_current' => false,
//                 'sort_date' => $email->curdatetime
//             ];
//         }
        
//         // 3. If no logs at all, create default
//         if (empty($allLogs)) {
//             $allLogs[] = [
//                 'id' => 'default_' . $doc_no,
//                 'type' => 'status',
//                 'uw_doc' => $doc_no,
//                 'curdatetime' => date('Y-m-d H:i:s'),
//                 'sender' => 'System',
//                 'receiver' => 'System',
//                 'cc_email' => null,
//                 'sub' => 'Document Status',
//                 'body' => 'Document has not been uploaded yet',
//                 'route' => 'Initial Status',
//                 'rep_tag_display' => '',
//                 'plr_final' => null,
//                 'source' => 'system',
//                 'is_current' => true,
//                 'sort_date' => date('Y-m-d H:i:s')
//             ];
//         }
        
//         // Sort all logs by date (newest first)
//         usort($allLogs, function($a, $b) {
//             return strtotime($b['sort_date']) - strtotime($a['sort_date']);
//         });
        
//         \Log::info('Total logs found: ' . count($allLogs));
        
//         return response()->json([
//             'success' => true,
//             'message' => 'Activity logs fetched successfully',
//             'document_no' => $doc_no,
//             'logs' => $allLogs,
//             'count' => count($allLogs),
//             'current_status' => $allLogs[0]['body'] ?? 'Unknown'
//         ]);
        
//     } catch (\Exception $e) {
//         \Log::error('Error in getLogsByDocument: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        
//         return response()->json([
//             'success' => false,
//             'message' => 'Error fetching logs: ' . $e->getMessage(),
//             'logs' => []
//         ], 500);
//     }
// }
public function getLogsByDocument($doc_no)
{
    try {
        \Log::info('getLogsByDocument called for: ' . $doc_no);
        
        if (empty($doc_no) || $doc_no == 'undefined') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid document number',
                'logs' => []
            ], 400);
        }
        
        \Log::info('Searching for document: ' . $doc_no);
        
        // Status mapping function with meaningful names
        $getStatusText = function($plr_final, $rep_tag) {
            if ($plr_final === null || $plr_final === '' || $plr_final === 'null') {
                return 'Not Uploaded Yet';
            }
            
            // Map rep_tag to meaningful names
            $repTagNames = [
                'P/R' => 'Preliminary Report',
                'F/R' => 'Final Report',
                null => 'Report',
                '' => 'Report'
            ];
            
            $repName = $repTagNames[$rep_tag] ?? 'Report';
            
            // Map plr_final to meaningful status
            $statusMap = [
                'Y' => 'Approved',
                'N' => 'Pending Approval',
                'I' => 'In Process',
                'R' => 'Reminder Sent',
                'V' => 'Revision Requested'
            ];
            
            $statusName = $statusMap[$plr_final] ?? 'Unknown';
            
            return $repName . ' ' . $statusName;
        };
        
        $allLogs = [];
        
        // 1. GET CURRENT FILE STATUS (MOST RECENT)
        $latestFile = DB::table('filestab')
            ->where('doc_no', $doc_no)
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($latestFile) {
            // Check if it's a file upload or just status
            $hasFileUpload = !empty($latestFile->file_path);
            
            // Map rep_tag to meaningful name
            $repTagDisplay = '';
            if ($latestFile->rep_tag) {
                $repTagNames = [
                    'P/R' => 'Preliminary Report',
                    'F/R' => 'Final Report'
                ];
                $repTagDisplay = $repTagNames[$latestFile->rep_tag] ?? $latestFile->rep_tag;
            }
            
            // Determine activity type
            $activityType = 'Current Status';
            $route = 'Current Status';
            
            if ($hasFileUpload) {
                $activityType = 'Document Upload';
                $route = 'Document Upload';
            }
            
            // Get status text - THIS IS THE REAL CURRENT STATUS
            $statusText = $getStatusText($latestFile->plr_final, $latestFile->rep_tag);
            
            $currentStatusLog = [
                'id' => 'file_' . $latestFile->id,
                'type' => 'current_status',
                'uw_doc' => $doc_no,
                'curdatetime' => $latestFile->updated_at ?: $latestFile->created_at,
                'sender' => 'System',
                'receiver' => 'System',
                'cc_email' => null,
                'sub' => $activityType,
                'body' => $hasFileUpload 
                    ? ($latestFile->remarks ?: 'Document uploaded') . ' (Status: ' . $statusText . ')'
                    : 'Status updated to: ' . $statusText,
                'route' => $route,
                'rep_tag_display' => $repTagDisplay,
                'plr_final' => $latestFile->plr_final, // THIS IS THE REAL STATUS
                'remarks' => $latestFile->remarks,
                'source' => 'filestab',
                'is_current' => true,
                'sort_date' => $latestFile->updated_at ?: $latestFile->created_at
            ];
            
            // Add current status as FIRST entry
            $allLogs[] = $currentStatusLog;
        }
        
        // 2. GET ALL EMAIL LOGS (HISTORICAL RECORD)
        $emailLogs = EmailLog::where('uw_doc', $doc_no)
            ->orderBy('curdatetime', 'desc')
            ->get();
        
        foreach ($emailLogs as $email) {
            // Determine email type
            $subject = strtolower($email->sub);
            $body = strtolower($email->body ?? '');
            
            $emailType = 'Email';
            $route = 'Email';
            
            if (str_contains($subject, 'reminder') || str_contains($body, 'reminder')) {
                $emailType = 'Reminder';
                $route = 'Reminder Email';
            } elseif (str_contains($subject, 'revision') || str_contains($body, 'revision') || str_contains($subject, 'plr')) {
                $emailType = 'Revision Request';
                $route = 'PLR Revision';
            } elseif (str_contains($subject, 'approve') || str_contains($body, 'approve')) {
                $emailType = 'Approval Notice';
                $route = 'Approval Email';
            }
            
            // Try to extract report type from email body
            $repTagDisplay = '';
            if (str_contains($body, 'preliminary') || str_contains($subject, 'preliminary')) {
                $repTagDisplay = 'Preliminary Report';
            } elseif (str_contains($body, 'final') || str_contains($subject, 'final')) {
                $repTagDisplay = 'Final Report';
            }
            
            // **CRITICAL CHANGE HERE: Show FULL body without truncation**
            $allLogs[] = [
                'id' => 'email_' . $email->id,
                'type' => 'email',
                'uw_doc' => $doc_no,
                'curdatetime' => $email->curdatetime,
                'sender' => $email->sender,
                'receiver' => $email->receiver,
                'cc_email' => $email->email_cc ?? null,
                'sub' => $emailType,
                'body' => $email->sub . ': ' . $email->body, // REMOVED THE SUBSTRING TRUNCATION
                'route' => $route,
                'rep_tag_display' => $repTagDisplay,
                'plr_final' => null, // Don't show status for emails - they're just communication history
                'source' => 'email_logs',
                'is_current' => false,
                'sort_date' => $email->curdatetime
            ];
        }
        
        // 3. If no logs at all, create default
        if (empty($allLogs)) {
            $allLogs[] = [
                'id' => 'default_' . $doc_no,
                'type' => 'status',
                'uw_doc' => $doc_no,
                'curdatetime' => date('Y-m-d H:i:s'),
                'sender' => 'System',
                'receiver' => 'System',
                'cc_email' => null,
                'sub' => 'Document Status',
                'body' => 'Document has not been uploaded yet',
                'route' => 'Initial Status',
                'rep_tag_display' => '',
                'plr_final' => null,
                'source' => 'system',
                'is_current' => true,
                'sort_date' => date('Y-m-d H:i:s')
            ];
        }
        
        // Sort all logs by date (newest first)
        usort($allLogs, function($a, $b) {
            return strtotime($b['sort_date']) - strtotime($a['sort_date']);
        });
        
        \Log::info('Total logs found: ' . count($allLogs));
        
        return response()->json([
            'success' => true,
            'message' => 'Activity logs fetched successfully',
            'document_no' => $doc_no,
            'logs' => $allLogs,
            'count' => count($allLogs),
            'current_status' => $allLogs[0]['body'] ?? 'Unknown'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error in getLogsByDocument: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Error fetching logs: ' . $e->getMessage(),
            'logs' => []
        ], 500);
    }
}


}