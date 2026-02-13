<?php

namespace App\Observers;

use App\Models\Message;
use App\Services\IAService;

class MessageObserver
{
    
      // Déclenché automatiquement après création d’un message

    public function created(Message $message)
    {
        app(IAService::class)->analyserMessage($message);
    }
}
