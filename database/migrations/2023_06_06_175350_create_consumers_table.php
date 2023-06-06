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
        Schema::create('consumers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rfid_code')->unique();
            $table->timestamps();
        });

        Schema::create('concrete_sessions', function (Blueprint $table) {
            $table->id();
            $table->timestamp('imported_at');
            $table->timestamp('delivered_at');
            $table->string('file_name');
            $table->string('file_path');

            $table
                ->foreignId('consumer_id')
                ->constrained('consumers')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumers');
    }
};
