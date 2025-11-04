<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Products;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Validator;
class ServiceController extends Controller
{
    /**
     * Restore a soft-deleted service.
     */
    public function restore($id)
    {
        $service = Service::onlyTrashed()
            ->where('admin_id', Auth::id())
            ->findOrFail($id);
        $service->restore();
        return redirect()->route('services.archived.index')->with('message', 'Service restored successfully.');
    }
    public function index(Request $request)
    {
        $query = Service::where('admin_id', Auth::id());

        if ($request->filled('search')) {
            $query->where('service_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('unit')) {
            $query->where('unit', $request->unit);
        }

    $services = $query->whereNull('deleted_at')->paginate(5);
        $units = Service::select('unit')->distinct()->pluck('unit');

        return view('admin.services.index', compact('services', 'units'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

 
public function store(Request $request)
{
    $request->validate([
        'service_name.*'    => 'required|string|max:255',
        'unit.*'            => 'required|string|max:50',
        'unit_custom.*'     => 'nullable|string|max:50',
        'price.*'           => 'required|numeric|min:0',
        'start_time.*'      => 'nullable|date_format:H:i',
        'duration_value.*'  => 'nullable|numeric',
        'duration_unit.*'   => 'nullable|string|max:20',
        'description.*'     => 'required|string',
        'images.*'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $adminId = Auth::id();


        foreach ($request->service_name as $index => $name) {
        $start = $request->start_time[$index] ?? null;
        $end = $request->end_time[$index] ?? null;

        if ($start && $end && strtotime($start) >= strtotime($end)) {
            return back()->withErrors([
                "end_time.$index" => "End time must be after start time for service #".($index + 1),
            ])->withInput();
        }

        // Handle only one image per row
        $imagePath = null;
        if ($request->hasFile('images') && isset($request->file('images')[$index])) {
            $imgFile = $request->file('images')[$index];
            if ($imgFile) {
                $imagePath = $imgFile->store('services', 'public');
            }
        }

        // Combine duration fields
        $durationValue = $request->duration_value[$index] ?? '';
        $durationUnit = $request->duration_unit[$index] ?? '';
        $duration = $durationValue && $durationUnit ? $durationValue . ' ' . $durationUnit : null;

        // Determine final unit: prefer unit_custom if provided, otherwise use selected unit
        $selectedUnit = $request->unit[$index] ?? null;
        $customUnits = $request->input('unit_custom', []);
        $customUnit = isset($customUnits[$index]) ? trim($customUnits[$index]) : null;
        $finalUnit = (!empty($customUnit)) ? $customUnit : $selectedUnit;

        Service::create([
            'admin_id'        => $adminId,
            'service_name'    => $name,
            'unit'            => $finalUnit,
            'price'           => $request->price[$index],
            'start_time'      => $start,
            'duration'        => $duration,
            'description'     => $request->description[$index],
            'images'          => $imagePath,
        ]);
    }

    return redirect()->back()->with('success', 'Services successfully added.');
}

    public function show($id)
    {
        $service = Service::findOrFail($id);
        return view('admin.services.show', compact('service'));
    }

    
   
public function edit($id)
{
  $service = Service::where('id', $id)
    ->where('admin_id', Auth::id())
    ->firstOrFail();
return response()->json($service);
}


public function update(Request $request, $id)
{
    $service = Service::where('id', $id)
        ->where('admin_id', Auth::id())
        ->firstOrFail();


    $request->validate([
        'service_name'    => 'required|string|max:255',
        'unit'            => 'required|string|max:50',
        'custom_unit'     => 'nullable|string|max:50',
        'price'           => 'required|numeric|min:0',
        'start_time'      => 'nullable|date_format:H:i',
        'duration_value'  => 'nullable|numeric',
        'duration_unit'   => 'nullable|string|max:20',
        'description'     => 'required|string',
        'images'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    // Build duration
    $duration = ($request->duration_value && $request->duration_unit)
        ? $request->duration_value . ' ' . $request->duration_unit
        : null;

    // Handle image upload if a new image is provided
    $imagePath = $service->images;
    if ($request->hasFile('images')) {
        // Delete old image if exists
        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }
        $imagePath = $request->file('images')->store('services', 'public');
    }

    // Prefer custom_unit when provided (and non-empty), otherwise use the selected unit
    $finalUnit = $request->custom_unit && trim($request->custom_unit) !== '' ? trim($request->custom_unit) : $request->unit;

    $service->update([
        'service_name'    => $request->service_name,
        'unit'            => $finalUnit,
        'price'           => $request->price,
        'start_time'      => $request->start_time,
        'duration'        => $duration,
        'description'     => $request->description,
        'images'          => $imagePath,
    ]);

    // Log activity
    ActivityLog::create([
        'user_admin' => Auth::user()->name ?? 'Unknown',
        'action' => 'Updated Service',
        'details' => 'Service #' . $service->id . ' updated',
        'timestamp' => now(),
    ]);

    return redirect()->route('services.index')->with('message', 'Service updated successfully');
}

    public function destroy($id)
    {
         $service = Service::where('id', $id)
                      ->where('admin_id', Auth::id())
                      ->firstOrFail();
        if ($service->images) {
            Storage::disk('public')->delete($service->images);
        }
        $service->delete();

        return redirect()->route('services.index')->with('success', 'Service deleted (soft) successfully.');
    }

    // public function archivedIndex()
    // {
    //     $archived = Service::onlyTrashed()
    //     ->where('admin_id', Auth::id())
    //     ->get();        
    //     return view('admin.services.archived', compact('archived'));
    // }
     public function bulkArchive(Request $request)
    {
        $ids = $request->input('selected_services', []);
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'No services selected.');
        }
        Service::whereIn('id', $ids)
            ->where('admin_id', Auth::id())
            ->delete(); // Soft delete

        // Log activity for each archived service
        foreach ($ids as $id) {
            \App\Models\ActivityLog::create([
                'user_admin' => Auth::user()->name ?? 'Unknown',
                'action' => 'Archived Service',
                'details' => 'Service #' . $id . ' archived',
                'timestamp' => now(),
            ]);
        }
        return redirect()->route('services.index')->with('success', 'Selected services archived successfully.');
    }

    public function forceDelete($id)
    {
 $service = Service::onlyTrashed()
                      ->where('id', $id)
                      ->where('admin_id', Auth::id())
                      ->firstOrFail();        if ($service->images) {
            Storage::disk('public')->delete($service->images);
        }
        $service->forceDelete();

        return redirect()->route('services.archived.index')->with('success', 'Service permanently deleted.');
    }

    public function inventory()
    {
    $services = Service::where('admin_id', Auth::id())->get();
        return view('admin.services.inventory', compact('services'));
    }
  
  //FOr Customer
    // In your controller, e.g., UserServiceController.php

public function index1(Request $request)
{
    $search = $request->input('search');
    $category = $request->input('category');

    // Assign $category to a variable before using in the query builder
    $selectedCategory = $category;

    $services = Service::query()
        ->when($search, function ($query, $search) {
            return $query->where(function($q) use ($search) {
                $q->where('service_name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        })
        ->when($selectedCategory && $selectedCategory !== 'All', function ($query) use ($selectedCategory) {
            // category filter removed; keep query unchanged
            return $query;
        })
        ->with(['admin.profile'])
        ->latest()
        ->paginate(12);

    return view('user.services.index', compact('services', 'category'));
}

public function show1($id)
{
    $service = Service::with(['admin', 'bookings'])->findOrFail($id);
    // Compute nextAvailableDate (same logic as UserServiceController)
    $lastApprovedBooking = $service->bookings()
        ->where('status', 'ongoing')
        ->orderByDesc('booking_start')
        ->first();
    if ($lastApprovedBooking) {
        $duration = is_numeric($service->duration) ? intval($service->duration) : 1;
        $nextAvailable = \Carbon\Carbon::parse($lastApprovedBooking->booking_start)
            ->addDays($duration)
            ->addDays(3);
        if ($lastApprovedBooking->booking_end && $lastApprovedBooking->booking_end > $lastApprovedBooking->booking_start) {
            $nextAvailable = \Carbon\Carbon::parse($lastApprovedBooking->booking_end)->addDays(3);
        }
        $nextAvailableDate = $nextAvailable->toDateString();
    } else {
        $nextAvailableDate = now()->addDays(2)->toDateString();
    }
    return view('user.services.show', compact('service', 'nextAvailableDate'));
}


}
