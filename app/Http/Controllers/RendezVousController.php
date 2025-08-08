<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
class RendezVousController extends Controller
{

    /**
     * Affiche la liste des rendezVous.
     */
    public function index(): JsonResponse
    {
        try {
            $rendezVous = RendezVous::with('patient', 'user')
                ->orderByDesc('date_time')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Liste des rendezVous récupérée avec succès',
                'data' => $rendezVous,
                'count' => $rendezVous->count()
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des rendezVous: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des rendezVous',
                'error' => 'Une erreur interne s\'est produite'
            ], 500);
        }
    }

    /**
     * Enregistre une nouvelle rendez vous.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'patient_id' => 'required|exists:patients,id',
                'user_id' => 'required|exists:users,id',
                'date_time' => 'required|date',
                'reason' => 'required|string|max:255',
                'statut' => 'required|in:confirmed,canceled,postponed'
            ], [
                'patient_id.required' => 'Le patient est requis',
                'user_id.required' => 'Le médecin est requis',
                'date_time.required' => 'La date est obligatoire',
                'reason.required' => 'Le reason est obligatoire',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $rendezVous = RendezVous::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Rendez-vous créée avec succès',
                'data' => $rendezVous
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la rendez-vous: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la rendez-vous',
                'error' => 'Une erreur interne s\'est produite'
            ], 500);
        }
    }

    /**
     * Affiche un rendez-vous spécifique.
     */
    public function show($id): JsonResponse
    {
        try {
            $rendezVous = RendezVous::with('patient', 'user')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Rendez-vous trouvée',
                'data' => $rendezVous
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rendez-vous non trouvée',
                'error' => 'La rendez-vous demandée n\'existe pas'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de la Rendez-vous: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la Rendez-vous',
                'error' => 'Une erreur interne s\'est produite'
            ], 500);
        }
    }

    /**
     * Met à jour une Rendez-vous existante.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $rendezVous = RendezVous::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'patient_id' => 'required|exists:patients,id',
                'user_id' => 'required|exists:users,id',
                'date_time' => 'required|date',
                'reason' => 'required|string|max:255',
                'statut' => 'required|in:confirmed,canceled,postponed'
            ], [
                'patient_id.required' => 'Le patient est requis',
                'user_id.required' => 'Le médecin est requis',
                'date_time.required' => 'La date est obligatoire',
                'reason.required' => 'Le reason est obligatoire',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $rendezVous->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Rendez-vous mise à jour avec succès',
                'data' => $rendezVous
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'rendez-vous non trouvée',
                'error' => 'La rendez à modifier n\'existe pas'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la rendez-vous: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la rendez',
                'error' => 'Une erreur interne s\'est produite'
            ], 500);
        }
    }

    /**
     * Supprime une rendez.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $rendezVous = RendezVous::findOrFail($id);
            $rendezVous->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rendez-vous supprimée avec succès',
                'data' => [
                    'deleted_rendezVous_id' => $id
                ]
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rendez-vous non trouvée',
                'error' => 'La rendez-vous à supprimer n\'existe pas'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la rendez-vous: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la rendez-vous',
                'error' => 'Une erreur interne s\'est produite'
            ], 500);
        }
    }
}