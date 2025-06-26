<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Services\DeeplTranslateService;
use App\Http\Services\LibreTranslateService;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    protected LibreTranslateService $libreTranslateService;
    protected DeeplTranslateService $deepl;

    public function __construct(LibreTranslateService $libreTranslateService, DeeplTranslateService $deepl)
    {
        $this->libreTranslateService = $libreTranslateService;
        $this->deepl = $deepl;
    }

    public function libreTranslate(Request $request)
    {
        // Validate input data
        $request->validate([
            'text' => 'required|string',         // Make sure 'text' is provided
            'target_lang' => 'nullable|string',  // Optional target language (default 'es')
            'source_lang' => 'nullable|string',  // Optional source language (default 'en')
        ]);

        // Retrieve input from the request
        $text = $request->input('text'); // Text to translate
        $targetLang = $request->input('target_lang', 'es'); // Default target language is Spanish
        $sourceLang = $request->input('source_lang', 'en'); // Default source language is English

        // Perform the translation
        $translatedText = $this->libreTranslateService->translate($text, $targetLang, $sourceLang);

        // Return the translated text as a JSON response
        return response()->json([
            'original' => $text,
            'translated' => $translatedText
        ]);
    }


    public function deeplTranslate(Request $request)
    {

        $text = $request->input('text');
        $to = $request->input('to');
        try {
            $translated = $this->deepl->translate($text, $to);
            return response()->json(['translated' => $translated]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
