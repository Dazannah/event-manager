<?php

namespace App\Services;

use App\Models\Message;
use App\Interfaces\IAiService;
use App\Jobs\AiChatMessageJob;
use App\Events\ChatMessageEvent;
use App\Events\HelpdeskMessageEvent;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Collection;

class AiService implements IAiService {
  public function handleUserMessage(Message $user_message, Collection $events): void {
    $json_events = json_encode($events);

    $system_prompt = <<<END
    You are a kind assistant, and answer the questions shortly.
    END;

    $prompt = $system_prompt . "\nUser events: " . $json_events  . "\nUser question: " . $user_message->text;


    AiChatMessageJob::dispatch($prompt, $user_message->chat_id);
  }
}
