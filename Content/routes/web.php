<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\SurveyReportController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\UserAuth;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\AutoLoginController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Login page 
Route::match(['get', 'post'], '/login', [UserController::class, 'login'])->name('login');
//Change Password Page Showing Now
Route::get('/changePassword', [UserController::class, 'changePassword'])
    ->name('change.password.form');

Route::post('/changePassword', [UserController::class, 'changePassword'])
    ->name('change.password');


Route::match(['get', 'post'], '/forgetPass', [PasswordController::class, 'forgetPassEmail']);
Route::match(['get', 'post'], '/forgetPass/{token}', [PasswordController::class, 'forgetPass'])->name('password.reset');
Route::match(['get', 'post'], '/resetPassword', [PasswordController::class, 'resetPassword']);

// Public routes (AJAX / file download)
Route::get('/documents/{document_no}/files', [DocumentController::class, 'getDocuments'])->name('documents.files');
Route::get('/documents/file/{filename}', [DocumentController::class, 'downloadFile'])->name('documents.download');
Route::get('/documents/{document_no}/download-zip', [DocumentController::class, 'downloadAllAsZip'])
         ->name('documents.downloadZip');



// Protected routes (Surveyor + Admin)
Route::group(['middleware' => ['web', UserAuth::class]], function(){

    // Logout
    Route::get('/logout', function () {
    Session::forget('user');
    if (session('status')) {
        return redirect('/login')->with('status', 'Password Change. Login with new credentials');
    } else {
        return redirect('/login');
    }
})->name('logout');

// Redirect /admin/logout to /logout
Route::get('/admin/logout', function () {
    return redirect()->route('logout');
});

    // Surveyor main page / home
    Route::match(['get', 'post'],'/', [MainController::class, 'main'])->name('home');
    

    // Show upload page
    Route::get('/upload-document/{document_no}', [DocumentController::class, 'showUploadForm'])
         ->name('upload.document.form');

    // Handle file upload POST
    Route::post('/upload-document/{document_no}', [DocumentController::class, 'upload'])
         ->name('upload.document');

    // Admin routes
    Route::get('/admin/files', [AdminController::class, 'showFiles'])->name('admin.files');
    Route::get('/admin/files/{id}/view', [AdminController::class, 'viewReport'])->name('admin.files.view');

    // Admin AJAX routes
    Route::get('/admin/get-files', [AdminController::class, 'getFiles'])->name('admin.getFiles');
    Route::get('/admin/get-stats', [AdminController::class, 'getStats'])->name('admin.getStats');
    Route::get('/admin/get-appointment-stats', [AdminController::class, 'getAppointmentStats'])->name('admin.getAppointmentStats');
    Route::get('/admin/get-surveyors', [AdminController::class, 'getSurveyors'])->name('admin.getSurveyors'); // NEW ROUTE
    // Survey report
    Route::get('/surveyReport', [SurveyReportController::class, 'index'])->name('survey.report');

    // Approve / Send revision
    // Route::post('/documents/{doc}/approve', [DocumentController::class, 'approvePlr'])->name('documents.approve');
    Route::match(['get', 'post'],'/documents/{doc}/approve', [DocumentController::class, 'approvePlr'])->name('documents.approve');

    Route::post('/documents/{docNo}/send-revision', [DocumentController::class, 'sendRevisionEmail']);

   // Surveyor Management Routes
    Route::get('/admin/add-surveyor', [AdminController::class, 'addSurveyor'])->name('admin.addSurveyor');
    Route::post('/admin/store-surveyor', [AdminController::class, 'storeSurveyor'])->name('admin.storeSurveyor');
    Route::post('/admin/update-surveyor/{id}', [AdminController::class, 'updateSurveyor'])->name('admin.updateSurveyor');
    // Route::post('/admin/delete-surveyor/{id}', [App\Http\Controllers\AdminController::class, 'deleteSurveyor'])->name('admin.deleteSurveyor');
    
});


Route::get('/reminder', [ReminderController::class, 'showForm'])->name('reminder.form');
Route::post('/reminder/send', [ReminderController::class, 'sendReminder'])->name('reminder.send');

Route::post('/admin/unapprove-plr', [AdminController::class, 'unapprovePlr'])->name('admin.unapprove-plr');

