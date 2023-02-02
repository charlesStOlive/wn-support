<?php namespace Waka\Support\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use Exception;
use Waka\Support\Models\Ticket;
use Backend\Models\User;
use Waka\Support\Models\Settings;
use System\Helpers\DateTime as DateTimeHelper;
/**
 * reportTicket Report Widget
 */
class ReportTickets extends ReportWidgetBase
{
    /**
     * @var string The default alias to use for this widget
     */
    protected $defaultAlias = 'ReportTicketReportWidget';

    /**
     * Defines the widget's properties
     * @return array
     */
    public function defineProperties()
    {
        return [
            'title' => [
                'title'             => 'backend::lang.dashboard.widget_title_label',
                'default'           => 'Facturation de tickets',
                'type'              => 'string',
                'validationPattern' => '^.+$',
                'validationMessage' => 'backend::lang.dashboard.widget_title_error',
            ],
            'user' => [
                'title'             => 'Quel utilisateur ?',
                'type'              => 'dropdown',
                'validationPattern' => '^.+$',
                'validationMessage' => 'backend::lang.dashboard.widget_title_error',
            ],
        ];
    }
    
    /**
     * Adds widget specific asset files. Use $this->addJs() and $this->addCss()
     * to register new assets to include on the page.
     * @return void
     */
    protected function loadAssets()
    {
    }

    public function getUserOptions()
    {
        $usersToReturn = [];
        $usersToReturn['me'] = 'Mon profil';
        $users =   Settings::getClientManagers();
        $users =   User::whereIn('id', $users)->pluck('login', 'id');
        $usersToReturn = $usersToReturn +  $users->toArray();
        return  $usersToReturn;
    }
    
    /**
     * Renders the widget's primary contents.
     * @return string HTML markup supplied by this widget.
     */
    public function render()
    {
        try {
            $this->prepareVars();
        } catch (Exception $ex) {
            $this->vars['error'] = $ex->getMessage();
        }

        return $this->makePartial('reporttickets');
    }

    /**
     * Prepares the report widget view data
     */
    public function prepareVars()
    {
        $userSelected = $this->property('user');
        //trace_log($userSelected);
        if(!$userSelected or $userSelected == 'me') {
            $userSelected = \BackendAuth::getUser()->id;
        }
        $userTickets = Ticket::where('next_id', $userSelected)->whereNotIn('state', ['archived', 'abdn'])->get();
        $this->vars['userTickets'] = $userTickets;
        //trace_log($userTickets->toArray());
    }

    public function evalDate($date)
    {
        if ($date === null) {
            return null;
        }
        return \Backend::dateTime($date, ['timeSince' => true]);
    }
}
