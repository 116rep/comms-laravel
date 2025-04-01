<?php
namespace App\Listeners;

use App\Events\VoiceChatEvent;
use App\Models\VoiceChatEvent as VoiceChatEventModel;

class LogVoiceChatEvent
{
    public function handle(VoiceChatEvent $event)
    {
        VoiceChatEventModel::create([
            "user_id" => $event->userId,
            "action" => $event->action,
        ]);
    }
}
