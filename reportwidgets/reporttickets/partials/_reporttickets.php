<div class="report-widget">
    <h3><?= e($this->property('title')) ?></h3>

    <?php if (!isset($error)): ?>
        <div class="control-list">
            <table class="table data" data-control="rowlink">
                <tbody>
                <?php foreach($userTickets as $ticket): ?>
                    <tr>
                        <td>
                            <a href="<?=Backend::url('waka/support/tickets/update/'.$ticket->id);?>" >
                                <?=$ticket->wfPlaceLabel?>
                            </a>
                        </td>
                        <td><?=$ticket->name?></td>
                        <td><?=$this->evalTimetenseTypeValue($ticket->updated_at);?></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="flash-message static warning"><?= e($error) ?></p>
    <?php endif ?>
</div>