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
        Event::subscribe(new \Waka\Support\Listeners\WorkflowTicketListener());

        // Event::listen('backend.form.extendFields', function ($widget) {

        //     //trace_log('yo');
        //     if (!$widget->getController() instanceof \System\Controllers\Settings) {
        //         return;
        //     }

        //     // Only for the Wconfig Settings
        //     if (!$widget->model instanceof Settings) {
        //         return;
        //     }

        //     if ($widget->isNested === true) {
        //         return;
        //     }
        //     $widget->addTabFields(\Yaml::parseFile(plugins_path() . '/waka/support/models/settings/fields.yaml'));

        //     // $widget->addTabFields([
        //     //     'sf_responsable' => [
        //     //         'tab' => 'Support',
        //     //         'label' => "Collaborateurs recevant l'email de bilan Sales Force",
        //     //         'type' => 'taglist',
        //     //         'mode' => 'array',
        //     //         'useKey' => 'true',
        //     //         'options' => 'listUsers',
        //     //     ],
        //     //     'sf_active_imports' => [
        //     //         'tab' => 'Support',
        //     //         'label' => 'waka.salesforce::lang.settings.active_imports',
        //     //         'type' => 'checkboxlist',
        //     //         'quickselect' => true,
        //     //         'options' => 'listImports',
        //     //     ],

        //     //     'sf_oldest_date' => [
        //     //         'tab' => 'Support',
        //     //         'label' => 'waka.salesforce::lang.settings.oldest_date',
        //     //         'type' => 'datepicker',
        //     //     ],
        //     //     'sf_cron_time' => [
        //     //         'tab' => 'Support',
        //     //         'label' => "Heure d'execution du CRON",
        //     //         'type' => 'datepicker',
        //     //         'mode' => 'time',
        //     //         'span' => 'left',
        //     //         'width' => '100px',
        //     //     ],
        //     // ]);
        // });
        
    }

    public function registerSchedule($schedule)
    {

        $schedule->call(function () {
            $support_team = Settings::getSupportUsers();
            trace_log(Carbon::parse(Settings::get('recap_team_cron'))->format('H:i'));
            foreach ($support_team as $userId) {
                \Waka\Mailer\Classes\MailCreator::find('waka.support::client_team', true)->setModelId($userId)->renderMail();
            }

            $client_team = Settings::getClientManagers();
            foreach ($client_team as $userId) {
                \Waka\Mailer\Classes\MailCreator::find('waka.support::client_team', true)->setModelId($userId)->renderMail();
            }
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
                'icon'        => 'icon-life-ring',
                'url'         => Backend::url('waka/support/tickets'),
                'permissions' => ['waka.support.*'],
                'order'       => 600,
                'counter' => \Waka\Support\Models\Ticket::getMenucounter(),
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
                'icon'        => 'icon-life-ring',
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
            ]
        ];
    }

}
