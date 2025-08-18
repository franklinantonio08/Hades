<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Nombres desglosados + 'name' por compatibilidad
            $table->string('primer_nombre', 100);
            $table->string('segundo_nombre', 100)->nullable();
            $table->string('primer_apellido', 100);
            $table->string('segundo_apellido', 100)->nullable();
            $table->string('name'); // lo llenamos concatenando en el controlador

            // Contacto
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('telefono', 25)->nullable();

            // Tipo de usuario
            $table->enum('tipo_usuario', ['abogado','solicitante'])->default('solicitante');

            // Documentos (solicitante)
            $table->string('documento_tipo', 20)->nullable();   // p.ej. Ruex
            $table->string('documento_numero', 50)->nullable();

            // Abogado
            $table->string('abogado_id', 50)->nullable();
            $table->string('firma_estudio', 120)->nullable();
            $table->string('idoneidad_path')->nullable();

            // Auth
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
