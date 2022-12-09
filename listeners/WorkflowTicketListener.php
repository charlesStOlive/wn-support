<?php namespace Waka\Support\Listeners;

use Carbon\Carbon;
use Waka\Utils\Classes\Listeners\WorkflowListener;
use Waka\Support\Models\Settings;
use Backend\Models\User;

class WorkflowTicketListener extends WorkflowListener
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($event)
    {
        //Evenement obligatoires
        $event->listen('workflow.ticket.guard', [$this, 'onGuard']);
        $event->listen('workflow.ticket.entered', [$this, 'onEntered']);
        $event->listen('workflow.ticket.afterModelSaved', [$this, 'onAfterSavedFunction']);
        //Evenement optionels à déclarer ici.
        //$event->listen('workflow.ticket.leave', [$this, 'onLeave']);
        //$event->listen('workflow.ticket.transition', [$this, 'onTransition']);
        $event->listen('workflow.ticket.enter', [$this, 'onEnter']);
    }

    /**
     * Fonctions de Gard
     * Permet de bloquer ou pas une transition d'état
     * doit retourner true or false
     */
    // public function authorized($event, $args = null)
    // {
    //     $blocked = false;
    //     $model = $event->getSubject();
    //     $type = $args['name'];
    //     //A terminer
    //     return $blocked;
    // }
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
        trace_log('createChildTicket');
        
        $model = $event->getSubject();
        trace_log($model->name);
        $model->createChildTicket();
    }

    // public function cleanData($event, $args = null)
    // {
    //     //trace_log('nettoyage des donnes');
    // }
    // public function removeValue($event, $args = null)
    // {
    //     $blocked = false;
    //     $model = $event->getSubject();
    //     $field = $args['field'];
    //     $model->{$field} = null;
    // }

    /**
     * Fonctions de production de doc, pdf, etc.
     * passe par l'evenement afterModelSaved
     * 2 arguements $model et $arg
     * Ici les valeurs ne peuvent plus être modifié il faut passer par un traitement
     */
    public function askSleep($model) {
        //trace_log('fonction askSleep');
        \Event::fire('waka.workflow.popup_afterSave', ['name' => 'sleep']);
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
