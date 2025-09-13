<?php

namespace App\Http\Controllers;

use App\Models\Syllabus;
use App\Services\SyllabusPdfService;
use Illuminate\Http\Response;

class SyllabusPdfController extends Controller
{
    /**
     * View PDF in browser
     */
    public function view(Syllabus $syllabus): Response
    {
        // Simple permission check - user must be authenticated
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        try {
            $pdfService = new SyllabusPdfService();
            return $pdfService->streamPdf($syllabus);
        } catch (\Exception $e) {
            abort(500, 'Error generating PDF: ' . $e->getMessage());
        }
    }

    /**
     * Download PDF
     */
    public function download(Syllabus $syllabus): Response
    {
        // Simple permission check - user must be authenticated
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        try {
            $pdfService = new SyllabusPdfService();
            return $pdfService->downloadPdf($syllabus);
        } catch (\Exception $e) {
            abort(500, 'Error generating PDF: ' . $e->getMessage());
        }
    }
}
