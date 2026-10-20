<?php

declare(strict_types=1);

use App\Modules\Core\Identity\Models\Team;
use App\Modules\Core\Identity\Models\User;
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
        Schema::create('teams', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('memberships', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('role');
            $table->foreignIdFor(Team::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique([new Team()->getForeignKey(), new User()->getForeignKey()]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
        Schema::dropIfExists('memberships');
    }
};
