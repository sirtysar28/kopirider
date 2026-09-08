<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('date_statuses', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->enum('status', ['available', 'enquiry', 'booked'])->default('available');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('date_statuses');
    }
};