// // PLR Revision Email Route
// Route::post('/surveyor/documents/{docNo}/send-revision', [AdminController::class, 'sendRevisionEmail'])
//     ->name('admin.send-revision')
//     ->middleware(['auth', 'admin']);

// for testing only

Route:: get('/getdata',[ReminderController::class,'send']);
Route:: get('/gethistory/{uwDoc}',[ReminderController::class,'getReminderHistory']);
Route:: get('/debugAllLogs',[ReminderController::class,'debugAllLogs']);

//// test routes

// Route::get('/test-upload/{document_no}', [DocumentController::class, 'testshowUploadForm'])
//     ->name('test.upload.form');

// Route::post('/test-upload/{document_no}', [DocumentController::class, 'uploadtest'])
//     ->name('test.upload.submit');

Route::get('/check-session', function() {
    return session('user');
});
// Route::get('/resources/download-zip', function() {
//     $zip = new ZipArchive();
//     $zipFileName = 'Resources_' . date('Y-m-d') . '.zip';
//     $zipPath = storage_path('app/temp/' . $zipFileName);
    
//     if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
//         $zip->addFile(public_path('resources/pdfs/claims-guidelines.pdf'), 'Claims_Guidelines.pdf');
//         $zip->addFile(public_path('resources/pdfs/report-template.pdf'), 'Report_Template.pdf');
//         $zip->addFile(public_path('resources/pdfs/documentation-checklist.pdf'), 'Documentation_Checklist.pdf');
//         $zip->close();
//     }
    
//     return response()->download($zipPath)->deleteFileAfterSend(true);
// })->name('resources.download.zip');



Route::get('/resources/download-zip', [ResourceController::class, 'downloadResourcesZip'])
    ->name('resources.download.zip');

 Route::get('/resources/download/{filename}', [ResourceController::class, 'downloadSingle'])
    ->where('filename', '.*') // allow spaces and special chars
    ->name('resources.download.single');

Route::get('/reminder/logs', [ReminderController::class, 'debugAllLogs'])
    ->name('reminder.debugAllLogs');

Route::get('/email-logs/{doc_no}', [EmailController::class, 'getLogsByDocument'])
    ->name('email.logs.by.document');






Route::post('/admin/save-approval', [AdminController::class, 'saveApproval'])->name('admin.saveApproval');
Route::post('/admin/save-remarks', [AdminController::class, 'saveRemarks'])->name('admin.saveRemarks');

Route::get('/admin/get-approval-data', [AdminController::class, 'getApprovalData'])->name('admin.getApprovalData');




Route::post('/admin/mark-in-process', [AdminController::class, 'markAsInProcess'])->name('admin.mark-in-process');
    
Route::post('/admin/resend-welcome-email/{id}', [AdminController::class, 'resendWelcomeEmail'])->name('admin.surveyors.resend-email');

// // Make sure UserAuth is not applied to embedded routes
// Route::middleware(['web'])->group(function () {
//     Route::get('/embedded/files', [AdminController::class, 'showFiles'])
//         ->middleware('embedded.auth'); // Only this middleware, not UserAuth
// });









Route::get('/embedded/files', [AdminController::class, 'showFiles'])
    ->middleware(['embedded.auth', 'web']) // embedded.auth sets session
    ->withoutMiddleware(['UserAuth']); // Skip UserAuth entirely



// ============================================
// NEW PAGES - Add these routes
// ============================================

// Email Logs Page
Route::get('/email-logs', function () {
    return view('email-logs');
})->middleware(['web', UserAuth::class])->name('email.logs');

// Resources Page (changed path to avoid conflicts)
Route::get('/documents-library', function () {
    return view('resources');
})->middleware(['web', UserAuth::class])->name('resources.page');









Route::get('/email-logs/{doc_no}', [ReminderController::class, 'getLogsByDocument'])
    ->name('email.logs.by.document');













/*        TEST ROUTES BELOW */
























Route::get('/generate-test-url', function () {
    $ts = time();
    $secret = config('services.portal1.secret');
    $sig = hash_hmac('sha256', $ts, $secret);
    
    $url = url('/embedded/files') . '?' . http_build_query([
        'ts' => $ts,
        'sig' => $sig
    ]);
    
    return '<a href="' . $url . '" target="_blank">Test Link</a><br><br>' . $url;
});




