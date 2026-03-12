<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('kelas')->unique();
            $table->time('morning_start')->default('06:30');
            $table->time('morning_end')->default('08:00');
            $table->time('afternoon_start')->default('15:00');
            $table->time('afternoon_end')->default('16:00');
            $table->time('late_threshold')->default('07:30');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_schedules');
    }
};
