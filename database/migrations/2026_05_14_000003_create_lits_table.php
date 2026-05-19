<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salle_id')->constrained()->onDelete('cascade');
            $table->string('numero');
            $table->enum('statut', ['libre', 'occupe', 'maintenance', 'hors_service'])->default('libre');
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lits');
    }
};
