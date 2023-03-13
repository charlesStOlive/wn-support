<?php

namespace Waka\Support;

use Waka\Support\Models\Ticket;
use System\Classes\PluginBase;
use Backend;
use Event;
use Waka\Support\Models\Settings;
use Carbon\Carbon;

/**
 * Support Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'waka.support::lang.plugin.name',
            'description' => 'waka.support::lang.plugin.description',
            'author'      => 'Renatio',
            'icon'        => 'icon-life-ring',
        ];
    }

    /**
     * Boot plugin
     */

    public function boot()
    {
        \DataSources::registerDataSources(plugins_path().'/waka/support/config/datasources.yaml');
        Event::subscribe(new \Waka\Support\Listeners\WorkflowTicketListener());
    }

    public function registerWorkflows() {
        return [
            'ticket' => '/waka/support/config/ticket.yaml',
        ];
    }

    public function registerSchedule($schedule)
    {
        //trace_log(Settings::get('recap_team_cron'));
        if(!Settings::get('recap_team_cron')) {
            //Il n' y a pas de date cela rique d'engendrer un email par minute
            return;
        }
        // if(!Settings::get('activate_bilans')) {
        //     //On bloque si desactivé
        //     return;
        // }



        $schedule->call(function () {
            //Partie 1 : réactivation des tâches endormis
            $sleepIngTickets = \Waka\Support\Models\Ticket::where('state', 'sleep')->whereDate('awake_at' ,'<=', \Carbon\Carbon::now());
            //trace_log($sleepIngTickets->count());
            foreach($sleepIngTickets->get() as $ticketToOpen) {
                //trace_log($ticketToOpen->wakaWorkflowCan('sleep_to_wait_support'));
                if($ticketToOpen->wakaWorkflowCan('sleep_to_wait_support')) {
                    $ticketToOpen->workflow_apply('sleep_to_wait_support');
                    $ticketToOpen->save();
                }
                //trace_log($ticketToOpen->wakaWorkflowCan('sleep_to_wait_managment'));
                if($ticketToOpen->wakaWorkflowCan('sleep_to_wait_managment')) {
                    $ticketToOpen->workflow_apply('sleep_to_wait_managment');
                    $ticketToOpen->save();
                }
            }
            //trace_log('call support');
            //trace_log('call support');
            // $support_team = Settings::getSupportUsers();
            // //trace_log(Carbon::parse(Settings::get('recap_team_cron'))->format('H:i'));
            // foreach ($support_team as $userId) {
            //     //trace_log($userId." : Utilisateur");
                
            //     $countNext = \Waka\Support\Models\Ticket::opened()->nextUser($userId)->count();
            //     //trace_log($countNext);
            //     //trace_log(\Waka\Support\Models\Ticket::opened()->get()->toArray());
            //     if($countNext) {
            //         \Waka\Mailer\Classes\MailCreator::find('waka.support::client_team', true)->setModelId($userId)->renderMail();
            //     }   
            //     //\Waka\Mailer\Classes\MailCreator::find('waka.support::client_team', true)->setModelId($userId)->renderMail();
            // }

            // $client_team = Settings::get('client_manage_team');
            // //trace_log($client_team);
            // foreach ($client_team as $client) {
            //     //trace_log($client['id']." : Utilisateur");
            //     //trace_log($client['receive_recap']);
            //     if($client['receive_recap']) {
            //         $countNext = \Waka\Support\Models\Ticket::opened()->nextUser($client['id'])->count();
            //         if($countNext) {
            //             \Waka\Mailer\Classes\MailCreator::find('waka.support::client_team', true)->setModelId($client['id'])->renderMail();
            //         }   
            //     }
            //     //
            // }
        })->dailyAt(Carbon::parse(Settings::get('recap_team_cron'))->format('H:i'));


    }

    /**
     * Register backend navigation.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'support' => [
                'label'       => 'waka.support::lang.navigation.support',
                'icon'        => 'icon-bug',
                'url'         => Backend::url('waka/support/tickets'),
                'permissions' => ['waka.support.*'],
                'order'       => 600,
                'sideMenu' => [
                    'side-menu-tickets' => [
                        'label' => 'waka.support::lang.navigation.support',
                        'icon' => 'icon-bug',
                        'url' => Backend::url('waka/support/tickets'),
                        'permissions' => ['waka.support.*'],
                        'counter' => \Waka\Support\Models\Ticket::countScope('nextUser'),
                    ],
                    'side-menu-ticketgroups' => [
                        'label' => 'waka.support::lang.settings.label_ticket_groupes',
                        'icon' => 'icon-gear',
                        'url' => Backend::url('waka/support/ticketgroups'),
                        'permissions' => ['waka.support.*']
                    ],
                ]
            ],
        ];
    }

    /**
     * Register permissions.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'waka.support.user'        => [
                'label' => 'waka.support::lang.permissions.user',
                'tab'   => 'waka.support::lang.permissions.tab'
            ],
            'waka.support.admin.base'         => [
                'label' => 'waka.support::lang.permissions.admin_base',
                'tab'   => 'waka.support::lang.permissions.tab'
            ],
            'waka.support.admin.super'    => [
                'label' => 'waka.support::lang.permissions.admin_super',
                'tab'   => 'waka.support::lang.permissions.tab'
            ],
            'waka.support.access_ticket_types'    => [
                'label' => 'waka.support::lang.permissions.ticket_types',
                'tab'   => 'waka.support::lang.permissions.tab'
            ],
            
        ];
    }

    public function registerWakaRules()
    {
        return [
            'asks' => [],
            'fncs' => [
                ['Waka\Support\WakaRules\Fncs\Tickets'],
            ],
        ];
    }

    /**
     * Register form widgets
     *
     * @return array
     */
    public function registerFormWidgets()
    {
        return [
            'Waka\Support\FormWidgets\TicketToolbar'  => [
                'label' => 'waka.support::lang.ticket.toolbar',
                'code'  => 'ticket_toolbar'
            ],
            'Waka\Support\FormWidgets\TicketMessages' => [
                'label' => 'waka.support::lang.ticket.messages',
                'code'  => 'ticket_messages'
            ]
        ];
    }

    public function registerReportWidgets()
    {
        return [
            'Waka\Support\ReportWidgets\ReportSupport' => [
                'label'   => 'Résumé dernier tickets',
                'context' => 'dashboard',
                'permissions' => [
                    'waka.support.*',
                ],
            ],
            'Waka\Support\ReportWidgets\ReportTickets' => [
                'label'   => 'liste de tickets utilisateurs',
                'context' => 'dashboard',
                'permissions' => [
                    'waka.support.admin',
                ],
            ],
        ];
    }

    /**
     * Register mail templates
     *
     * @return array
     */
    public function registerMailTemplates()
    {
        return [
            'waka.support::mail.new_ticket'    => 'New ticket mail to support team.',
            'waka.support::mail.new_reply'     => 'New reply message for ticket.',
            'waka.support::mail.ticket_closed' => 'Close ticket mail.'
        ];
    }

    /**
     * Register settings.
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'support_settings' => [
                'label'       => 'waka.support::lang.settings.label_support_settings',
                'description' => 'waka.support::lang.settings.description',
                'category'    => 'waka.support::lang.settings.category',
                'icon'        => 'icon-bug',
                'class'       => 'Waka\Support\Models\Settings',
                'order'       => 500,
                'keywords'    => 'support help',
                'permissions' => ['waka.support.access_settings']
            ],
             'TicketTypes' => [
                'label'       => 'waka.support::lang.settings.label_ticket_types',
                'description' => 'waka.support::lang.settings.description',
                'category'    => 'waka.support::lang.settings.category',
                'icon'        => 'icon-gear',
                'url' => Backend::url('waka/support/tickettypes'),
                'order'       => 501,
                'keywords'    => 'support help',
                'permissions' => ['waka.support.admin.super']
            ],
            
        ];
    }

}
