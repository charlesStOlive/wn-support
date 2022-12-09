<?php namespace Waka\Support\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Ticket Group Back-end Controller
 */
class TicketGroups extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Waka.Utils.Behaviors.BtnsBehavior',
        'Backend.Behaviors.RelationController',
        'Waka.Utils.Behaviors.SideBarUpdate',
        'Waka.ImportExport.Behaviors.ExcelImport',
        'Waka.ImportExport.Behaviors.ExcelExport',
    ];
    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $btnsConfig = 'config_btns.yaml';
    public $relationConfig = 'config_relation.yaml';
    public $sideBarUpdateConfig = 'config_side_bar_update.yaml';

    public $requiredPermissions = ['waka.support.*'];
    //FIN DE LA CONFIG AUTO

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Waka.Support', 'support', 'side-menu-ticketgroups');
    }

    //startKeep/

    public function update($id)
    {
        $this->bodyClass = 'compact-container';
        return $this->asExtension('FormController')->update($id);
    }

    public function listInjectRowClass($record, $value)
    {
        if(!$this->user->hasAccess('waka.support.admin.super')) {
            return 'nolink  disabled';
        }
    }

    public function onClotureTicketGroup() {
        $model =  \Waka\Support\Models\TicketGroup::find($this->params[0]);
        //trace_log($model->name);
        $countRunning = $model->tickets()->where('state', 'running')->count();
        //trace_log($countRunning);
        //trace_log($model->tickets()->where('state', 'running')->get()->toArray());
        if($countRunning)  {
            \Flash::error('Un ticket est en cours de production impossible de cloturer le groupe');
             return \Redirect::refresh();
        } 
        foreach($model->tickets as $ticket) {
            if($ticket->workflow_can('to_archived_factu')) {
                //trace_log('ticket a archiver et à recreer : '.$ticket->name);
                $ticket->workflow_apply('to_archived_factu');
                $ticket->save();
            } else {
                //trace_log('ticket déjà  archiver : '.$ticket->name);
            }
        }
        $model->is_factured = true;
        $model->save();
        $redirectUrl = $this->formGetRedirectUrl('update-close', $model);
        //trace_log($redirectUrl);
        \Flash::success('groupe de tickets cloturé !');
        return \Backend::redirect($redirectUrl);
    }

        //endKeep/
}

