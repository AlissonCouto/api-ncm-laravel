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
    Schema::create('ncm_codes', function (Blueprint $table) {
        $table->id();
        $table->string('ncm_code', 50)->unique(); // Código NCM (único)
        $table->text('description');
        $table->string('parent_code', 50)->nullable(); // Adiciona parent_code
        $table->date('start_date'); // Data de início de validade
        $table->date('end_date'); // Data de fim de validade
        $table->string('normative_act_type', 50)->nullable(); // Tipo do ato normativo
        $table->integer('normative_act_number')->nullable(); // Número do ato normativo
        $table->integer('normative_act_year')->nullable(); // Ano do ato normativo

        // Adiciona índice na coluna parent_code para a chave estrangeira
        $table->index('ncm_code');
        $table->index('parent_code');

        // Cria a chave estrangeira com as ações de cascata
        $table->foreign('parent_code')->references('ncm_code')->on('ncm_codes')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ncm_codes');
    }
};
