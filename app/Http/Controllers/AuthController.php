<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        Log::info('Requête reçue pour tentative de connexion', ['ip' => $request->ip()]);

        try {
            // Validation
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Authentification
            if (!Auth::attempt($validated)) {
                Log::warning('Tentative de connexion échouée', ['email' => $validated['email']]);
                return response()->json(['message' => 'Identifiants invalides'], 401);
            }

            // Créer un token Sanctum
            $user = Auth::user();
            $token = $user->createToken('auth-token')->plainTextToken;

            Log::info('Connexion réussie', ['email' => $validated['email']]);
            return response()->json([
                'message' => 'Connecté avec succès',
                'token' => $token,
                'user' => $user
            ]);
        } catch (ValidationException $e) {
            // Erreurs de validation
            Log::error('Erreur de validation lors de la connexion', [
                'errors' => $e->errors(),
                'ip' => $request->ip()
            ]);
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            // Toute autre erreur
            Log::error('Erreur serveur lors de la tentative de connexion', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Erreur serveur. Veuillez réessayer plus tard.'
            ], 500);
        }
    }

    public function user(Request $request)
    {
        Log::info('Récupération des informations de l\'utilisateur', ['user_id' => $request->user()->id]);
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        // Révoquer le token actuel
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['message' => 'Déconnecté']);
    }
}
