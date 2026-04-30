<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            // Vérifier si les colonnes n'existent pas déjà
            if (!Schema::hasColumn('factures', 'pec_organisme')) {
                $table->string('pec_organisme')->nullable()->after('montant')
                      ->comment('Nom de l\'organisme preneur en charge');
            }

            if (!Schema::hasColumn('factures', 'pec_montant')) {
                $table->decimal('pec_montant', 10, 2)->nullable()->after('pec_organisme')
                      ->comment('Montant couvert par la prise en charge');
            }
        });
    }

    public function down(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->dropColumn(['pec_organisme', 'pec_montant']);
        });
    }
};
