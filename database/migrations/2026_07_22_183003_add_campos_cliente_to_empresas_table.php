<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->enum('tipo_cliente', ['corporativo', 'academico', 'gobierno'])
                ->default('corporativo')->after('nombre');

            $table->string('nombre_contacto_operativo')->nullable();
            $table->string('apellido_contacto_operativo')->nullable();
            $table->string('telefono_contacto_operativo')->nullable();
            $table->string('email_contacto_operativo')->nullable();

            $table->string('nombre_contacto_facturacion')->nullable();
            $table->string('apellido_contacto_facturacion')->nullable();
            $table->string('telefono_contacto_facturacion')->nullable();
            $table->string('email_contacto_facturacion')->nullable();

            $table->date('fecha_de_contrato')->nullable();
            $table->unsignedInteger('vigencia_contrato')->nullable(); // años
            $table->unsignedInteger('dias_de_credito')->nullable();

            $table->string('rfc_fiscal')->nullable(); // ya existe 'rfc' en la tabla, ver nota abajo
            $table->string('uso_de_cfdi')->nullable();
            $table->string('razon_social_fiscal')->nullable(); // ya existe 'razon_social', ver nota
            $table->text('direccion_fiscal')->nullable();
            $table->string('portal_de_facturacion')->nullable();
            $table->text('notas')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_cliente',
                'nombre_contacto_operativo', 'apellido_contacto_operativo',
                'telefono_contacto_operativo', 'email_contacto_operativo',
                'nombre_contacto_facturacion', 'apellido_contacto_facturacion',
                'telefono_contacto_facturacion', 'email_contacto_facturacion',
                'fecha_de_contrato', 'vigencia_contrato', 'dias_de_credito',
                'rfc_fiscal', 'uso_de_cfdi', 'razon_social_fiscal',
                'direccion_fiscal', 'portal_de_facturacion', 'notas',
            ]);
        });
    }
};