<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorsDomainsTable extends Migration
{
    public function up(): void
    {
        Schema::create('cors_domains', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cors_domains');
    }
}
