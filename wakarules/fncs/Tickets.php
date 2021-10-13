<?php namespace Waka\Support\WakaRules\Fncs;

use Waka\Utils\Classes\Rules\FncBase;
use Waka\Utils\Interfaces\Fnc as FncInterface;

class Tickets extends FncBase implements FncInterface
{
    protected $tableDefinitions = [];

    /**
     * Returns information about this event, including name and description.
     */

    public $jsonable = ['states'];
    //
    public function fncDetails()
    {
        return [
            'name'        =>  'tickets',
            'description' => 'description de tickets ',
            'icon'        => 'icon-users',
            'premission'  => 'wcli.utils.fnc.edit.admin',
        ];
    }

    public function fncBridges()
    {
        return [
            'emuser'        => [
                'Wcli\Crpf\Models\EmUser',
            ],
        ];
    }

    public function listTicketState()
    {
        $model = new \Waka\Support\Models\Ticket();
        return $model->listAllWorklowstate();
    }


    public function getText()
    {
        $hostObj = $this->host;
        //trace_log($hostObj->config_data);
        $state = $hostObj->config_data['state'] ?? null;
        $mode = $hostObj->config_data['mode'] ?? null;
        if($state) {
            return "Etats : ".$state.' | Mode : '.$mode; 
        } else {
            return "Tout états | Mode : ".$mode; ;
        }
    }


    public function resolve($modelSrc, $poductorDs) {
        //$query = $this->getBridgeQuery($modelSrc, $poductorDs);
        $iduser = $modelSrc->id;
        $mode = $this->getConfig('mode');
        $states = $this->getConfig('states');
        $query = \Waka\Support\models\Ticket::opened();
        //trace_log($query->get()->toArray());
        if($mode == 'next_only' ) {
            $query = $query->where('next_id', $iduser);
        } else if($mode == 'creator_only') {
            $query = $query->where('creator_id', $iduser);
        } 
        if($states) {
            $query = $query->whereIn('state', $states);
        }
        $query = $query->orderBy('state')->get();
        
        
        $query = $query->map(function ($item) {
            //trace_log($item->name);
            return $item->append('wfPlaceLabel');
        });
        return $query->toArray();
    }
}
