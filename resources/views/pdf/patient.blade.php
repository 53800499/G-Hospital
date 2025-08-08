<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Fiche Patient</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }

        .section {
            margin-bottom: 20px;
        }

        .title {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="title">Fiche Patient</div>

    <div class="section">
        <strong>Nom :</strong> {{ $patient->last_name }}<br>
        <strong>Prénom :</strong> {{ $patient->first_name }}<br>
        <strong>Date de naissance :</strong> {{ $patient->birth_date }}<br>
        <strong>Date de naissance :</strong> {{ optional($patient->birth_date)->format('d/m/Y') }}<br>
        <strong>Sexe :</strong> {{ $patient->gender === 'M' ? 'Masculin' : 'Féminin' }}<br>
        <strong>Email :</strong> {{ $patient->email }}<br>
        <strong>Téléphone :</strong> {{ $patient->phone }}<br>
    </div>

    <div class="section">
        <strong>Adresse :</strong> {{ $patient->address }}<br>
        <strong>Contact urgence :</strong> {{ $patient->emergency_contact_name }}<br>
        <strong>Tél urgence :</strong> {{ $patient->emergency_contact_phone }}<br>
    </div>

    <div class="section">
        <strong>Date de création :</strong> {{ optional(value: $patient->created_at)->format('d/m/Y H:i') }}
    </div>
</body>

</html>
