<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Optional link to user account
            $table->foreignId('contracting_process_id')->constrained()->cascadeOnDelete();
            
            // Personal data (Sync from Candidate)
            $table->string('document_type');
            $table->string('document_number');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            
            // Employment data
            $table->string('cargo');
            $table->decimal('salary', 15, 2);
            $table->date('hired_at');
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
