<?php

namespace App\Models;

use App\Traits\SanitizedRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use SanitizedRequest;

    protected static function booted()
    {
        static::created(function ($subscription) {
            switch($subscription->service->plan_period_format) {
                case 'D':
                    $end_at = Carbon::now()->addDay($subscription->service->plan_period);
                    break;
                case 'W':
                    $end_at = Carbon::now()->addWeek($subscription->service->plan_period);
                    break;
                case 'M':
                    $end_at = Carbon::now()->addMonth($subscription->service->plan_period);
                    break;
                case 'Y':
                    $end_at = Carbon::now()->addYear($subscription->service->plan_period);
                    break;
                default:
                    $end_at = Carbon::now()->addDay(1);
                    break;
            }

            RoleUser::updateOrCreate([
                'user_id' => $subscription->user->id,
            ], [
                'role_id' => $subscription->service->role_id,
                'end_at' => $end_at
            ]);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}