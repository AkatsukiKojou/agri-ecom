@extends('admin.layout')
@section('content')
<div class="max-w-5xl mx-auto py-10">
    <div class="flex justify-between items-center mb-10">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-green-400 to-lime-400 shadow-lg">
                <i class="bi bi-calendar2-week-fill text-2xl text-white"></i>
            </span>
            <h1 class="text-4xl font-extrabold text-green-900 tracking-tight">Events</h1>
        </div>
        <button onclick="openEventForm()" class="flex items-center gap-2 bg-gradient-to-r from-green-500 to-lime-500 text-white px-6 py-3 rounded-2xl shadow-lg hover:from-green-600 hover:to-lime-600 font-bold text-lg transition-all duration-200">
            <i class="bi bi-plus-circle-fill text-2xl"></i> New Event
        </button>
    </div>
    <!-- Modal -->
    <div id="eventFormModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl p-10 relative border-4 border-lime-200 animate-fade-in">
            <button onclick="closeEventForm()" class="absolute top-4 right-6 text-gray-400 hover:text-red-600 text-4xl font-bold">&times;</button>
            <h2 class="text-2xl font-bold text-green-800 mb-6 flex items-center gap-2"><i class="bi bi-pencil-square"></i> Create Event</h2>
            <form id="eventForm" action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-6">
                    <label class="block mb-2 font-semibold text-green-700">Description</label>
                    <textarea name="description" class="w-full border-2 border-lime-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-lime-400 focus:outline-none text-lg" rows="3" required>{{ old('description') }}</textarea>
                    @error('description')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="mb-8">
                    <label class="block mb-2 font-semibold text-green-700">Media (optional, you can select multiple images/videos)</label>
                    <button type="button" id="addPhotoBtn" class="flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-green-500 to-lime-500 text-white rounded-xl font-semibold shadow hover:from-green-600 hover:to-lime-600 transition mb-2">
                        <i class="bi bi-plus-circle"></i> Add Media
                    </button>
                    <input type="file" name="images[]" id="eventImagesInput" class="hidden" multiple accept="image/*,video/*">
                    @error('images')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    @error('images.*')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    <div id="imagePreviewContainer" class="flex flex-wrap gap-4 mt-4"></div>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeEventForm()" class="px-5 py-2 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100">Cancel</button>
                    <button type="submit" class="px-7 py-2 rounded-xl bg-gradient-to-r from-green-500 to-lime-500 text-white font-bold shadow hover:from-green-600 hover:to-lime-600 transition">Create</button>
                </div>
            </form>
        </div>
    </div>
 
    <!-- Edit Event Modal (single instance, outside loop) -->
    <div id="editEventModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl p-10 relative border-4 border-yellow-200 animate-fade-in">
            <button onclick="closeEditEventModal()" class="absolute top-4 right-6 text-gray-400 hover:text-red-600 text-4xl font-bold">&times;</button>
            <h2 class="text-2xl font-bold text-yellow-800 mb-6 flex items-center gap-2"><i class="bi bi-pencil-square"></i> Edit Event</h2>
            <form id="editEventForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-6">
                    <label class="block mb-2 font-semibold text-yellow-700">Description</label>
                    <textarea name="description" id="editEventDescription" class="w-full border-2 border-yellow-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-yellow-400 focus:outline-none text-lg" rows="3" required></textarea>
                    <div id="editEventError" class="text-red-600 text-sm mt-1"></div>
                </div>
                <div class="mb-6">
                    <label class="block mb-2 font-semibold text-yellow-700">Current Media</label>
                    <div id="editEventMediaPreview" class="flex flex-wrap gap-4"></div>
                    <div class="text-xs text-gray-500 mt-1">Click the trash icon to remove media.</div>
                </div>
                <div class="mb-8">
                    <label class="block mb-2 font-semibold text-yellow-700">Add New Media (optional)</label>
                    <input type="file" name="images[]" id="editEventImagesInput" class="block w-full text-sm text-gray-700 border border-yellow-200 rounded-lg" multiple accept="image/*,video/*">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeEditEventModal()" class="px-5 py-2 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100">Cancel</button>
                    <button type="submit" class="px-7 py-2 rounded-xl bg-gradient-to-r from-yellow-500 to-yellow-400 text-white font-bold shadow hover:from-yellow-600 hover:to-yellow-500 transition">Update</button>
                </div>
            </form>
        </div>
    </div>
    </div>
    <script>
    function openEventForm() {
        document.getElementById('eventFormModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeEventForm() {
        document.getElementById('eventFormModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('eventForm');
        const imagesInput = document.getElementById('eventImagesInput');
        const previewContainer = document.getElementById('imagePreviewContainer');
        const addPhotoBtn = document.getElementById('addPhotoBtn');
        // Use DataTransfer to accumulate files
        let dt = new DataTransfer();
        if(addPhotoBtn && imagesInput) {
            addPhotoBtn.addEventListener('click', function() {
                imagesInput.click();
            });
        }
        if(imagesInput && previewContainer) {
            imagesInput.addEventListener('change', function(e) {
                // Add new files to DataTransfer
                Array.from(imagesInput.files).forEach(file => {
                    dt.items.add(file);
                });
                // Update the input's files
                imagesInput.files = dt.files;
                // Render all previews
                previewContainer.innerHTML = '';
                Array.from(dt.files).forEach(file => {
                    if(file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(ev) {
                            const img = document.createElement('img');
                            img.src = ev.target.result;
                            img.className = 'h-24 w-24 object-cover rounded-lg border border-lime-200 shadow';
                            previewContainer.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    } else if(file.type.startsWith('video/')) {
                        const reader = new FileReader();
                        reader.onload = function(ev) {
                            const video = document.createElement('video');
                            video.src = ev.target.result;
                            video.className = 'h-24 w-24 object-cover rounded-lg border border-lime-200 shadow';
                            video.controls = true;
                            previewContainer.appendChild(video);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
        }
        if(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(form);
                fetch(form.action, {
                    method: form.method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
                    }
                })
                .then(res => res.ok ? res.text() : Promise.reject(res))
                .then(() => {
                    window.location.reload();
                })
                .catch(() => {
                    alert('Failed to post event. Please check your input.');
                });
            });
        }
    });
    </script>
    @if(session('success'))
        <div class="mb-6 text-green-900 bg-green-100 border-l-4 border-green-500 p-4 rounded-xl shadow flex items-center gap-3">
            <i class="bi bi-check-circle-fill text-green-600 text-2xl"></i> <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif
    <div class="grid grid-cols-1 gap-8 mt-8">
    @forelse($events as $event)
    <div class="bg-white rounded-3xl shadow-xl border-2 border-lime-100 hover:shadow-2xl transition flex flex-col items-center overflow-hidden w-full max-w-4xl mx-auto">
            @if($event->images->count())
                <div class="relative w-full bg-lime-50 flex flex-col items-center justify-center py-4 px-2">
                    <div class="relative w-full flex items-center justify-center">
                        <div class="overflow-hidden w-full flex items-center justify-center" style="min-height: 18rem;">
                            @php $firstMedia = $event->images->first(); @endphp
                            @if($firstMedia && $firstMedia->type === 'video')
                                <video src="{{ asset('storage/' . $firstMedia->image_path) }}" class="object-cover h-72 rounded-xl border border-lime-200 shadow event-image" style="min-width: 18rem; max-width: 24rem;" controls data-idx="0"></video>
                            @elseif($firstMedia)
                                <img src="{{ asset('storage/' . $firstMedia->image_path) }}" class="object-cover h-72 rounded-xl border border-lime-200 shadow event-image" style="min-width: 18rem; max-width: 24rem;" data-idx="0" />
                            @endif
                        </div>
                    </div>
                    <div class="flex gap-2 mt-4 overflow-x-auto pb-2 custom-scrollbar thumbnails-row">
                        @foreach($event->images as $idx => $img)
                            <div class="relative">
                                @if($img->type === 'video')
                                    <video src="{{ asset('storage/' . $img->image_path) }}" class="object-cover h-16 w-16 rounded-lg border-2 border-lime-300 shadow cursor-pointer event-thumb transition-all duration-200" data-idx="{{ $idx }}" style="object-position:center;" onclick="selectEventImage(this)" muted></video>
                                @else
                                    <img src="{{ asset('storage/' . $img->image_path) }}" class="object-cover h-16 w-16 rounded-lg border-2 border-lime-300 shadow cursor-pointer event-thumb transition-all duration-200" data-idx="{{ $idx }}" style="object-position:center;" onclick="selectEventImage(this)">
                                @endif
                                <div class="thumb-highlight absolute inset-0 pointer-events-none rounded-lg border-4 border-green-500 opacity-0"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="absolute top-4 right-6 bg-white bg-opacity-80 rounded-full px-3 py-1 text-xs font-semibold text-green-700 shadow">{{ $event->images->count() }} media</div>
                </div>
            @else
                <div class="h-72 w-full bg-gradient-to-br from-green-100 to-lime-100 flex items-center justify-center">
                    <i class="bi bi-image text-8xl text-lime-300"></i>
                </div>
            @endif
            <div class="p-10 flex-1 flex flex-col justify-between w-full">
                <div>
                    <div class="text-3xl font-bold text-green-900 mb-4 flex items-center gap-4 justify-center">
                        <i class="bi bi-megaphone-fill text-lime-600"></i> {{ $event->description }}
                    </div>
                    <div class="text-base text-gray-500 mb-3 flex items-center gap-2 justify-center">
                        <i class="bi bi-clock-history"></i> Posted: {{ $event->created_at->diffForHumans() }}
                    </div>
                    <div class="flex justify-center gap-4 mt-6">
                        <button type="button" class="inline-flex items-center gap-1 px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-white font-semibold rounded-lg shadow transition" onclick='openEditEventModal({{ $event->id }}, `{{ addslashes($event->description) }}`, `@json($event->images->map(fn($img) => ["id"=>$img->id,"image_path"=>$img->image_path,"type"=>$img->type]))` )'>
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
@push('scripts')
<script>
// Edit Event Modal logic
let currentEditEventId = null;
let currentEditEventMedia = [];
function openEditEventModal(eventId, description, mediaJson) {
    currentEditEventId = eventId;
    document.getElementById('editEventDescription').value = description;
    document.getElementById('editEventError').innerText = '';
    try {
        currentEditEventMedia = JSON.parse(mediaJson);
    } catch { currentEditEventMedia = []; }
    renderEditEventMedia();
    document.getElementById('editEventModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}
function closeEditEventModal() {
    document.getElementById('editEventModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}
function renderEditEventMedia() {
    const container = document.getElementById('editEventMediaPreview');
    container.innerHTML = '';
    currentEditEventMedia.forEach((media, idx) => {
        let el;
        if(media.type === 'video') {
            el = document.createElement('div');
            el.innerHTML = `<video src="/storage/${media.image_path}" class="h-20 w-20 object-cover rounded-lg border border-yellow-200 shadow" controls></video>`;
        } else {
            el = document.createElement('div');
            el.innerHTML = `<img src="/storage/${media.image_path}" class="h-20 w-20 object-cover rounded-lg border border-yellow-200 shadow" />`;
        }
        el.className = 'relative';
        const delBtn = document.createElement('button');
        delBtn.type = 'button';
        delBtn.className = 'absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 shadow';
        delBtn.innerHTML = '<i class="bi bi-trash"></i>';
        delBtn.onclick = function() {
            currentEditEventMedia.splice(idx, 1);
            renderEditEventMedia();
        };
        el.appendChild(delBtn);
        container.appendChild(el);
    });
}
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editEventForm');
    if(editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const desc = document.getElementById('editEventDescription').value;
            const errorDiv = document.getElementById('editEventError');
            const imagesInput = document.getElementById('editEventImagesInput');
            errorDiv.innerText = '';
            const formData = new FormData();
            formData.append('description', desc);
            // Attach new images
            if(imagesInput && imagesInput.files.length > 0) {
                Array.from(imagesInput.files).forEach(f => formData.append('images[]', f));
            }
            // Attach remaining media IDs (for backend to keep)
            formData.append('keep_media', JSON.stringify(currentEditEventMedia.map(m => m.id)));
            fetch(`/events/${currentEditEventId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'PUT',
                },
                body: formData
            })
            .then(res => {
                if(res.ok) return res.json ? res.json() : res.text();
                return res.json().then(data => { throw data; });
            })
            .then(() => { window.location.reload(); })
            .catch(err => {
                if(err && err.errors && err.errors.description) {
                    errorDiv.innerText = err.errors.description[0];
                } else {
                    errorDiv.innerText = 'Failed to update event.';
                }
            });
        });
    }
});
</script>
@endpush
                        <form action="{{ route('events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this event?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow transition">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
<script>
// Robust carousel for event images/videos
function swapMainMedia(thumb) {
    const parent = thumb.closest('.relative');
    const container = parent.querySelector('.overflow-hidden.w-full.flex.items-center.justify-center');
    // Remove all current main media (img or video)
    container.querySelectorAll('.event-image').forEach(el => el.remove());
    const idx = thumb.dataset.idx;
    const isVideo = thumb.tagName.toLowerCase() === 'video';
    let newMedia;
    if(isVideo) {
        newMedia = document.createElement('video');
        newMedia.src = thumb.src;
        newMedia.className = 'object-cover h-72 rounded-xl border border-lime-200 shadow event-image';
        newMedia.style = thumb.style.cssText;
        newMedia.controls = true;
        newMedia.dataset.idx = idx;
    } else {
        newMedia = document.createElement('img');
        newMedia.src = thumb.src;
        newMedia.className = 'object-cover h-72 rounded-xl border border-lime-200 shadow event-image';
        newMedia.style = thumb.style.cssText;
        newMedia.dataset.idx = idx;
    }
    container.appendChild(newMedia);
}
 
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.event-image').forEach(function(img, i) {
        img.dataset.idx = 0;
    });
    document.querySelectorAll('.thumbnails-row').forEach(function(row) {
        const thumbs = row.querySelectorAll('.event-thumb');
        if(thumbs.length) {
            highlightThumb(thumbs[0]);
        }
        thumbs.forEach(function(thumb) {
            thumb.addEventListener('click', function() {
                swapMainMedia(thumb);
                highlightThumb(thumb);
            });
        });
    });
});
 
function highlightThumb(thumb) {
    document.querySelectorAll('.thumbnails-row .thumb-highlight').forEach(el => el.style.opacity = 0);
    const highlight = thumb.parentElement.querySelector('.thumb-highlight');
    if(highlight) highlight.style.opacity = 1;
}
 
function slideEventImage(btn, dir) {
    const parent = btn.closest('.relative');
    const thumbs = parent.querySelectorAll('.event-thumb');
    let mainMedia = parent.querySelector('.event-image');
    let idx = parseInt(mainMedia.dataset.idx || 0);
    idx = idx + dir;
    if(idx < 0) idx = thumbs.length - 1;
    if(idx >= thumbs.length) idx = 0;
    swapMainMedia(thumbs[idx]);
}
 
function selectEventImage(thumb) {
    swapMainMedia(thumb);
}
</script>
        </div>
    @empty
        <div class="col-span-2 text-center text-gray-400 text-xl py-20">
            <i class="bi bi-calendar-x text-5xl mb-4"></i><br>
            No events found.
        </div>
    @endforelse
    </div>
    <div class="mt-10 flex justify-center">
        {{ $events->links() }}
    </div>
</div>
<style>
@keyframes fade-in {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease;
}
body.overflow-hidden {
    overflow: hidden;
}
</style>
@endsection