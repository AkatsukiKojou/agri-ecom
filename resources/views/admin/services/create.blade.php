@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Create Service</h2>

    @if ($errors->any())
        <div class="mb-4 text-red-600">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label>Service Name *</label>
                <input type="text" name="service_name" value="{{ old('service_name') }}" class="border w-full p-2 rounded" required>
            </div>

            <div>
                <label>Category</label>
                <select name="category" class="border w-full p-2 rounded">
                    <option value="">Select Category</option>
                    <option value="Health" {{ old('category') == 'Health' ? 'selected' : '' }}>Health</option>
                    <option value="Education" {{ old('category') == 'Education' ? 'selected' : '' }}>Education</option>
                    <option value="Agriculture" {{ old('category') == 'Agriculture' ? 'selected' : '' }}>Agriculture</option>
                    <option value="Technology" {{ old('category') == 'Technology' ? 'selected' : '' }}>Technology</option>
                    <option value="Finance" {{ old('category') == 'Finance' ? 'selected' : '' }}>Finance</option>
                </select>
            </div>

            <div>
                <label>Tags (comma separated)</label>
                <input type="text" name="tags" value="{{ old('tags') }}" class="border w-full p-2 rounded">
            </div>

            <div>
                <label>Price (leave blank if free)</label>
                <input type="number" step="0.01" name="price" value="{{ old('price') }}" class="border w-full p-2 rounded">
            </div>

            <div>
                <label>Capacity</label>
                <input type="number" name="capacity" value="{{ old('capacity') }}" class="border w-full p-2 rounded">
            </div>

            <div>
                <label>Location Scope</label>
                <input type="text" name="location_scope" value="{{ old('location_scope') }}" class="border w-full p-2 rounded">
            </div>

            <div>
                <label>Service Mode *</label>
                <select name="service_mode" class="border w-full p-2 rounded" required>
                    <option value="">Select Mode</option>
                    <option value="Online" {{ old('service_mode') == 'Online' ? 'selected' : '' }}>Online</option>
                    <option value="Onsite" {{ old('service_mode') == 'Onsite' ? 'selected' : '' }}>Onsite</option>
                    <option value="Hybrid" {{ old('service_mode') == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <input type="checkbox" name="is_available" value="1" {{ old('is_available', 1) ? 'checked' : '' }}>
                <label>Available</label>
            </div>

            <div>
                <label>Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" class="border w-full p-2 rounded">
            </div>

            <div>
                <label>End Date</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" class="border w-full p-2 rounded">
            </div>

            <div class="col-span-2">
                <label>Schedule</label>
                <input type="text" name="schedule" value="{{ old('schedule') }}" class="border w-full p-2 rounded">
            </div>

            <div class="col-span-2">
                <label>Description</label>
                <textarea name="description" rows="3" class="border w-full p-2 rounded">{{ old('description') }}</textarea>
            </div>

            <div class="col-span-2">
                <label>Requirements</label>
                <textarea name="requirements" rows="2" class="border w-full p-2 rounded">{{ old('requirements') }}</textarea>
            </div>

            <div class="col-span-2">
                <label>Eligibility</label>
                <textarea name="eligibility" rows="2" class="border w-full p-2 rounded">{{ old('eligibility') }}</textarea>
            </div>

            <div class="col-span-2">
                <label>Image Upload</label>
                <input type="file" name="image_upload" class="border w-full p-2 rounded">
            </div>

            <div class="col-span-2">
                <label>Brochure Upload (PDF)</label>
                <input type="file" name="brochure_upload" class="border w-full p-2 rounded">
            </div>

            <div>
                <label>Contact Person Name *</label>
                <input type="text" name="contact_person_name" value="{{ old('contact_person_name') }}" class="border w-full p-2 rounded" required>
            </div>

            <div>
                <label>Contact Person Phone *</label>
                <input type="text" name="contact_person_phone" value="{{ old('contact_person_phone') }}" class="border w-full p-2 rounded" required>
            </div>

            <div class="col-span-2">
                <label>Contact Person Email</label>
                <input type="email" name="contact_person_email" value="{{ old('contact_person_email') }}" class="border w-full p-2 rounded">
            </div>

        </div>

        <div class="mt-6">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Create Service</button>
        </div>
    </form>
</div>
@endsection
