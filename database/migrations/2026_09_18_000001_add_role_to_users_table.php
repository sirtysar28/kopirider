<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a role column so lead deletion (and future admin-only powers)
     * can be limited to super admins.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('staff')->after('password');
        });

        // The first account ever created is the owner → make them super admin.
        $firstId = DB::table('users')->orderBy('id')->value('id');

        if ($firstId) {
            DB::table('users')->where('id', $firstId)->update(['role' => 'superadmin']);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
