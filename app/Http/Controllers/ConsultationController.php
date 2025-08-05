<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ConsultationController extends Controller
{
    /**
     * Affiche la liste des consultations.
     */
    public function index(): JsonResponse
    {
        try {
            $consultations = Consultation::with('patient', 'user')
                ->orderByDesc('date_consultation')
                ->get();
            Log::info('Requête reçue pour récupérer la liste des consultations', [
                'count' => $consultations->all()
            ]);  

            return response()->json([
                'success' => true,
                'message' => 'Liste des consultations récupérée avec succès',
                'data' => $consultations,
                'count' => $consultations->count()
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des consultations: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des consultations',
                'error' => 'Une erreur interne s\'est produite'
            ], 500);
        }
    }

    /**
     * Enregistre une nouvelle consultation.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'patient_id' => 'required|exists:patients,id',
                'user_id' => 'required|exists:users,id',
                'date_consultation' => 'required|date',
                'motif' => 'required|string|max:255',
                'diagnostic' => 'nullable|string',
                'prescription' => 'nullable|string',
            ], [
                'patient_id.required' => 'Le patient est requis',
                'user_id.required' => 'Le médecin est requis',
                'date_consultation.required' => 'La date de consultation est obligatoire',
                'motif.required' => 'Le motif est obligatoire',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $consultation = Consultation::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Consultation créée avec succès',
                'data' => $consultation
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la consultation: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la consultation',
                'error' => 'Une erreur interne s\'est produite'
            ], 500);
        }
    }

    /**
     * Affiche une consultation spécifique.
     */
    public function show($id): JsonResponse
    {
        try {
            $consultation = Consultation::with('patient', 'user')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Consultation trouvée',
                'data' => $consultation
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Consultation non trouvée',
                'error' => 'La consultation demandée n\'existe pas'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de la consultation: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la consultation',
                'error' => 'Une erreur interne s\'est produite'
            ], 500);
        }
    }

    /**
     * Met à jour une consultation existante.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $consultation = Consultation::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'patient_id' => 'sometimes|required|exists:patients,id',
                'user_id' => 'sometimes|required|exists:users,id',
                'date_consultation' => 'sometimes|required|date',
                'motif' => 'sometimes|required|string|max:255',
                'diagnostic' => 'nullable|string',
                'prescription' => 'nullable|string',
            ], [
                'motif.required' => 'Le motif est obligatoire',
                'date_consultation.date' => 'La date doit être valide',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $consultation->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Consultation mise à jour avec succès',
                'data' => $consultation
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Consultation non trouvée',
                'error' => 'La consultation à modifier n\'existe pas'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la consultation: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la consultation',
                'error' => 'Une erreur interne s\'est produite'
            ], 500);
        }
    }

    /**
     * Supprime une consultation.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $consultation = Consultation::findOrFail($id);
            $consultation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Consultation supprimée avec succès',
                'data' => [
                    'deleted_consultation_id' => $id
                ]
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Consultation non trouvée',
                'error' => 'La consultation à supprimer n\'existe pas'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la consultation: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la consultation',
                'error' => 'Une erreur interne s\'est produite'
            ], 500);
        }
    }
}