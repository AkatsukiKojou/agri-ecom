<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'profile_photo' => 'nullable|image|max:2048',
            'owner_name' => 'required|string|max:255',
            'farm_name' => 'required|string|max:255',
            'certificate' => 'nullable|image|max:2048',
            'farm_photo' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'email' => 'nullable|email',
            'fb' => 'nullable|url',
            'phone_number' => 'nullable|string|max:20',
    
            'sex' => 'nullable|in:Male,Female,Other',
            'birthdate' => 'nullable|date',
            'civil_status' => 'nullable|string',
            'education_level' => 'nullable|string',
    
            'farm_size' => 'nullable|numeric',
            'farm_type' => 'nullable|string',
            'main_crop' => 'nullable|string',
            'tenure_status' => 'nullable|string',
            'years_of_operation' => 'nullable|integer',
    
            'has_irrigation' => 'boolean',
            'uses_fertilizer' => 'boolean',
            'access_to_credit' => 'boolean',
            'machinery_owned' => 'nullable|string',
    
            'certification_number' => 'nullable|string',
            'association_name' => 'nullable|string',
    
            'province_id' => 'required|exists:provinces,id',
            'municipality_id' => 'required|exists:municipalities,id',
            'barangay_id' => 'required|exists:barangays,id',
        ];
    }
    
}
