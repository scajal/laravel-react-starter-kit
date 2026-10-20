<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->json('attribute_changes')->nullable();
            $table->nullableUlidMorphs('causer', 'causer');
            $table->text('description');
            $table->string('event')->nullable();
            $table->string('log_name')->nullable()->index();
            $table->json('properties')->nullable();
            $table->nullableUlidMorphs('subject', 'subject');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
