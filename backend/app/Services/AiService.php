<?php

namespace App\Services;

use App\Models\Message;
use App\Interfaces\IAiService;
use App\Events\HelpdeskMessageEvent;
use Illuminate\Support\Facades\Http;

class AiService implements IAiService {
  public function handleUserMessage(Message $user_message): void {

    $system_prompt = <<<END
    You are a kind assistant, and answer the questions shortly.
    END;

    $prompt = $system_prompt . "\nUser: " . $user_message->text;

    $response = Http::post(config('app.ai_url') . '/api/generate', [
      "model" => "mistral",
      "prompt" => $prompt,
      "stream" => false
    ]);

    $response_json = (array)json_decode($response->body());

    $ai_message = new Message([
      "text" => $response_json['response'],
      "chat_id" => $user_message->chat_id
    ]);

    $ai_message->save();

    broadcast(new HelpdeskMessageEvent($ai_message));
  }
}
