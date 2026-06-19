<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ComplaintController extends Controller
{
    /**
     * Show the public complaint submission form
     */
    public function create()
    {
        // Check if complaints system is enabled
        $systemSettings = DB::table('system_settings')->first();
        
        if (!$systemSettings || !$systemSettings->complaints_system_enabled) {
            abort(404, 'Complaints system is currently unavailable.');
        }

        $categories = Complaint::getCategories();

        if (empty($categories)) {
            abort(503, 'Complaint categories are not configured.');
        }

        return view('complaints.create', compact('categories'));
    }

    /**
     * Store a new complaint
     */
    public function store(Request $request)
    {
        // Check if complaints system is enabled
        $systemSettings = DB::table('system_settings')->first();
        
        if (!$systemSettings || !$systemSettings->complaints_system_enabled) {
            return back()->with('error', 'Complaints system is currently unavailable.');
        }

        $activeSlugs = \App\Models\ComplaintCategory::activeSlugs();

        if (empty($activeSlugs)) {
            return back()->with('error', 'No complaint categories are currently available.');
        }

        $validated = $request->validate([
            'category' => ['required', Rule::in($activeSlugs)],
            'description' => 'required|string|min:20|max:5000',
            'suggested_solution' => 'nullable|string|max:2000',
            'is_anonymous' => 'required|boolean',
            'complainant_name' => 'required_if:is_anonymous,0|nullable|string|max:255',
            'complainant_email' => 'required_if:is_anonymous,0|nullable|email|max:255',
            'complainant_phone' => 'nullable|string|max:20',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB max
        ]);

        // Handle file upload
        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('complaints/evidence', 'public');
        }

        // Create complaint
        $complaint = Complaint::create([
            'complaint_number' => Complaint::generateComplaintNumber(),
            'category' => $validated['category'],
            'description' => $validated['description'],
            'suggested_solution' => $validated['suggested_solution'] ?? null,
            'is_anonymous' => $validated['is_anonymous'],
            'complainant_name' => $validated['is_anonymous'] ? null : $validated['complainant_name'],
            'complainant_email' => $validated['is_anonymous'] ? null : $validated['complainant_email'],
            'complainant_phone' => $validated['is_anonymous'] ? null : $validated['complainant_phone'],
            'evidence_path' => $evidencePath,
            'staff_id' => auth('staff')->check() ? auth('staff')->id() : null,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('complaints.success', $complaint->id)
            ->with('success', 'Your complaint has been submitted successfully.');
    }

    /**
     * Show success page after submission
     */
    public function success($id)
    {
        $complaint = Complaint::findOrFail($id);
        return view('complaints.success', compact('complaint'));
    }
}
