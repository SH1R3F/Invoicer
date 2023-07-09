<?php

namespace App\Rules;

use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\ValidationRule;

class UrlOrImage implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!empty($value) && !($value instanceof UploadedFile) && !filter_var($value, FILTER_VALIDATE_URL)) {
            $fail(__(':attribute value is invalid', ['attribute' => $attribute]));
        }

        if ($value instanceof UploadedFile) {
            // Validate image
            $validator = Validator::make([$attribute => $value], [$attribute => 'image|mimes:jpg,png,jpeg|max:10240']);
            if ($validator->fails()) {
                $fail($validator->errors()->first($attribute));
            }
        }
    }
}
