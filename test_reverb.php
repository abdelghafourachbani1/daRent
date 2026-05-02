<?php
$c = App\Models\Conversation::find(1);
$m = App\Models\Message::create([
    'conversation_id' => 1,
    'sender_id'       => $c->owner_id,
    'contenu'         => 'HELLO FROM THE OTHER SIDE ' . time(),
    'date_envoie'     => now(),
    'est_lu'          => false,
]);
broadcast(new App\Events\MessageSent($m))->toOthers();
echo "Message broadcasted.\n";
