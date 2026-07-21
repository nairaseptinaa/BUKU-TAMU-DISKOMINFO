<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guestbooks', function (Blueprint $table) {
            $table->id();
            $table->timestamp('visit_date')->useCurrent();
            $table->string('name', 150);
            $table->string('position', 100);
            $table->enum('visitor_type', ['internal', 'external']);
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('external_agency', 150)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->foreignId('service_type_id')->constrained('service_types');
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guestbooks');
    }
};