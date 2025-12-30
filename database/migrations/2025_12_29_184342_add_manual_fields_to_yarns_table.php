<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('yarns', function (Blueprint $table) {
            $table->string('name', 120)->nullable()->after('location_id');

            // Hersteller-/Farbcode (z.B. "401", "A12", "Red-07", Hex wäre auch möglich)
            $table->string('color_code', 40)->nullable()->after('name');

            // Chargennummer / Dye Lot
            $table->string('batch_number', 60)->nullable()->after('color_code');

            // Empfohlene Nadelstärke (oft Range, daher String: "3.5–4.0", "4", "4,5")
            $table->string('needle_size', 20)->nullable()->after('batch_number');

            // Optional: schneller suchen/filtern pro User
            $table->index(['user_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::table('yarns', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'name']);
            $table->dropColumn(['name', 'color_code', 'batch_number', 'needle_size']);
        });
    }
};
