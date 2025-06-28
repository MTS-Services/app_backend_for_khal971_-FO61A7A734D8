<?php

namespace App\Http\Services;
use Illuminate\Support\Facades\Http;

class DeeplTranslateService
{

    protected $allow_langs = ['es', 'ar', 'it'];

    public function translate(string $text, string $to = 'EN', string $from = null): string
    {
        $params = [
            'auth_key' => config('services.deepl.api_key'),
            'text' => $text,
            'target_lang' => strtoupper($to),
        ];


        if ($from) {
            $params['source_lang'] = strtoupper($from);
        }

        $response = Http::asForm()->post(config('services.deepl.endpoint'), $params);

        if ($response->successful()) {
            return $response->json()['translations'][0]['text'];
        }

        throw new \Exception('DeepL translation failed: ' . $response->body());
    }
}
