<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Stop extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    // маршруты, проходящие через данную остановку
    public function routeDirections(): BelongsToMany
    {
        return $this->belongsToMany(RouteDirection::class, 'route_stops')
            ->withPivot('stop_order')
            ->orderBy('stop_order');
    }
    //маршруты, где данная остановка конечная
    public function terminalForDirections(): HasMany
    {
        return $this->hasMany(RouteDirection::class, 'last_stop_id');
    }

}
