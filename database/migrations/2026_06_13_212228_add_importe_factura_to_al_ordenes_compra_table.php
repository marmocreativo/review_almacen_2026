<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('al_ordenes_compra', function (Blueprint $table) {
            $table->decimal('IMPORTE_FACTURA', 12, 2)->nullable()->after('FOLIO_FACTURA');
        });
    }

    public function down(): void
    {
        Schema::table('al_ordenes_compra', function (Blueprint $table) {
            $table->dropColumn('IMPORTE_FACTURA');
        });
    }
};
