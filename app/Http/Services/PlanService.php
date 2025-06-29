<?php

namespace App\Http\Services;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Collection;

class PlanService
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
    public function getActivePlans(): Collection
    {
        return Plan::where('status', Plan::STATUS_ACTIVE)
            ->orderBy('order_index', 'asc')
            ->get();
    }

    public function getPlanById(int $id): ?Plan
    {
        return Plan::where('status', Plan::STATUS_ACTIVE)->find($id);
    }

    // Optional: for admin use
    public function createPlan(array $data): Plan
    {
        $data['created_by'] = $this->user->id;
        $data['updated_by'] = $this->user->id;
        if (isset($data['features']) && is_array($data['features'])) {
            $data['features'] = json_encode($data['features']);
        }

        return Plan::create($data);
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
