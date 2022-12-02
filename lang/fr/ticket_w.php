<?php
return [
    'name' => 'Gestion des taches',
    'com' => 'Un commentaire sur la gestion des taches',
    'places' => [
        'draft' => 'Brouillon',
        'wait_support' => 'Retour support attendu',
        'wait_managment' => 'Retour client attendu',
        'en_cours' => 'En production',
        'validated' => 'Ticket validé',
        'sleep' => 'En sommeil',
        'archived' => 'Archivé',
        'abdn' => 'Abandon du ticket',
    ],
    'trans' => [
        'draft_to_wait_support' => 'Transmettre au support',
        'draft_to_wait_managment' => 'Transmettre au  client',
        'draft_to_sleep' => 'Mettre en someil',
        'draft_to_abdn' => 'Abandonner le ticket',
        'draft_to_validated' => 'Valider et clôturer le ticket',
        'wait_support_to_wait_managment' => 'Envoyer au client',
        'wait_support_to_sleep' => 'Mise en sommeil du ticket',
        'wait_support_to_abdn' => 'Abandonner le ticket',
        'wait_support_to_en_cours' => 'En cours de production',
        'en_cours_to_wait_support' => 'FIN prod (retour support attendu)',
        'en_cours_to_wait_managment' => 'FIN prod (retour client attendu)',
        'wait_managment_to_wait_support' => 'Répondre au support',
        'wait_managment_to_validated' => 'Valider et clôturer le ticket',
        'wait_managment_to_sleep' => 'Mise en sommeil du ticket',
        'wait_managment_to_abdn' => 'Abandonner le ticket',
        'sleep_to_wait_support' => 'Réveiller + transmission support',
        'sleep_to_wait_managment' => 'Réveiller + transmission client',
        'sleep_to_validated' => 'Réveiller et valider',
        'sleep_to_abdn' => 'Abandonner ticket',
        'validated_to_archived' => 'Archivage du ticket',
    ],
    'error_message' => [
        'default' => '',
        'none' => 'Aucune validation en dehors des règles du levier.',
        'factu' => 'Le champ réveil et date de réveil doivent être remplis',
    ],
];