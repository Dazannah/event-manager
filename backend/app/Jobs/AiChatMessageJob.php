<?php

namespace App\Jobs;

use App\Models\Message;
use App\Events\ChatMessageEvent;
use App\Events\HelpdeskMessageEvent;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class AiChatMessageJob implements ShouldQueue {
    use Queueable;

    public string $prompt;
    public int $chat_id;

    /**
     * Create a new job instance.
     */
    public function __construct($prompt, $chat_id) {
        $this->prompt = $prompt;
        $this->chat_id = $chat_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        $response = Http::timeout(120)->post(config('app.ai_url') . '/api/generate', [
            "model" => "llama3.2:3b",
            "prompt" => $this->prompt,
            "stream" => false
        ]);

        $response_json = (array)json_decode($response->body());

        $ai_message = new Message([
            "text" => $response_json['response'],
            "chat_id" => $this->chat_id
        ]);

        $ai_message->save();

        broadcast(new HelpdeskMessageEvent($ai_message));
        broadcast(new ChatMessageEvent($ai_message));
    }
}
