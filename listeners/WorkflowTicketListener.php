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
        //$event->listen('workflow.ticket.enter', [$this, 'onEnter']);
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
        return Settings::isClientManager();
    }

    public function isSupport($event, $args = null)
    {
        return Settings::isSupportMember();
    }

    /**
     * FONCTIONS DE TRAITEMENT PEUVENT ETRE APPL DANS LES FONCTIONS CLASSIQUES
     */

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


    public function sendNotification($model, $args = null)
    {
        //trace_log('sendNotification');
        //trace_log($args);
        $subject = $model->name;
        $modelId = $model->id;
        //trace_log($model->ticket_messages);
        $model = $model->toArray();
        $model = compact('model');
        $dotedModel = array_dot($model);



        $code = $args['code'];
        $mode =  $args['mode'];

        $clientManagerIds = [];

        if($mode == 'support') {
            $userIds = Settings::getSupportUsers();
        } else {
            $userIds = Settings::getClientManagers();
        }

        
        $users = User::whereIn('id', $userIds)->get(['email'])->pluck('email')->toArray();
        //trace_log($users);
        $usersEmails = implode(',',$users);

        $datasEmail = [
            'emails' => $usersEmails,
            'subject' => null,
        ];
        trace_log('envoyer un email -----------------------');
        trace_log($dotedModel);
        trace_log($code);
        trace_log($datasEmail);
        trace_log('----------------------Fin email');
    }

}
