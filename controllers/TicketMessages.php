<?php namespace Waka\Support\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Ticket Message Back-end Controller
 */
class TicketMessages extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Waka.Utils.Behaviors.BtnsBehavior',
    ];
    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $btnsConfig = 'config_btns.yaml';
    //FIN DE LA CONFIG AUTO

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Waka.Support', 'support', 'side-menu-ticketmessages');
    }

    //startKeep/

        //endKeep/
}

