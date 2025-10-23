<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('versions', function (Blueprint $table) {
            $table->id();

            // Polymorphic user relationship
            $table->nullableMorphs('user');

            // Versionable model relationship
            $table->morphs('versionable');

            // Version contents
            $table->json('contents')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['versionable_id', 'versionable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versions');
    }
};
