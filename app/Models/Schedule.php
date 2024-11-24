<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;
    protected $table = 'schedule';

    protected $fillable = [
        'route_stop_id',
        'arrival_time'
    ];

    protected $casts = [
        'arrival_time' => 'datetime:H:i'
    ];

   //остановка маршрута
    public function routeStop(): BelongsTo
    {
        return $this->belongsTo(RouteStop::class);
    }

}
