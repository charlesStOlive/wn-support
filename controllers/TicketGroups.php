<?php namespace Waka\Support\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;

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

    public $requiredPermissions = ['nobody'];
    //FIN DE LA CONFIG AUTO

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Waka.Support', 'TicketGroups');
    }

    //startKeep/

    public function update($id)
    {
        $this->bodyClass = 'compact-container';
        return $this->asExtension('FormController')->update($id);
    }

        //endKeep/
}

