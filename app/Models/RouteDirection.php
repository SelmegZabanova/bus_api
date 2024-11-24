<?php

namespace App\Models;

use App\Enums\DirectionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RouteDirection extends Model
{
    use HasFactory;
    protected $fillable = ['route_id', 'direction'];

    protected $casts = [
        'direction' => DirectionType::class
    ];

    //маршрут
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }


     //все остановки в порядке следования

    public function stops(): BelongsToMany
    {
        return $this->belongsToMany(Stop::class, 'route_stops')
            ->withPivot('stop_order')
            ->orderBy('stop_order');
    }


     //все остановки маршрута

    public function routeStops(): HasMany
    {
        return $this->hasMany(RouteStop::class);
    }

}
