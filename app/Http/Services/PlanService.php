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
    protected $lang;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->lang = request()->header('Accept-Language') ?: self::getDefaultLang();
    }
    public function getDefaultLang(): string
    {
        return defaultLang() ?: 'en';
    }
    public function getActivePlans(string $orderBy = 'order_index', string $direction = 'asc')
    {
        $plans = Plan::query();
        return $plans->orderBy($orderBy, $direction)->latest();
    }

    public function getPlanById($param, string $query_field = 'id'): Plan|null
    {
        $plan = Plan::with('translations');
        return $plan->where($query_field, $param)->first();
    }

    // Optional: for admin use
    public function createPlan(array $data): Plan|null
    {
        try{
            $data['created_by'] = $this->user->id;
        if (isset($data['features']) && is_array($data['features'])) {
            $data['features'] = json_encode($data['features']);
        }
        return DB::transaction(function () use ($data) {
            $plan = Plan::create($data);
            PlanTranslation::create(['plan_id' => $plan->id, 'language' => $this->lang, 'name' => $data['name']]);
            TranslateModelJob::dispatch(Plan::class, PlanTranslation::class, 'plan_id', $plan->id, ['name'], $this->lang);
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
        $plan->update($data);
        return $plan;
    }

    public function deletePlan(Plan $plan): void
    {
        $plan->delete();
    }
}
