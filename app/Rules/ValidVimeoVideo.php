<?php

namespace App\Rules;

use App\Actions\Courses\ResolveVimeoEmbedUrlAction;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidVimeoVideo implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (blank($value)) {
            return;
        }

        if (app(ResolveVimeoEmbedUrlAction::class)->handle($value) === null) {
            $fail('No reconocemos ese valor. Pega el número del video o el link de Vimeo');
        }
    }
}
