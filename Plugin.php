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
            '/waka/support/config/ticket_w.yaml',
        ];
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
                'label'       => \Lang::get('waka.support::lang.navigation.support'),
                'icon'        => 'icon-bug',
                'url'         => Backend::url('waka/support/tickets'),
                'permissions' => ['waka.support.*'],
                'order'       => 600,
                'sideMenu' => [
                    'side-menu-tickets' => [
                        'label' => \Lang::get('waka.support::lang.navigation.support'),
                        'icon' => 'icon-bug',
                        'url' => Backend::url('waka/support/tickets'),
                        'permissions' => ['waka.support.*'],
                        'counter' => \Waka\Support\Models\Ticket::countScope('userCounter'),
                    ],
                    'side-menu-ticketgroups' => [
                        'label' => \Lang::get('waka.support::lang.settings.label_ticket_groupes'),
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
            'waka.support.user.base'        => [
                'label' => \Lang::get('waka.support::lang.permissions.user'),
                'tab' => \Lang::get('waka.support::lang.permissions.tab')
            ],
            'waka.support.user.super'        => [
                'label' => \Lang::get('waka.support::lang.permissions.user_super'),
                'tab' => \Lang::get('waka.support::lang.permissions.tab')
            ],
            'waka.support.admin.base'         => [
                'label' => \Lang::get('waka.support::lang.permissions.admin_base'),
                'tab'   => \Lang::get('waka.support::lang.permissions.tab')
            ],
            'waka.support.admin.super'    => [
                'label' => \Lang::get('waka.support::lang.permissions.admin_super'),
                'tab'   => \Lang::get('waka.support::lang.permissions.tab')
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
                'label'       => \Lang::get('waka.support::lang.settings.label_support_settings'),
                'description' => \Lang::get('waka.support::lang.settings.description'),
                'category'    => \Lang::get('waka.support::lang.settings.category'),
                'icon'        => 'icon-bug',
                'class'       => 'Waka\Support\Models\Settings',
                'order'       => 500,
                'keywords'    => 'support help',
                'permissions' => ['waka.support.access_settings']
            ],
             'TicketTypes' => [
                'label'       => \Lang::get('waka.support::lang.settings.label_ticket_types'),
                'description' => \Lang::get('waka.support::lang.settings.types_description'),
                'category'    => \Lang::get('waka.support::lang.settings.category'),
                'icon'        => 'icon-gear',
                'url' => Backend::url('waka/support/tickettypes'),
                'order'       => 501,
                'keywords'    => 'support help',
                'permissions' => ['waka.support.admin.super']
            ],
            
        ];
    }

}
