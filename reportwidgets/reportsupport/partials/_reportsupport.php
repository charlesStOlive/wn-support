<div class="report-widget">
    <h3><?= e($this->property('title')) ?></h3>

    <?php if (!isset($error)) : ?>
        <div class="scoreboard">
            <div class="scoreboard-item title-value">
                <h4>Nb tickets</h4>
                <p><?= $ticketsGroup?->qty ?></p>
                <p class="description">ouvert : <?= $ticketsGroup?->qtyOpened ?></p>
            </div>

            <div class="scoreboard-item title-value">
                <h4>Nb heures</h4>
                <p class=""><?= $ticketsGroup?->heures ?> H</p>
                <p class="description">soit : <?= $ticketsGroup?->total ?> € HT</p>
            </div>

            <div class="scoreboard-item title-value">
                <h4>Groupe de tickets</h4>
                <p><?= $ticketsGroup?->name ?></p>
            </div>
        </div>
        
    <?php else : ?>
        <p class="flash-message static warning"><?= e($error) ?></p>
    <?php endif ?>
</div>