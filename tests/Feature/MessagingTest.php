<?php

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('property owners can see and open conversations for their properties', function () {
    $tenant = User::factory()->create(['role' => 'tenant']);
    $realOwner = User::factory()->create(['role' => 'owner']);
    $otherOwner = User::factory()->create(['role' => 'owner']);
    $property = Property::factory()->create(['user_id' => $realOwner->id]);

    $conversation = Conversation::create([
        'tenant_id' => $tenant->id,
        'owner_id' => $otherOwner->id,
        'property_id' => $property->id,
    ]);

    Message::create([
        'conversation_id' => $conversation->id,
        'sender_id' => $tenant->id,
        'contenu' => 'Bonjour',
        'date_envoie' => now(),
        'est_lu' => false,
    ]);

    Sanctum::actingAs($realOwner);

    $this->getJson('/api/conversations')
        ->assertOk()
        ->assertJsonPath('total', 1)
        ->assertJsonPath('conversations.0.id', $conversation->id);

    $this->getJson("/api/conversations/{$conversation->id}")
        ->assertOk()
        ->assertJsonPath('conversation.id', $conversation->id);
});
