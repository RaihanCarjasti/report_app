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
        Schema::create("documents", function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("name");
            $table->string("path");
            $table->string("type");
            $table->boolean("passed")->default(false);
            $table->foreignId("task_id")->constrained("tasks")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("documents");
    }
};
