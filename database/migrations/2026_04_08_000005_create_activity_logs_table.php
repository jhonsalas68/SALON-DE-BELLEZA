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
        Schema::create('activity_logs', function (Blueprint $col) {
            $col->id();
            $col->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $col->string('action'); // e.g., 'CREATE', 'UPDATE', 'DELETE', 'LOGIN'
            $col->text('description');
            $col->json('details')->nullable(); // Store old/new values if needed
            $col->string('ip_address')->nullable();
            $col->string('user_agent')->nullable();
            $col->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
