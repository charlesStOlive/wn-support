<?php namespace Waka\Support\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Ticket Back-end Controller
 */
class Tickets extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Waka.Utils.Behaviors.BtnsBehavior',
        'Waka.Utils.Behaviors.SidebarInfoBehavior',
        'Waka.ImportExport.Behaviors.ExcelImport',
        'Waka.ImportExport.Behaviors.ExcelExport',
        'Backend.Behaviors.ReorderController',
        'Waka.Utils.Behaviors.WorkflowBehavior',
    ];
    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $btnsConfig = 'config_btns.yaml';
    public $reorderConfig = 'config_reorder.yaml';
    public $sidebarInfoConfig = 'config_sidebar_info.yaml';
    public $workflowConfig = 'config_workflow.yaml'; 
    //FIN DE LA CONFIG AUTO

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Waka.Support', 'support', 'tickets');
    }

    //startKeep/

    public function update($id)
    {
        $this->bodyClass = 'compact-container';
        return $this->asExtension('FormController')->update($id);
    }

        //endKeep/
}

