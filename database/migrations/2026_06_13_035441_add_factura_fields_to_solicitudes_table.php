<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('al_solicitudes', function (Blueprint $table) {
            $table->decimal('IMPORTE_FACTURA', 12, 2)->nullable()->after('ESTADO_FACTURA');
            $table->string('FACTURA_PDF', 500)->nullable()->after('IMPORTE_FACTURA');
            $table->string('FACTURA_XML', 500)->nullable()->after('FACTURA_PDF');
        });
    }

    public function down(): void
    {
        Schema::table('al_solicitudes', function (Blueprint $table) {
            $table->dropColumn(['IMPORTE_FACTURA', 'FACTURA_PDF', 'FACTURA_XML']);
        });
    }
};
