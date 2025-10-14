<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ValidCep implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cep = preg_replace('/[^0-9]/', '', $value);

        // Cache key para evitar consultas repetidas
        $cacheKey = "cep:{$cep}";

        $result = Cache::remember($cacheKey, now()->addHours(2), function () use ($cep) {
            $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");
            if ($response->failed()) {
                return null;
            }
            return $response->json();
        });

        if (! $result || isset($result['erro'])) {
            $fail('The zip code is invalid');
        }
    }
}
