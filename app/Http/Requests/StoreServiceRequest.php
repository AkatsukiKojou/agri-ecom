<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Allow all admins to create
    }

    public function rules(): array
    {
        return [
            'service_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'tags' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_available' => 'nullable|boolean',
            'capacity' => 'nullable|integer|min:0',
            'location_scope' => 'nullable|string|max:255',
            'service_mode' => 'required|in:Online,Onsite,Hybrid',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'schedule' => 'nullable|string|max:255',
            'requirements' => 'nullable|string',
            'eligibility' => 'nullable|string',
            'image_upload' => 'nullable|image|max:2048', // max 2MB
            'brochure_upload' => 'nullable|mimes:pdf|max:5120', // max 5MB
            'contact_person_name' => 'required|string|max:255',
            'contact_person_phone' => 'required|string|max:20',
            'contact_person_email' => 'nullable|email|max:255',
        ];
    }
}
