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
        Schema::create('otp_tokens', function (Blueprint $t) {
            $t->id();
            $t->string('channel'); // 'sms' or 'email'
            $t->string('identifier'); // phone number or email
            $t->string('purpose'); // 'signup','login','password_reset'
            $t->string('code_hash');
            $t->timestamp('expires_at');
            $t->unsignedTinyInteger('attempts')->default(0);
            $t->boolean('consumed')->default(false);
            $t->timestamps();
            $t->index(['identifier','purpose','expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_tokens');
    }
};
