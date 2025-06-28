<?php

namespace App\Http\Services;

use GuzzleHttp\Client;

class LibreTranslateService
{
    protected $client;
    protected $url;

    public function __construct()
    {
        $this->client = new Client();
        $this->url = 'https://libretranslate.de/translate'; // You can replace this with your self-hosted URL
    }

    /**
     * Translate text using LibreTranslate API.
     *
     * @param  string  $text
     * @param  string  $targetLang
     * @param  string  $sourceLang
     * @return string
     */
    public function translate($text, $targetLang = 'es', $sourceLang = 'en')
    {
        try {
            // Send the POST request to LibreTranslate
            $response = $this->client->post($this->url, [
                'form_params' => [
                    'q' => $text,
                    'source' => $sourceLang,
                    'target' => $targetLang,
                    'format' => 'text',
                ]
            ]);


            $body = $response->getBody()->getContents();
            dd($response->getStatusCode(), $body);

            $test = json_decode($body, true);

            // Decode the JSON response body
            dd($test);

            // Check for the 'translatedText' key in the response
            if (isset($body['translatedText'])) {
                return $body['translatedText']; // Return the translated text
            } else {
                return 'Translation failed'; // If 'translatedText' is not set
            }

        } catch (\Exception $e) {
            // Catch any exceptions and return the error message
            return 'Error: ' . $e->getMessage();
        }
    }
}
