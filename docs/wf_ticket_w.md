# Workflow : **ticket_w**
* type: **state_machine / single_state**
* Nom : ticket_w
* comentaire : 

## Representation graphique
![](../assets/docs_images/ticket_w_tb.jpeg)


##Explications graph : 
### Etat : 
* les réctangles arronds représentent les états
* CRON_AUTO : indique que des tentatives de transitions seront automatiquement effectués par l'app. 
* FORM_AUTO : indique que le formulaire va tenter d'éffectuer une transition automatique en fonction des données qu'il possède. 
* Si il y a une permission alors seul les utilisateurs avec ses permissions auront accès à l'état. 
* Si il y a une NO permission alors seul les  utilisateurs ayant une permission différente auront assès à l'état. A noter : N'écrase pas la config du controller. 

### Transitions :  
* les rectangles droits représentent les transitions 
* Un réctange de couleur avec BTN, indique qu'un bouton portant ce nom sera arriché dans le formulaire 
* il y a trois types de fonctions : 
  * Gard : Une fonction qui permet de valider ou non une transition, si la fonction retourne false, la transition ne sera pas autorisé. 
  * Trait: Une fonction de traitement qui sera executé lors de la transitions 
  * Prod: Une fonction qui va produire un élement, cette fonction est appelé après l'enregistrmeent du modèle porteur du workflow. 

## Liste des états
* **Brouillon** | code : draft
* **Retour Notilac attendu** | code : wait_support
* **Retour Client attendu** | code : wait_managment
    * Permissions : **waka.support.user.***
* **En production** | code : running
    * Permissions : **waka.support.admin.***
* **Ticket validé** | code : validated
* **En sommeil** | code : sleep
* **Archivé** | code : archived
* **Abandon du ticket** | code : abdn

## Liste des transitions
* **Abandonner** | code : wait_support_to_abdn
    * Bouton : Abandonner le ticket
    * Liste des fonctions 
        *  isCreatorAsking | type : gard  | description : Est-ce le créateur de la tâche
* **Abandonner** | code : wait_managment_to_abdn
    * Bouton : Abandonner le ticket
    * Liste des fonctions 
        *  isCreatorAsking | type : gard  | description : Est-ce le créateur de la tâche
* **Archivage** | code : validated_to_archived
    * Bouton : Archivage du ticket
* **Archivage facturation** | code : to_archived_factu
    * Bouton : Archivage facturation
    * Liste des fonctions 
        *  createChildTicket | type : trait_onEnter  | description : Fonction qui va créer un ticket enfants
* **Envoyer  client** | code : wait_support_to_wait_managment
    * Bouton : Envoyer au client
    * Liste des fonctions 
        *  sendNotification | type : prod  |  Arguments : waka.support::new_ticket, client  | description : Envoyer une notification
        *  isSupport | type : gard  | description : Est dans l&#039;équipe support
* **FIN prod** | code : running_to_wait_managment
    * Bouton : Envoyer au client
    * Liste des fonctions 
        *  isSupport | type : gard  | description : Est dans l&#039;équipe support
* **Mise en sommeil ** | code : wait_support_to_sleep
    * Bouton : Mise en sommeil du ticket
    * Liste des fonctions 
        *  askSleep | type : prod  | description : Permet de préciser quant il faut réveiller la tâche
        *  removeTicketGroup | type : trait_onEnter  | description : Fonction qui supprimer le group ticket 
        *  isSupport | type : gard  | description : Est dans l&#039;équipe support
* **En Production** | code : wait_support_to_running
    * Bouton : En cours de production
    * Liste des fonctions 
        *  isSupport | type : gard  | description : Est dans l&#039;équipe support
* **Répondre à Notilac** | code : wait_managment_to_wait_support
    * Bouton : Répondre à Notilac
    * Liste des fonctions 
        *  sendNotification | type : prod  |  Arguments : waka.support::new_ticket, support  | description : Envoyer une notification
* **Réveiller C** | code : sleep_to_wait_managment
    * Bouton : Réveiller et transmettre C
    * Liste des fonctions 
        *  cleanAwake | type : trait_onEnter  | description : Fonction qui enlève le champs awake_at
* **Réveiller N** | code : sleep_to_wait_support
    * Bouton : Réveiller et transmettre N
    * Liste des fonctions 
        *  cleanAwake | type : trait_onEnter  | description : Fonction qui enlève le champs awake_at
* **Transmission Client** | code : draft_to_wait_managment
    * Bouton : Transmettre au  client
    * Liste des fonctions 
        *  sendNotification | type : prod  |  Arguments : waka.support::new_ticket, client  | description : Envoyer une notification
        *  isSupport | type : gard  | description : Est dans l&#039;équipe support
* **Transmission Notilac** | code : draft_to_wait_support
    * Bouton : Transmettre à Notilac
    * Liste des fonctions 
        *  sendNotification | type : prod  |  Arguments : waka.support::new_ticket, support  | description : Envoyer une notification
* **Validation** | code : wait_managment_to_validated
    * Bouton : Valider le ticket
    * Liste des fonctions 
        *  sendNotification | type : prod  |  Arguments : waka.support::new_ticket, support  | description : Envoyer une notification
        *  isClient | type : gard  | description : Est dans l&#039;équipe client
* **Valider et clôturer** | code : draft_to_validated
    * Bouton : Valider et clôturer le ticket
    * Liste des fonctions 
        *  isCreatorAsking | type : gard  | description : Est-ce le créateur de la tâche

## Les jeux de validations des champs
* **default**: support_client et support_user obligatoire
