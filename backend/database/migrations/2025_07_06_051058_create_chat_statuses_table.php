<?php

use App\Models\ChatStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('chat_statuses', function (Blueprint $table) {
            $table->id();
            $table->text('technical_name');
            $table->text('display_name');
        });

        ChatStatus::create([
            'technical_name' => 'open',
            'display_name' => 'Open'
        ]);

        ChatStatus::create([
            'technical_name' => 'closed',
            'display_name' => 'Closed'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('chat_statuses');
    }
};
