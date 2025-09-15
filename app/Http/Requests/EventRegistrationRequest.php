<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $eventId = $this->input('event_id');
        
        return [
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('registrations')->where('event_id', $eventId)
            ],
            'remark' => 'nullable|string|max:1000',
            'event_id' => 'required|integer|exists:events,id',
            'user_id' => 'required|integer|exists:users,id'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Full name is required.',
            'name.max' => 'Full name cannot exceed 255 characters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email has already been registered for this event.',
            'mobile.max' => 'Mobile number cannot exceed 20 characters.',
            'remark.max' => 'Additional comments cannot exceed 1000 characters.',
            'event_id.required' => 'Event ID is required.',
            'event_id.exists' => 'The selected event does not exist.',
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'The selected user does not exist.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'full name',
            'mobile' => 'mobile number',
            'email' => 'email address',
            'remark' => 'additional comments',
            'event_id' => 'event',
            'user_id' => 'user'
        ];
    }
}

