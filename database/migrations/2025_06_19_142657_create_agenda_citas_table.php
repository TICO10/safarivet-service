<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agenda_citas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('lastName');
            $table->string('email');
            $table->integer('phone');
            $table->string('petName');
            $table->string('petClase');
            $table->date('regfecxx');
            $table->time('reghorxx');
            $table->string('regusrxx');
            $table->date('regfecmx');
            $table->time('reghormx');
            $table->string('regusrmx');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_citas');
    }
};