Route::get('/check-middleware', function () {
    return response()->json([
        'middleware_registered' => class_exists(\App\Http\Middleware\VerifyEmbeddedSignature::class),
        'secret_configured' => !empty(config('services.portal1.secret')),
        'secret_length' => strlen(config('services.portal1.secret', '')),
        'route_exists' => true
    ]);
});





Route::get('/debug-services-config', function () {
    $configPath = config_path('services.php');
    $fileExists = file_exists($configPath);
    $fileContent = $fileExists ? file_get_contents($configPath) : 'File not found';
    
    // Check if portal1 key exists in the actual array
    $servicesConfig = require $configPath;
    $hasPortal1 = isset($servicesConfig['portal1']);
    
    return response()->json([
        'config_path' => $configPath,
        'file_exists' => $fileExists,
        'file_size' => $fileExists ? filesize($configPath) : 0,
        'has_portal1_key' => $hasPortal1,
        'portal1_config' => $hasPortal1 ? $servicesConfig['portal1'] : 'NOT FOUND',
        'full_config_keys' => array_keys($servicesConfig),
        'file_snippet' => substr($fileContent, 0, 2000) // First 2000 chars
    ]);
});












// Cache management routes
Route::get('/claims/cache-check', [MainController::class, 'cacheCheck']);
Route::get('/claims/cache-refresh', [MainController::class, 'forceRefreshCache']);
Route::get('/claims/cache-clear', [MainController::class, 'clearCache']);
Route::get('/claims/cache-stats', [MainController::class, 'cacheStats']);
Route::get('/claims/cache-list-users', [MainController::class, 'listAllCachedUsers'])->middleware('admin'); // Optional admin route




// Test route for PLR approval
Route::get('test-plr-approval', [DocumentController::class, 'testPlrApproval'])->name('test.plr.approval');

// Test route to see all documents and their PLR status
// Test route for PLR approval
Route::get('test-plr-approval', [DocumentController::class, 'testPlrApproval'])->name('test.plr.approval');

// Test route to see all documents and their PLR status
Route::get('test-plr-data', function() {
    $documents = \App\Models\FileTab::select('doc_no', 'plr_final', 'updated_by', 'updated_at', 'created_at')
                                  ->orderBy('doc_no')
                                  ->get()
                                  ->groupBy('doc_no');
    
    return response()->json([
        'count' => $documents->count(),
        'documents' => $documents,
        'summary' => [
            'total_documents' => \App\Models\FileTab::count(),
            'approved_plr' => \App\Models\FileTab::where('plr_final', 'Y')->count(),
            'pending_plr' => \App\Models\FileTab::where('plr_final', '!=', 'Y')->orWhereNull('plr_final')->count(),
            'with_updated_by' => \App\Models\FileTab::whereNotNull('updated_by')->count(),
            'without_updated_by' => \App\Models\FileTab::whereNull('updated_by')->count()
        ]
    ]);
});

// Route::get('test-approve/{docNo}', function($docNo) {
//     // Simulate approval process
//     try {
//         $userName = Auth::user()->name ?? 'System';
//         $file = \App\Models\FileTab::where('doc_no', $docNo)
//                                    ->orderByDesc('id')
//                                    ->first();
        
//         if (!$file) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Document not found',
//                 'tested_doc_no' => $docNo
//             ]);
//         }
        
//         // Show before state
//         $before = [
//             'plr_final' => $file->plr_final,
//             'updated_by' => $file->updated_by,
//             'updated_at' => $file->updated_at
//         ];
        
//         // Update
//         $file->plr_final = 'Y';
//         $file->updated_by = $userName;
//         $file->updated_at = now();
//         $file->save();
        
//         // Show after state
//         $after = [
//             'plr_final' => $file->plr_final,
//             'updated_by' => $file->updated_by,
//             'updated_at' => $file->updated_at
//         ];
        
//         return response()->json([
//             'success' => true,
//             'message' => 'Test approval completed',
//             'auth_info' => [
//                 'user_name' => $userName,
//                 'is_logged_in' => Auth::check(),
//                 'user_id' => Auth::id()
//             ],
//             'document' => [
//                 'doc_no' => $docNo,
//                 'file_id' => $file->id,
//                 'before' => $before,
//                 'after' => $after
//             ]
//         ]);
        
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => $e->getMessage(),
//             'trace' => $e->getTraceAsString()
//         ]);
//     }
// });


