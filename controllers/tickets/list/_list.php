<div class="list-scrollable" data-control="listwidget">
    <?php if (count($records)) : ?>
        <?php if ($this->config->recordOnClick && count($records)) : ?>
            <div class="messages_container">
                <a data-control="popup" data-size="huge" data-handler="onRelationButtonCreate" href="javascript:;" class="messages_btn" style="width:100%;">
                    <p><i class="icon-plus-square icon-lg"></i> Ajouter un message</p>
                </a>
            </div>
        <?php endif ?>
        <?= $this->makePartial('list_body_rows') ?>
    <?php else : ?>
        <div class="messages_container">
            <a data-control="popup" data-size="huge" data-handler="onRelationButtonCreate" href="javascript:;" class="messages_btn" style="width:100%;">
                <p><i class="icon-plus-square icon-lg"></i> Créer votre premier message</p>
            </a>
        </div>
    <?php endif ?>

</div>