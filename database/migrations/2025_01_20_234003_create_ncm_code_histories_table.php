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
        Schema::create('ncm_code_histories', function (Blueprint $table) {
            $table->id();
            $table->string('ncm_code', 50);
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('normative_act_type')->nullable();
            $table->integer('normative_act_number')->nullable();
            $table->integer('normative_act_year')->nullable();
            $table->string('updated_by')->nullable(); // O usuário ou sistema que fez a alteração
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ncm_code_histories');
    }
};
