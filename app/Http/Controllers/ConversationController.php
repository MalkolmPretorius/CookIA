<?php
// ConversationController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\User; // Assurez-vous d'importer le modèle User si ce n'est pas déjà fait

class ConversationController extends Controller
{
    public function store(Request $request)
    {
        // Récupérer l'utilisateur actuellement connecté
        $user = auth()->user();

        // Créer une nouvelle conversation
        $conversation = new Conversation();
        $conversation->user_id = $user->id;
        $conversation->save();

        // Enregistrer chaque message de la conversation dans chathistory
        foreach ($request->messages as $message) {
            \App\Models\ChatHistory::create([
                'user_id' => $user->id,
                'conversation_id' => $conversation->id,
                'message' => $message['content'],
                'sender' => $message['role'],
            ]);
        }

        return response()->json(['message' => 'Conversation saved successfully'], 201);
    }
}

