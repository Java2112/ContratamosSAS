<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Contracting\DocumentStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contracting_process_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained('contracting_document_types')->cascadeOnDelete();
            $table->string('file_path')->nullable();
            $table->string('status')->default(DocumentStatus::PENDING->value);
            $table->text('rejection_reason')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};
