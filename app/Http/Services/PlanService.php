<?php

namespace App\Http\Services;

use App\Jobs\TranslateModelJob;
use App\Models\Plan;
use App\Models\PlanTranslation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlanService
{
    protected User $user;
    protected string $lang;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->lang = request()->header('Accept-Language') ?: self::getDefaultLang();
    }
    public static function getDefaultLang(): string
    {
        return defaultLang() ?: 'en';
    }
    public function getPlans(string $orderBy = 'order_index', string $direction = 'asc')
    {
        $plans = Plan::with('translations');
        return $plans->orderBy($orderBy, $direction)->latest();
    }

    public function getPlan($param, string $query_field = 'id'): Plan|null
    {
        $plan = Plan::with('translations');
        return $plan->where($query_field, $param)->first();
    }

    // Optional: for admin use
    public function createPlan(array $data): Plan|null
    {
        try {
            $data['created_by'] = $this->user->id;
            if (isset($data['features']) && is_array($data['features'])) {
                $data['features'] = json_encode($data['features']);
            }
            return DB::transaction(function () use ($data) {
                $plan = Plan::create($data);
                PlanTranslation::create([
                    'plan_id' => $plan->id,
                    'language' => $this->lang,
                    'name' => $data['name'],
                    'description' => $data['description']
                ]);
                TranslateModelJob::dispatch(Plan::class, PlanTranslation::class, 'plan_id', $plan->id, ['name', 'description'], $this->lang);
                $plan = $plan->refresh()->load('translations');
                return $plan;
            });
        } catch (\Exception $e) {
            Log::error('Plan Create Error: ' . $e->getMessage());
            return null;
        }
    }

    public function updatePlan(Plan $plan, array $data): Plan
    {
        try {
            if (isset($data['features']) && is_array($data['features'])) {
                $data['features'] = json_encode($data['features']);
            }
            $plan->update($data);
            PlanTranslation::updateOrCreate([
                'plan_id' => $plan->id,
                'language' => $this->lang,
            ], [
                'name' => $data['name'],
                'description' => $data['description']
            ]);
            TranslateModelJob::dispatch(Plan::class, PlanTranslation::class, 'plan_id', $plan->id, ['name', 'description'], $this->lang);
            $plan = $plan->refresh()->load('translations');
            return $plan;
        } catch (\Exception $e) {
            Log::error('Plan Update Error: ' . $e->getMessage());
            return $plan;
        }
    }

    public function deletePlan(Plan $plan): void
    {
        try {
            $plan->delete();
        } catch (\Exception $e) {
            Log::error('Plan Delete Error: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Plan $plan): Plan
    {
        $plan->update(['status' => !$plan->status, 'updated_by' => $this->user->id]);
        return $plan->refresh();
    }
}
