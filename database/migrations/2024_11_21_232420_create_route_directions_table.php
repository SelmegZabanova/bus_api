<?php

use App\Enums\DirectionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('route_directions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->enum('direction',['ПРЯМОЕ', 'ОБРАТНОЕ']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_directions');
    }
};
