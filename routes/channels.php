<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
 
    if (!$conversation) {
        return false;
    }
    return $conversation->isParticipant($user->id);
});

Broadcast::channel('reservations.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});