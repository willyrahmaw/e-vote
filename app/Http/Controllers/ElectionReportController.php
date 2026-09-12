<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Services\Reports\ElectionReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ElectionReportController extends Controller
{
    public function show(Election $election, ElectionReportService $reportService): View
    {
        $reportData = $reportService->generateOfficialReturn($election);

        return view('reports.official-election-return', $reportData);
    }
}
