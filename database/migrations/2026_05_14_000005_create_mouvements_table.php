<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visite_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained();
            $table->foreignId('salle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lit_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['entree', 'passage', 'transfert', 'sortie']);
            $table->timestamp('heure_arrivee');
            $table->timestamp('heure_depart')->nullable();
            $table->foreignId('agent_id')->constrained('users');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements');
    }
};
