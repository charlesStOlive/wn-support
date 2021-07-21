<?php

namespace Renatio\Support\Updates;

use Waka\Support\Models\TicketType;
use Seeder;

/**
 * Class SeedTicketTypesTable
 * @package Renatio\Support\Updates
 */
class SeedTicketTypesTable extends Seeder
{

    /**
     * @return void
     */
    public function run()
    {
        TicketType::create([
            'name'        => 'Bug',
            'description' => 'Bug dans le cadre d un projet en cours de dev',
            'is_facturable' => false,
        ]);
        TicketType::create([
            'name'        => "Projet",
            'description' => 'Tâche liée à un projet en cours',
            'is_facturable' => false,
            'is_for_super_user' => true,
        ]);
        TicketType::create([
            'name'        => 'Assistance',
            'description' => 'Assistance.',
            'is_facturable' => true,
        ]);
        TicketType::create([
            'name'        => "Demande d'evolution",
            'description' => 'Assistance.',
            'is_facturable' => true,
        ]);
        TicketType::create([
            'name'        => "Proposition évolution",
            'description' => 'Assistance.',
            'is_facturable' => true,
            'is_for_super_user' => true,
        ]);
        
    }

}
