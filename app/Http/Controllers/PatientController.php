<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $patients = Patient::orderBy('last_name')->get();

            return response()->json([
                'success' => true,
                'message' => 'Liste des patients récupérée avec succès',
                'data' => $patients,
                'count' => $patients->count()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des patients: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des patients',
                'error' => 'Une erreur interne s\'est produite'
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'last_name' => 'required|string|max:100',
                'first_name' => 'required|string|max:100',
                'birth_date' => 'required|date|before:today',
                'gender' => 'required|in:M,F',
                'address' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'nullable|email|max:100|unique:patients,email',
                'emergency_contact_name' => 'required|string|max:100',
                'emergency_contact_phone' => 'required|string|max:20',
            ], [
                'last_name.required' => 'Le nom de famille est obligatoire',
                'first_name.required' => 'Le prénom est obligatoire',
                'birth_date.required' => 'La date de naissance est obligatoire',
                'birth_date.before' => 'La date de naissance doit être antérieure à aujourd\'hui',
                'gender.required' => 'Le genre est obligatoire',
                'gender.in' => 'Le genre doit être M ou F',
                'address.required' => 'L\'adresse est obligatoire',
                'phone.required' => 'Le numéro de téléphone est obligatoire',
                'email.email' => 'L\'email doit être valide',
                'email.unique' => 'Cet email est déjà utilisé',
                'emergency_contact_name.required' => 'Le nom du contact d\'urgence est obligatoire',
                'emergency_contact_phone.required' => 'Le téléphone du contact d\'urgence est obligatoire',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $patient = Patient::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Patient créé avec succès',
                'data' => $patient
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du patient: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du patient',
                'error' => 'Une erreur interne s\'est produite'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient): JsonResponse
    {
        try {
            Log::info('Requête reçue pour afficher le patient', ['id' => $patient->id]);

            return response()->json([
                'success' => true,
                'message' => 'Patient trouvé',
                'data' => $patient
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Patient non trouvé',
                'error' => 'Le patient demandé n\'existe pas'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du patient: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du patient',
                'error' => 'Une erreur interne s\'est produite'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'last_name' => 'sometimes|required|string|max:100',
                'first_name' => 'sometimes|required|string|max:100',
                'birth_date' => 'sometimes|required|date|before:today',
                'gender' => 'sometimes|required|in:M,F',
                'address' => 'sometimes|required|string|max:255',
                'phone' => 'sometimes|required|string|max:20',
                'email' => 'nullable|email|max:100|unique:patients,email,' . $patient->id,
                'emergency_contact_name' => 'sometimes|required|string|max:100',
                'emergency_contact_phone' => 'sometimes|required|string|max:20',
            ], [
                'last_name.required' => 'Le nom de famille est obligatoire',
                'first_name.required' => 'Le prénom est obligatoire',
                'birth_date.required' => 'La date de naissance est obligatoire',
                'birth_date.before' => 'La date de naissance doit être antérieure à aujourd\'hui',
                'gender.required' => 'Le genre est obligatoire',
                'gender.in' => 'Le genre doit être M ou F',
                'address.required' => 'L\'adresse est obligatoire',
                'phone.required' => 'Le numéro de téléphone est obligatoire',
                'email.email' => 'L\'email doit être valide',
                'email.unique' => 'Cet email est déjà utilisé par un autre patient',
                'emergency_contact_name.required' => 'Le nom du contact d\'urgence est obligatoire',
                'emergency_contact_phone.required' => 'Le téléphone du contact d\'urgence est obligatoire',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $patient->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Patient mis à jour avec succès',
                'data' => $patient
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Patient non trouvé',
                'error' => 'Le patient à modifier n\'existe pas'
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du patient: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du patient',
                'error' => 'Une erreur interne s\'est produite'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient): JsonResponse
    {
        try {
            $patientName = $patient->first_name . ' ' . $patient->last_name;
            $patient->delete();

            return response()->json([
                'success' => true,
                'message' => 'Patient supprimé avec succès',
                'data' => [
                    'deleted_patient' => $patientName
                ]
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Patient non trouvé',
                'error' => 'Le patient à supprimer n\'existe pas'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du patient: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du patient',
                'error' => 'Une erreur interne s\'est produite'
            ], 500);
        }
    }

    /**
     * Search patients by name or email
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:2|max:100'
            ], [
                'query.required' => 'Le terme de recherche est obligatoire',
                'query.min' => 'Le terme de recherche doit contenir au moins 2 caractères',
                'query.max' => 'Le terme de recherche ne peut pas dépasser 100 caractères'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terme de recherche invalide',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = $request->input('query');

            $patients = Patient::where('last_name', 'LIKE', "%{$query}%")
                ->orWhere('first_name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->orderBy('last_name')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Recherche terminée',
                'data' => $patients,
                'count' => $patients->count(),
                'search_term' => $query
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche de patients: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche',
                'error' => 'Une erreur interne s\'est produite'
            ], 500);
        }
    }

    /**
     * Get patients by gender
     */
    public function getByGender(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'gender' => 'required|in:M,F'
            ], [
                'gender.required' => 'Le genre est obligatoire',
                'gender.in' => 'Le genre doit être M ou F'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Genre invalide',
                    'errors' => $validator->errors()
                ], 422);
            }

            $gender = $request->input('gender');
            $patients = Patient::where('gender', $gender)
                ->orderBy('last_name')
                ->get();

            $genderLabel = $gender === 'M' ? 'Masculin' : 'Féminin';

            return response()->json([
                'success' => true,
                'message' => "Patients de genre {$genderLabel} récupérés",
                'data' => $patients,
                'count' => $patients->count(),
                'gender' => $genderLabel
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des patients par genre: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des patients',
                'error' => 'Une erreur interne s\'est produite'
            ], 500);
        }
    }
}
