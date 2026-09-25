<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Consent records for the booking form checkboxes (legal update 21-09-2026).
     * Every checkbox is opt-in only — nothing is pre-ticked.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->boolean('consent_privacy')->default(false)->after('whatsapp_opened_at');
            $table->boolean('consent_terms')->default(false)->after('consent_privacy');
            $table->boolean('consent_marketing')->default(false)->after('consent_terms');
            $table->boolean('consent_photos')->default(false)->after('consent_marketing');
            $table->timestamp('consented_at')->nullable()->after('consent_photos');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['consent_privacy', 'consent_terms', 'consent_marketing', 'consent_photos', 'consented_at']);
        });
    }
};
