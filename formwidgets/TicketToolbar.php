<?php

namespace Waka\Support\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Waka\Support\Models\TicketStatus;

/**
 * Class TicketToolbar
 * @package Waka\Support\FormWidgets
 */
class TicketToolbar extends FormWidgetBase
{

    /**
     * @inheritdoc
     */
    protected $defaultAlias = 'waka_support_ticket_toolbar';

    /**
     * @inheritdoc
     */
    public function render()
    {
        $this->prepareVars();

        return $this->makePartial('tickettoolbar');
    }

    /**
     * @return void
     */
    public function prepareVars()
    {
        $this->vars['statuses'] = TicketStatus::active()->get()->lists('name', 'id');
        $this->vars['model'] = $this->model;
    }

}
