<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Contracting\AffiliationStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_affiliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contracting_process_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // EPS, ARL, Pensión, Cesantías
            $table->string('entity_name');
            $table->string('affiliation_number')->nullable();
            $table->date('affiliation_date')->nullable();
            $table->string('status')->default(AffiliationStatus::PENDING->value);
            $table->string('file_path')->nullable(); // Proof of affiliation
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_affiliations');
    }
};
