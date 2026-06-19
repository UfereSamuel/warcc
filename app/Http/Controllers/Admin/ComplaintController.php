<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ComplaintController extends Controller
{
    /**
     * Display a listing of complaints
     */
    public function index(Request $request)
    {
        $query = Complaint::with(['staff', 'categoryRelation'])->orderBy('created_at', 'desc');

        // Filter by review status
        if ($request->filled('reviewed')) {
            if ($request->reviewed == '1') {
                $query->reviewed();
            } elseif ($request->reviewed == '0') {
                $query->unreviewed();
            }
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by anonymous status
        if ($request->filled('anonymous')) {
            if ($request->anonymous == '1') {
                $query->where('is_anonymous', true);
            } elseif ($request->anonymous == '0') {
                $query->where('is_anonymous', false);
            }
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $complaints = $query->paginate(20);
        $categories = Complaint::getAllCategories();

        // Statistics
        $stats = [
            'total' => Complaint::count(),
            'unreviewed' => Complaint::unreviewed()->count(),
            'reviewed' => Complaint::reviewed()->count(),
            'anonymous' => Complaint::where('is_anonymous', true)->count(),
        ];

        return view('admin.complaints.index', compact('complaints', 'stats', 'categories'));
    }

    /**
     * Display the specified complaint
     */
    public function show($id)
    {
        $complaint = Complaint::with(['staff', 'categoryRelation'])->findOrFail($id);
        $categories = Complaint::getAllCategories();

        return view('admin.complaints.show', compact('complaint', 'categories'));
    }

    /**
     * Toggle review status
     */
    public function toggleReview($id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->is_reviewed = !$complaint->is_reviewed;
        $complaint->save();

        return back()->with('success', 'Complaint review status updated.');
    }

    /**
     * Update admin notes
     */
    public function updateNotes(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);
        
        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:5000',
        ]);

        $complaint->admin_notes = $validated['admin_notes'];
        $complaint->save();

        return back()->with('success', 'Admin notes updated successfully.');
    }

    /**
     * Update complaint category
     */
    public function updateCategory(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        $validated = $request->validate([
            'category' => ['required', Rule::in(ComplaintCategory::allSlugs())],
        ]);

        $complaint->category = $validated['category'];
        $complaint->save();

        return back()->with('success', 'Complaint category updated successfully.');
    }

    /**
     * Delete a complaint
     */
    public function destroy($id)
    {
        $complaint = Complaint::findOrFail($id);
        
        // Delete evidence file if exists
        if ($complaint->evidence_path) {
            \Storage::disk('public')->delete($complaint->evidence_path);
        }
        
        $complaint->delete();

        return redirect()->route('admin.complaints.index')
            ->with('success', 'Complaint deleted successfully.');
    }

    /**
     * Download single complaint as PDF
     */
    public function downloadPdf($id)
    {
        $complaint = Complaint::with(['staff', 'categoryRelation'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.complaints.pdf', compact('complaint'));
        
        return $pdf->download('complaint-' . $complaint->complaint_number . '.pdf');
    }

    /**
     * Download multiple complaints as PDF (filtered)
     */
    public function downloadBulkPdf(Request $request)
    {
        $query = Complaint::with(['staff', 'categoryRelation'])->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('reviewed')) {
            if ($request->reviewed == '1') {
                $query->reviewed();
            } elseif ($request->reviewed == '0') {
                $query->unreviewed();
            }
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('anonymous')) {
            if ($request->anonymous == '1') {
                $query->where('is_anonymous', true);
            } elseif ($request->anonymous == '0') {
                $query->where('is_anonymous', false);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $complaints = $query->get();

        if ($complaints->isEmpty()) {
            return back()->with('error', 'No complaints found for the selected filters.');
        }

        $pdf = Pdf::loadView('admin.complaints.bulk-pdf', compact('complaints'));
        
        $filename = 'complaints-' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Toggle complaints system on/off
     */
    public function toggleSystem(Request $request)
    {
        $systemSettings = DB::table('system_settings')->first();
        
        if ($systemSettings) {
            DB::table('system_settings')
                ->where('id', $systemSettings->id)
                ->update([
                    'complaints_system_enabled' => !$systemSettings->complaints_system_enabled
                ]);
        } else {
            DB::table('system_settings')->insert([
                'complaints_system_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Complaints system status updated.');
    }

    /**
     * Display complaint categories management
     */
    public function categoriesIndex()
    {
        $categories = ComplaintCategory::orderBy('sort_order')->get();
        return view('admin.complaints.categories', compact('categories'));
    }

    /**
     * Store a new complaint category
     */
    public function categoriesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:complaint_categories,name',
        ]);

        $maxSortOrder = ComplaintCategory::max('sort_order') ?? 0;

        ComplaintCategory::create([
            'name' => $validated['name'],
            'slug' => ComplaintCategory::uniqueSlug($validated['name']),
            'is_active' => true,
            'sort_order' => $maxSortOrder + 1,
        ]);

        return back()->with('success', 'Category added successfully.');
    }

    /**
     * Update a complaint category
     */
    public function categoriesUpdate(Request $request, ComplaintCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:complaint_categories,name,' . $category->id,
            'sort_order' => 'required|integer|min:0',
        ]);

        $oldSlug = $category->slug;
        $newSlug = $category->name !== $validated['name']
            ? ComplaintCategory::uniqueSlug($validated['name'], $category->id)
            : $category->slug;

        $category->name = $validated['name'];
        $category->slug = $newSlug;
        $category->sort_order = $validated['sort_order'];
        $category->save();

        if ($oldSlug !== $newSlug) {
            Complaint::where('category', $oldSlug)->update(['category' => $newSlug]);
        }

        $message = $oldSlug !== $newSlug
            ? 'Category updated and linked complaints were synced to the new slug.'
            : 'Category updated successfully.';

        return back()->with('success', $message);
    }

    /**
     * Toggle category active status
     */
    public function categoriesToggle($id)
    {
        $category = ComplaintCategory::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        return back()->with('success', 'Category status updated.');
    }

    /**
     * Delete a complaint category
     */
    public function categoriesDestroy($id)
    {
        $category = ComplaintCategory::findOrFail($id);
        
        // Check if category has complaints
        $complaintsCount = Complaint::where('category', $category->slug)->count();
        
        if ($complaintsCount > 0) {
            return back()->with('error', 'Cannot delete category with existing complaints. Please reassign or delete those complaints first.');
        }

        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }
}
