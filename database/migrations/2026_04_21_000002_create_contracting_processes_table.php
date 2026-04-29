<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Contracting\ProcessStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracting_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default(ProcessStatus::PENDING_DOCUMENTS->value);
            
            // Received data from Selection
            $table->decimal('agreed_salary', 15, 2)->nullable();
            $table->string('contract_type')->nullable();
            $table->string('cargo')->nullable();
            
            // Timestamps
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracting_processes');
    }
};
