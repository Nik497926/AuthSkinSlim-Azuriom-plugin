<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_skin_slim', function (Blueprint $table) {
            $table->id();
            // Must match users.id: Azuriom uses increments() = UNSIGNED INT, not foreignId() (BIGINT).
            $table->unsignedInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->boolean('is_slim')->default(false);
            /** @var int Unix mtime of skins/{id}.png when is_slim was computed (invalidate on re-upload) */
            $table->unsignedBigInteger('skin_mtime');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_skin_slim');
    }
};
