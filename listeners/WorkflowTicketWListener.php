<?php namespace Waka\Support\Listeners;

use Carbon\Carbon;
use Waka\Utils\Classes\Listeners\WorkflowListener;
use Waka\Support\Models\Settings;
use Backend\Models\User;

class WorkflowTicketWListener extends WorkflowListener
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($event)
    {
        //Evenement obligatoires
        $event->listen('workflow.ticket_w.guard', [$this, 'onGuard']);
        $event->listen('workflow.ticket_w.entered', [$this, 'onEntered']);
        $event->listen('workflow.ticket_w.enter', [$this, 'onEnter']);
        $event->listen('workflow.ticket_w.afterModelSaved', [$this, 'onAfterSavedFunction']);
        //Evenement optionels à déclarer ici.
        //$event->listen('workflow.ticket_w.leave', [$this, 'onLeave']);
        $event->listen('workflow.ticket_w.transition', [$this, 'recLogs']);
        //$event->listen('workflow.ticket_w.enter', [$this, 'onEnter']);
    }

    /**
     * Fonctions de Gard
     * Permet de bloquer ou pas une transition d'état
     * doit retourner true or false
     */
     public function isCreatorAsking($event, $args = null)
    {
        $blocked = false;
        $model = $event->getSubject();
        $actualUser = \BackendAuth::getUser();
        if($model->user != $actualUser) {
            $blocked = true;
        }
        return $blocked;
    }

    public function isAwakable($event, $args = null)
    {
        $model = $event->getSubject();
        $date = Carbon::now();
        if ($model->awake_at->lte($date)) {
            return false;
        }
        return true;
    }

    



    public function isClient($event, $args = null)
    {
        $blocked = false;
        $blocked = !Settings::isClientManager(); //Si pas manager
        return $blocked;
    }

    public function isSupport($event, $args = null)
    {
        $blocked = false;
        $blocked = !Settings::isSupportMember(); //Si pas équipe support
        return $blocked;
    }
    

    /**
     * FONCTIONS DE TRAITEMENT PEUVENT ETRE APPL DANS LES FONCTIONS CLASSIQUES
     */

    public function createChildTicket($event, $args = null)
    {
        //trace_log('createChildTicket');
        
        $model = $event->getSubject();
        //trace_log($model->name);
        $model->createChildTicket();
    }

    public function removeTicketGroup($event, $args = null)
    {
        $model = $event->getSubject();
        $model->ticket_group_id = null;
    }

    /**
     * Fonctions de production de doc, pdf, etc.
     * passe par l'evenement afterModelSaved
     * 2 arguements $model et $arg
     * Ici les valeurs ne peuvent plus être modifié il faut passer par un traitement
     */

    public function askSleep($model) {
        //trace_log('fonction askSleep');
        $model->ticket_group_id = null;
        \Event::fire('waka.workflow.popup_afterSave', ['name' => 'sleep']);
    }

    public function cleanAwake($model) {
        //trace_log('fonction cleanAwake');
        $model->awake_at = null;
    }


    public function sendNotification($model, $args = null)
    {
        if($model->silent_mode) return;
        //trace_log('sendNotification');
        //trace_log($args);
        $subject = $model->name;
        $modelId = $model->id;
        //trace_log($model->ticket_messages);
        $nextUserid = $model->next_id;
        $userNext = User::find($nextUserid);
        // trace_log("userEmail");
        // trace_log("nextUserid ".$nextUserid);
        // trace_log($userNext->email);
        //

        $model = $model->toArray();
        $model = compact('model');
        $dotedModel = array_dot($model);



        $code = $args['code'];
        $mode =  $args['mode'];

        $datasEmail = [
            'emails' => $userNext->email,
            'subject' => null,
        ];
        // trace_log('envoyer un email -----------------------');
        // trace_log($dotedModel);
        // trace_log($code);
        // trace_log($datasEmail);
        // trace_log('----------------------Fin email');
        \Waka\Mailer\Classes\MailCreator::find($code, true)->setModelId($modelId)->renderMail($datasEmail);
    }

}