<?php
namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoiceChatEvent
{
    use Dispatchable, SerializesModels;

    public $userId;
    public $action;

    public function __construct($userId, $action)
    {
        $this->userId = $userId;
        $this->action = $action;
    }
}
