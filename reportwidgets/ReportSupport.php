<?php namespace Waka\Support\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use Exception;
use Waka\Support\Models\TicketGroup;


/**
 * reportSupport Report Widget
 */
class ReportSupport extends ReportWidgetBase
{
    /**
     * @var string The default alias to use for this widget
     */
    protected $defaultAlias = 'ReportSupportReportWidget';

    /**
     * Defines the widget's properties
     * @return array
     */
    public function defineProperties()
    {
        return [
            'title' => [
                'title'             => 'backend::lang.dashboard.widget_title_label',
                'default'           => 'Facturation de ticket',
                'type'              => 'string',
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
         $this->addCss('css/widget.css');
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

        return $this->makePartial('reportsupport');
    }

    /**
     * Prepares the report widget view data
     */
    public function prepareVars()
    {
        $lastTicketsGroup = TicketGroup::orderBy('created_at', 'desc')->where('is_factured', false)->first();
        trace_log($lastTicketsGroup->toArray());
        $this->vars['ticketsGroup'] = $lastTicketsGroup;
    }
}