Route::get('/email_logs', function () {
    // Get all column names
    $columns = Schema::getColumnListing('users');

    // Optionally, get first 5 records
    $records = DB::table('users')->get();

    // Return as JSON for easy viewing
    return response()->json([
        'columns' => $columns,
        'records' => $records,
    ]);
});






Route::get('/auto-login', [AutoLoginController::class, 'login']);


















// tets



Route::get('/debug/users-table', function() {
    try {
        // Check users table structure
        $columns = DB::select("DESCRIBE users");
        
        // Get all users with role information
        $users = DB::table('users')
                  ->select('*')
                  ->limit(20)
                  ->get();
        
        // Check for surveyor role users
        $surveyors = DB::table('users')
                      ->where('role', 'surveyor')
                      ->orWhere('role', 'LIKE', '%surveyor%')
                      ->orWhere('name', 'LIKE', '%surveyor%')
                      ->get();
        
        return response()->json([
            'users_table_columns' => $columns,
            'all_users_sample' => $users,
            'surveyor_users' => $surveyors,
            'total_users' => DB::table('users')->count(),
            'total_surveyors' => $surveyors->count()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});


// routes/api.php
Route::get('/surveyor-emails', function() {
    $emails = \App\Models\User::select('email')
        ->where('role', 'Surveyor')
        ->get();
    
    return response()->json([
        'status' => 'success',
        'count' => $emails->count(),
        'emails' => $emails->pluck('email')
    ]);
});
Route::get('/test-final-response', [AdminController::class, 'testFinalResponse']);





// In routes/web.php or a test controller
Route::get('/debug-doc-status/{docNo}', function($docNo) {
    $files = \App\Models\FileTab::where('doc_no', $docNo)
        ->orderBy('created_at', 'desc')
        ->get();
    
    return response()->json([
        'doc_no' => $docNo,
        'records' => $files->map(function($file) {
            return [
                'id' => $file->id,
                'plr_final' => $file->plr_final,
                'rep_tag' => $file->rep_tag,
                'created_at' => $file->created_at,
                'estimate_amount' => $file->estimate_amount,
            ];
        }),
        'has_p_r' => $files->where('rep_tag', 'P/R')->isNotEmpty(),
        'has_f_r' => $files->where('rep_tag', 'F/R')->isNotEmpty(),
        'has_approved_plr' => $files->where('rep_tag', 'P/R')->where('plr_final', 'Y')->isNotEmpty(),
    ]);
});


Route::get('/check-filetab', function () {
    // Get all column names
    $columns = Schema::getColumnListing('filestab');

    // Optionally, get first 5 records
    $records = DB::table('filestab')->get();

    // Return as JSON for easy viewing
    return response()->json([
        'columns' => $columns,
        'records' => $records,
    ]);
});



Route::get('/check-email_logs', function () {
    // Get all column names
    $columns = Schema::getColumnListing('email_logs');

    // Optionally, get first 5 records
    $records = DB::table('email_logs')->get();

    // Return as JSON for easy viewing
    return response()->json([
        'columns' => $columns,
        'records' => $records,
    ]);
});


Route::get('/claims_docs', function () {
    // Get all column names
    $columns = Schema::getColumnListing('claim_docs');

    // Optionally, get first 5 records
    $records = DB::table('claim_docs')->get();

    // Return as JSON for easy viewing
    return response()->json([
        'columns' => $columns,
        'records' => $records,
    ]);
});





Route::get('/test-email-logs/{doc_no}', function($doc_no) {
    $logs = EmailLog::where('uw_doc', $doc_no)->get();
    return response()->json([
        'test' => true,
        'doc_no' => $doc_no,
        'logs_count' => $logs->count(),
        'logs' => $logs->toArray(),
        'first_log' => $logs->first()
    ]);
});
Route::get('/check-users', function () {
    // Get all column names
    $columns = Schema::getColumnListing('users');

    // Optionally, get first 5 records
    $records = DB::table('users')->get();

    // Return as JSON for easy viewing
    return response()->json([
        'columns' => $columns,
        'records' => $records,
    ]);
});

