<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 12)->unique(); // e.g. KR-0001
            $table->string('session_id', 64)->index();
            $table->date('event_date')->nullable();
            $table->string('event_type', 40)->nullable();
            $table->string('guest_range', 20)->nullable();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->text('message')->nullable();
            $table->string('source')->default('website'); // website | direct | social
            $table->enum('status', ['new', 'contacted', 'negotiating', 'confirmed', 'lost'])->default('new');
            $table->boolean('is_complete')->default(false);
            $table->timestamp('whatsapp_opened_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['event_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
