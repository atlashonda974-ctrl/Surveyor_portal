<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class ResourceController extends Controller
{

    
    public function downloadResourcesZip()
    {
        $zip = new ZipArchive();
        $zipFileName = 'Resources_' . date('Y-m-d_His') . '.zip';
        $tempDir = storage_path('app/temp');
        $zipPath = $tempDir . '/' . $zipFileName;

        // Create temp directory if it doesn't exist
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {

            $pdfFiles = [
                [
                    'path' => public_path('resources/pdfs/Approval Note for disposal of Slavage 2025.pdf'),
                    'name' => 'Approval_Note_for_disposal_of_Salvage_2025.pdf'
                ],
                [
                    'path' => public_path('resources/pdfs/CLAIM FORM MOTOR (CONVENTIONAL).pdf'),
                    'name' => 'Claim_Form_Motor_Conventional.pdf'
                ],
                [
                    'path' => public_path('resources/pdfs/SATISFACTION NOTE MOTOR.pdf'),
                    'name' => 'Satisfaction_Note_Motor.pdf'
                ]
            ];

            foreach ($pdfFiles as $file) {
                if (File::exists($file['path'])) {
                    $zip->addFile($file['path'], $file['name']);
                }
            }

            $zip->close();
        } else {
            return response()->json(['error' => 'Unable to create ZIP file'], 500);
        }

        if (File::exists($zipPath)) {
            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        return response()->json(['error' => 'ZIP file not found'], 500);
    }


   public function downloadSingle($filename)
{
    // Decode any URL-encoded characters (e.g. spaces)
    $filename = urldecode($filename);

    $filePath = public_path('resources/pdfs/' . $filename);

    if (!File::exists($filePath)) {
        return response()->json(['error' => 'File not found: ' . $filename], 404);
    }

    return response()->download($filePath);
}


}
