<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Quota;
use Illuminate\Http\Request;

class QuotaController extends Controller
{
    public function index()
    {
        $quotas = Quota::with('lecturer')  // Eager load lecturer relationship
                       ->latest()
                       ->paginate(10);     // Show 10 entries per page

        return view('coordinator.quotas.index', compact('quotas'));
    }
}