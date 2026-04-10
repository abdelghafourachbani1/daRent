<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'contenu',
        'date_envoie',
        'est_lu',
    ];
 
    protected $casts = [
        'est_lu'      => 'boolean',
        'date_envoie' => 'datetime',
    ];

    public function markAsRead(): void {
        $this->update(['est_lu' => true]);
    }

    public function deleteMessage(): void {
        $this->delete();
    }
 
    public function conversation() {
        return $this->belongsTo(Conversation::class);
    }
 
    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
 
