<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'subject' => 'nullable|string|max:5000',
            'file' => 'nullable|file|mimes:pdf,docx,odt,mp4,mov,avi,wmv,webm|max:20480', // 20MB limit for Gemini inline data
            'language' => 'required|string|in:fr,en,es,de',
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (empty($this->subject) && !$this->hasFile('file')) {
                $validator->errors()->add('subject', __('Please provide either a subject description or upload a file.'));
            }
        });
    }
}
