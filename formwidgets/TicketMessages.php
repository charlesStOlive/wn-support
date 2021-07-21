<?php

namespace Waka\Support\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Waka\Support\Models\Ticket;

/**
 * Class TicketMessages
 * @package Waka\Support\FormWidgets
 */
class TicketMessages extends FormWidgetBase
{

    /**
     * @var string
     */
    protected $defaultAlias = 'waka_support_ticket_messages';

    /**
     * @inheritdoc
     */
    public function render()
    {
        $this->prepareVars();

        return $this->makePartial('ticketmessages');
    }

    /**
     * @return void
     */
    public function prepareVars()
    {
        $ticket = Ticket::with(['ticket_messages.user', 'user'])->find($this->model->id);
        $this->vars['ticket'] = $ticket;
    }

    /**
     * @inheritdoc
     */
    public function loadAssets()
    {
        $this->addCss('css/ticketmessages.css', 'Waka.Support');
        $this->addJs('js/ticketmessages.js', 'Waka.Support');
    }

    public function getSaveValue($value)
    {
        return \Backend\Classes\FormField::NO_SAVE_DATA;
    }

}
