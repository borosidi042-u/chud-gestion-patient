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
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->string('numero_facture'); // Numéro du reçu papier
            $table->decimal('montant', 10, 2);
            $table->date('date_facture');
            $table->foreignId('service_id')->constrained(); // Service lié à la prestation
            $table->foreignId('user_id')->constrained(); // Agent qui enregistre
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
