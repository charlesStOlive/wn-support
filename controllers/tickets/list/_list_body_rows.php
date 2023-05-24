<?php $lastRecord = $records->first(); ?>
<?php foreach ($records as $record) : ?>
    <?php if ($record === $lastRecord) : ?>
        <!-- Ici, vous êtes à la dernière itération -->
        
        <?= $this->makePartial('list_body_row', ['record' => $record, 'firstRecord' => true]) ?>
    <?php else : ?>
        <?= $this->makePartial('list_body_row', ['record' => $record, 'firstRecord' => false]) ?>
    <?php endif ?>

<?php endforeach ?>