<?php

namespace App\Jobs;

use App\Http\Services\DeeplTranslateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TranslateModelJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, Dispatchable;

    public $tries = 3;
    public $timeout = 30;
    public $backoff = [10, 30, 60];

    public function __construct(
        private string $modelClass,
        private string $translationModelClass,
        private string $foreignField,
        private int $modelId,
        private array $fieldsToTranslate,
        private string $sourceLanguage,
    ) {
        // $this->onQueue('translations');
    }

    public function handle(DeeplTranslateService $deepl): void
    {

        Log::info("Translating model", [
            'model_class' => $this->modelClass,
            'translation_model_class' => $this->translationModelClass,
            $this->foreignField => $this->modelId,
            'fields' => $this->fieldsToTranslate,

        ]);
        try {
            // Ensure model classes are valid
            if (!class_exists($this->modelClass)) {
                throw new \Exception("Model class not found: {$this->modelClass}");
            }

            if (!class_exists($this->translationModelClass)) {
                throw new \Exception("Translation model class not found: {$this->translationModelClass}");
            }
            Log::info("Source Language", [
                'source_language' => $this->sourceLanguage
            ]);
            // Get the source model
            $sourceModel = $this->translationModelClass::where($this->foreignField, $this->modelId)->where('language', $this->sourceLanguage)->first();
            Log::info("Source model found", [
                'model' => $sourceModel
            ]);
            if (!$sourceModel) {
                Log::warning("Model not found for translation", [
                    'source_model' => $this->modelClass,
                    'translation_model' => $this->translationModelClass,
                    $this->foreignField => $this->modelId
                ]);
                return;
            }

            // Get available languages from the helper (allowLangs should return an array of languages)
            $allowedLanguages = collect(allowLangs())
                ->except($this->sourceLanguage)
                ->values()
                ->toArray();

            if (empty($allowedLanguages)) {
                throw new \Exception("No allowed languages found");
            }
            // Loop through allowed languages or pick a specific language for translation
            foreach ($allowedLanguages as $targetLanguage) {
                try {
                    // Perform translations
                    $translatedData = [];

                    foreach ($this->fieldsToTranslate as $field) {
                        if (!isset($sourceModel->$field)) {
                            continue;
                        }

                        $originalText = $sourceModel->$field;
                        $translatedText = $deepl->translate($originalText, $targetLanguage, $this->sourceLanguage);
                        $translatedData[$field] = $translatedText;
                    }

                    Log::info("Translation started", [
                        'model_type' => $this->modelClass,
                        $this->foreignField => $this->modelId,
                        'language' => $targetLanguage,
                        'source_language' => $this->sourceLanguage,
                        'fields' => $translatedData,
                    ]);

                    if (empty($translatedData)) {
                        Log::warning("No fields to translate", [
                            'model_type' => $this->modelClass,
                            $this->foreignField => $this->modelId,
                            'fields' => $this->fieldsToTranslate
                        ]);
                        continue; // Skip if no data to translate
                    }

                    // Save translation
                    $this->translationModelClass::updateOrCreate(
                        [
                            $this->foreignField => $this->modelId,
                            'language' => $targetLanguage,
                        ],
                        $translatedData
                    );




                    Log::info("Translation completed successfully", [
                        'model_type' => $this->modelClass,
                        $this->foreignField => $this->modelId,
                        'language' => $targetLanguage,
                        'fields' => array_keys($translatedData)
                    ]);
                } catch (\Exception $e) {

                    Log::error('Generic translation job failed', [
                        'model_type' => $this->modelClass,
                        $this->foreignField => $this->modelId,
                        'error' => $e->getMessage(),
                        'attempt' => $this->attempts()
                    ]);
                    if ($this->attempts() >= $this->tries) {
                        $this->markTranslationAsFailed($targetLanguage);
                    }
                    throw $e;
                }
            }
        } catch (\Exception $e) {
            Log::error('Generic translation job failed', [
                'model_type' => $this->modelClass,
                $this->foreignField => $this->modelId,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);
            throw $e;
        }
    }

    private function markTranslationAsFailed(string $targetLanguage): void
    {
        DB::table('failed_model_translations')->insert([
            'model_type' => $this->modelClass,
            $this->foreignField => $this->modelId,
            'language' => $targetLanguage,
            'fields' => json_encode($this->fieldsToTranslate),
            'failed_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Translation job permanently failed', [
            'model_type' => $this->modelClass,
            $this->foreignField => $this->modelId,
            'error' => $exception->getMessage()
        ]);
    }
}
