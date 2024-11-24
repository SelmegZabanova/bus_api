<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RouteStop extends Model
{
    use HasFactory;
    protected $fillable = [
        'route_direction_id',
        'stop_id',
        'stop_order'
    ];


    //направление маршрута

    public function routeDirection(): BelongsTo
    {
        return $this->belongsTo(RouteDirection::class);
    }

    //Получить остановку

    public function stop(): BelongsTo
    {
        return $this->belongsTo(Stop::class);
    }


     //расписание для этой остановки маршрута

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function lastStop()
    {
        return $this->stop()->orderBy('stop_order', 'desc')->first();
    }

}
