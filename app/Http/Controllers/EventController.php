<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
 
class EventController extends Controller
{
    public function index()
    {
        $events = Event::where('created_by', Auth::id())
            ->latest()
            ->paginate(10);
        return view('events.index', compact('events'));
    }
 
    public function create()
    {
        return view('events.create');
    }
 
    public function store(Request $request)
    {
    // Debug: show all request data
    Log::debug('Event store request data', $request->all());
 
        try {
            $request->validate([
                'description' => 'required|string',
                'images' => 'nullable|array',
                'images.*' => 'nullable|mimes:jpeg,png,jpg,gif,webp,mp4,avi,mov,wmv|max:1048576', // 1GB max
            ]);
            Log::debug('Validation passed');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        }
 
        $event = new Event();
        $event->description = $request->description;
        $event->created_by = Auth::id();
        $event->save();
        Log::debug('Event saved', ['event_id' => $event->id]);
 
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $media) {
                $path = $media->store('events', 'public');
                $mime = $media->getMimeType();
                $type = str_starts_with($mime, 'video/') ? 'video' : 'image';
                $event->images()->create([
                    'image_path' => $path,
                    'type' => $type,
                ]);
            }
            Log::debug('Media saved', ['media' => $event->images()->get()]);
        }
 
        return redirect()->route('events.index')->with('success', 'Event posted!');
    }
 
    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }
 
    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }
 
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'description' => 'required|string',
            'images.*' => 'nullable|mimes:jpeg,png,jpg,gif,webp,mp4,avi,mov,wmv|max:1048576',
        ]);
 
        $event->description = $request->description;
        $event->save();
 
        // Handle media removal
        $keepMedia = collect(json_decode($request->input('keep_media', '[]'), true));
        $allMedia = $event->images()->get();
        foreach ($allMedia as $media) {
            if (!$keepMedia->contains($media->id)) {
                // Delete file from storage
                Storage::disk('public')->delete($media->image_path);
                $media->delete();
            }
        }
 
        // Handle new uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $media) {
                $path = $media->store('events', 'public');
                $mime = $media->getMimeType();
                $type = str_starts_with($mime, 'video/') ? 'video' : 'image';
                $event->images()->create([
                    'image_path' => $path,
                    'type' => $type,
                ]);
            }
        }
 
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('events.index')->with('success', 'Event updated!');
    }
 
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted!');
    }
}