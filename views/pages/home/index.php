<?php
/* @var $calendar \mpf\helpers\Calendar */
?>
<?= \app\components\htmltools\Page::title('Events Calendar', [
    [
        'url' => ['events', 'index'],
        'label' => 'Events List',
        'htmlOptions' => ['class' => 'selected']
    ],
    [
        'url' => ['events', 'create'],
        'label' => 'Create Event'
    ]
]); ?>

<div class="calendar-nextpreviousbar">
    <span class="calendar-currentmonthtitle"><?= \app\components\htmltools\Page::get()->translate($calendar->getMonthName()) . ' ' . $calendar->year ?></span>
    <?= \mpf\web\helpers\Html::get()->link(["events", "index", ['month' => $calendar->previous()->month, 'year' => $calendar->previous()->year]], \app\components\htmltools\Page::get()->translate($calendar->previous()->getMonthName()) . ' ' . $calendar->previous()->year . ' &#171;', ['class' => 'general-button calendar-prevmonth']); ?>
    <?= \mpf\web\helpers\Html::get()->link(["events", "index", ['month' => $calendar->next()->month, 'year' => $calendar->next()->year]], '&#187; ' . \app\components\htmltools\Page::get()->translate($calendar->next()->getMonthName()) . ' ' . $calendar->next()->year, ['class' => 'general-button calendar-nextmonth']); ?>
</div>

<div class="calendar-table-container">
    <table class="calendar-table">
        <?php for ($i = 1; $i <= $calendar->getNumberOfWeeks(); $i++) { ?>
            <tr>
                <th><?= \app\components\htmltools\Page::get()->translate("Week") . ' ' . $i ?></th>
                <?php foreach ($calendar->getWeek($i) as $day) { ?>
                    <td class="<?= $day->inCurrentMonth ? 'current-month' : 'other-month'; ?> <?= $day->isToday() ? 'current-day' : ''; ?>">
                        <span class="calendar-current-day"><?= $day->dayNumber; ?><span class="calendar-current-day-name"> | <?= \app\components\htmltools\Page::get()->translate($day->getWeekDay()); ?></span></span>
                        <ul>
                        <?php foreach ($day->events as $event) { ?>
                            <?php /* @var $event \app\models\Event */ ?>
                            <li>
                            <?= $event->getDetailsLink($day->date); ?>
                            </li>
                        <?php } ?>
                        </ul>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </table>
</div>

<div class="calendar-nextpreviousbar">
    <?= \mpf\web\helpers\Html::get()->link(["events", "index", ['month' => $calendar->previous()->month, 'year' => $calendar->previous()->year]], \app\components\htmltools\Page::get()->translate($calendar->previous()->getMonthName()) . ' ' . $calendar->previous()->year . ' &#171;', ['class' => 'general-button calendar-prevmonth']); ?>
    <?= \mpf\web\helpers\Html::get()->link(["events", "index", ['month' => $calendar->next()->month, 'year' => $calendar->next()->year]], '&#187; ' . \app\components\htmltools\Page::get()->translate($calendar->next()->getMonthName()) . ' ' . $calendar->next()->year, ['class' => 'general-button calendar-nextmonth']); ?>
</div>