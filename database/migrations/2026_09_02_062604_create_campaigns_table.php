<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained('blogs')->cascadeOnDelete();
            $table->string('name');
            
            $table->string('source_type')->default('google_trends');
            $table->string('geo_location')->default('ID');
            $table->string('custom_keyword')->nullable();
            
            $table->integer('posts_per_day')->default(5);
            $table->integer('interval_minutes')->default(120);
            
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_run_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};