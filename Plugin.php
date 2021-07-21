<?php

namespace Waka\Support;

use Waka\Support\Models\Ticket;
use System\Classes\PluginBase;
use Backend;
use Event;

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
        \Event::subscribe(new \Waka\Support\Listeners\WorkflowTicketListener());
        
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
                'label'       => 'waka.support::lang.settings.label',
                'description' => 'waka.support::lang.settings.description',
                'category'    => 'waka.support::lang.settings.category',
                'icon'        => 'icon-life-ring',
                'class'       => 'Waka\Support\Models\Settings',
                'order'       => 500,
                'keywords'    => 'support help',
                'permissions' => ['waka.support.access_settings']
            ],
             'TicketTypes' => [
                'label'       => 'waka.support::lang.settings.label',
                'description' => 'waka.support::lang.settings.description',
                'category'    => 'waka.support::lang.settings.category',
                'icon'        => 'icon-gear',
                'url' => Backend::url('wcli/crpf/payss'),
                'order'       => 500,
                'keywords'    => 'support help',
                'permissions' => ['waka.support.admin.super']
            ]
        ];
    }

}
