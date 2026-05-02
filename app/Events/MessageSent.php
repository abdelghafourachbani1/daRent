<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    public function __construct(Message $message) {
        $this->message = $message->load([
            'sender:id,nom,avatar',
            'conversation:id,tenant_id,owner_id,property_id',
            'conversation.property:id,user_id',
        ]);
    }

    public function broadcastOn(): array {
        $conversation = $this->message->conversation;
        $recipientIds = collect([
            $conversation?->tenant_id,
            $conversation?->owner_id,
            $conversation?->property?->user_id,
        ])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        return [
            new PrivateChannel('conversation.' . $this->message->conversation_id),
            ...$recipientIds->map(fn ($id) => new PrivateChannel('App.Models.User.' . $id))->all(),
        ];
    }

    public function broadcastAs(): string {
        return 'MessageSent';
    }

    public function broadcastWith(): array {
        return [
            'message' => [
                'id'              => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'contenu'         => $this->message->contenu,
                'date_envoie'     => $this->message->date_envoie,
                'est_lu'          => $this->message->est_lu,
                'sender'          => [
                    'id'     => $this->message->sender->id,
                    'nom'    => $this->message->sender->nom,
                    'avatar' => $this->message->sender->avatar,
                ],
            ],
        ];
    }
}
