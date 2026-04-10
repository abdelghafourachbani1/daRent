<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MessagingController extends Controller
{

    public function createConversation(Request $request): JsonResponse {
            $request->validate([
                'property_id' => 'required|exists:properties,id',
                'message'     => 'required|string|max:2000',
            ]);
    
            $property = Property::findOrFail($request->property_id);
    
            if (!$request->user()->isTenant()) {
                return response()->json([
                    'message' => 'Seul un locataire peut initier une conversation.',
                ], 403);
            }
    
            if ($property->user_id === $request->user()->id) {
                return response()->json([
                    'message' => 'Vous ne pouvez pas envoyer un message pour votre propre propriété.',
                ], 422);
            }
    
            $conversation = Conversation::firstOrCreate([
                'tenant_id'   => $request->user()->id,
                'owner_id'    => $property->user_id,
                'property_id' => $property->id,
            ]);
    
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $request->user()->id,
                'contenu'         => $request->message,
                'date_envoie'     => now(),
                'est_lu'          => false,
            ]);

            broadcast(new MessageSent($message))->toOthers();
    
            $conversation->load(['property:id,titre', 'owner:id,nom', 'tenant:id,nom']);
    
            return response()->json([
                'message'      => 'Conversation créée avec succès',
                'conversation' => $conversation,
                'first_message'=> $message->load('sender:id,nom,avatar'),
            ], 201);
    }
 
    public function getConversations(Request $request): JsonResponse {
        $userId = $request->user()->id;
 
        $conversations = Conversation::where('tenant_id', $userId)
            ->orWhere('owner_id', $userId)
            ->with([
                'property:id,titre,adress',
                'tenant:id,nom,avatar',
                'owner:id,nom,avatar',
                'latestMessage',
            ])
            ->withCount(['messages as unread_count' => function ($q) use ($userId) {
                $q->where('est_lu', false)
                  ->where('sender_id', '!=', $userId);
            }])
            ->latest()
            ->get();
 
        return response()->json([
            'conversations' => $conversations,
            'total'         => $conversations->count(),
        ]);
    }
 
    public function getConversation(Request $request, Conversation $conversation): JsonResponse {

        if (!$conversation->isParticipant($request->user()->id)) {
            return response()->json([
                'message' => 'Accès refusé.',
            ], 403);
        }
 
        $messages = Message::where('conversation_id', $conversation->id)
            ->with('sender:id,nom,avatar')
            ->orderBy('created_at', 'asc')
            ->get();
 
        Message::where('conversation_id', $conversation->id)
               ->where('sender_id', '!=', $request->user()->id)
               ->where('est_lu', false)
               ->update(['est_lu' => true]);
 
        $conversation->load(['property:id,titre,adress', 'tenant:id,nom', 'owner:id,nom']);
 
        return response()->json([
            'conversation' => $conversation,
            'messages'     => $messages,
        ]);
    }

    public function sendMessage(Request $request): JsonResponse {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'contenu'         => 'required|string|max:2000',
        ]);
 
        $conversation = Conversation::findOrFail($request->conversation_id);
 
        if (!$conversation->isParticipant($request->user()->id)) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }
 
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $request->user()->id,
            'contenu'         => $request->contenu,
            'date_envoie'     => now(),
            'est_lu'          => false,
        ]);
 
        $message->load('sender:id,nom,avatar');

        broadcast(new MessageSent($message))->toOthers();
 
        return response()->json([
            'message' => 'Message envoyé',
            'data'    => $message,
        ], 201);
    }

    public function getMessages(Request $request, int $conversationId): JsonResponse {
        $conversation = Conversation::findOrFail($conversationId);
 
        if (!$conversation->isParticipant($request->user()->id)) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }
 
        $messages = Message::where('conversation_id', $conversationId)
            ->with('sender:id,nom,avatar')
            ->orderBy('created_at', 'asc')
            ->paginate(50);
 
        return response()->json($messages);
    }

    public function deleteMessage(Request $request, Message $message): JsonResponse {
        if ($message->sender_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }
 
        $message->deleteMessage();
 
        return response()->json(['message' => 'Message supprimé']);
    }
}
 

