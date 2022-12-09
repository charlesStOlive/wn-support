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
        'Waka.Utils.Behaviors.SideBarUpdate',
        'Waka.ImportExport.Behaviors.ExcelImport',
        'Waka.ImportExport.Behaviors.ExcelExport',
        'Backend.Behaviors.ReorderController',
        'Waka.Utils.Behaviors.WorkflowBehavior',
    ];
    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $btnsConfig = 'config_btns.yaml';
    public $reorderConfig = 'config_reorder.yaml';
    public $sideBarUpdateConfig = 'config_side_bar_update.yaml';
    public $workflowConfig = 'config_workflow.yaml'; 

    public $requiredPermissions = ['waka.support.*'];
    //FIN DE LA CONFIG AUTO
    //startKeep/
    public $wfSleepWidget;

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Waka.Support', 'support', 'side-menu-tickets');
        $this->wfSleepWidget = $this->createSleepFormWidget();
    }

    public function update($id)
    {
        $this->bodyClass = 'compact-container';
        return $this->asExtension('FormController')->update($id);
    }

    public function onCreateNewFromArchived() {
        $ticket = \Waka\Support\Models\Ticket::find($this->params[0]);
        $newId = $ticket->createChildTicket();
        return \Redirect::to(\Backend::url('waka/support/tickets/update/'.$newId));
    }

    public function onAfterSaveWorkflowJs() {
        $wfPopupAfterSave = \Session::pull('popup_afterSave');
        if(!$wfPopupAfterSave) {
            return true;
        }
        if($this->params[0] ?? false) {
            $this->vars['modelId'] = $this->params[0];
            $this->vars['wfSleepWidget'] = $this->wfSleepWidget;
            //trace_log("make partial");
            return $this->makePartial('sleepform');
        }
       
    }

    public function onTestCron() {
        $sleepIngTickets = \Waka\Support\Models\Ticket::where('state', 'sleep')->whereDate('awake_at' ,'<', \Carbon\Carbon::now());
        //trace_log($sleepIngTickets->count());
        foreach($sleepIngTickets->get() as $ticketToOpen) {
            //trace_log($ticketToOpen->workflow_can('sleep_to_wait_support'));
            if($ticketToOpen->workflow_can('sleep_to_wait_support')) {
                $ticketToOpen->workflow_apply('sleep_to_wait_support');
                $ticketToOpen->save();
            }
            //trace_log($ticketToOpen->workflow_can('sleep_to_wait_managment'));
            if($ticketToOpen->workflow_can('sleep_to_wait_managment')) {
                $ticketToOpen->workflow_apply('sleep_to_wait_managment');
                $ticketToOpen->save();
            }
        }
        //trace_log('call support');
    }

    public function onSaveSleep() {
        $ticket = \Waka\Support\Models\Ticket::find(post('modelId'));
        $ticket->awake_at = post('wf_sleep_array.awake_at');
        $ticket->save();
        return \Redirect::to(\Backend::url('waka/support/tickets'));
    }
    public function createSleepFormWidget() {
        $config = $this->makeConfig('$/waka/support/models/ticket/fields_sleep.yaml');
        $config->alias = 'wfSleepWidget';
        $config->arrayName = 'wf_sleep_array';
        $config->model = new \Waka\Support\Models\Ticket();
        $widget = $this->makeWidget('Backend\Widgets\Form', $config);
        $widget->bindToController();
        return $widget;
    }
    //endKeep/
}

