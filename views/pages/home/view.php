<?php use mpf\web\helpers\Html; ?>
<?php use app\components\htmltools\Page; ?>
<?php use mpf\helpers\DateTimeHelper; ?>
<?php /* @var $event \mpf\modules\calendar\models\Event */ ?>
<?= Page::title('Events Calendar', [
    [
        'url' => ['home', 'index'],
        'label' => 'Events List'
    ],
    [
        'url' => ['home', 'create'],
        'label' => 'Create Event'
    ]
]); ?>
<div class="event-details-page">
    <h2 class="event-title"><?= $event->title; ?>
        <?php if ($event->canIEditIt()) { ?>
            <span class="event-owner-actions-bar">
                <?= Html::get()->link(['events', 'edit', ['id' => $event->id]], Html::get()->mpfImage('oxygen/32x32/actions/document-edit.png')); ?>
            </span>
        <?php } ?>
    </h2>
    <div class="event-details">
        <b><?= DateTimeHelper::get()->niceDate($event->event_time, true) . ' ~ ' . DateTimeHelper::get()->niceDate($event->event_end_time, true); ?></b>

        <?= Html::get()->link($event->author->getProfileURL(), $event->author->name) . $event->author->getHtmlIcon() . Html::get()->tag('span', Page::get()->translate("Created By")); ?>
        <span class="event-category-name event-category-<?= $event->category->html_class_suffix; ?>"><?= $event->category->getName(); ?></span>
    </div>
    <div class="event-icon">
        <?= Html::get()->image($event->getIconURL()) ?>
    </div>

    <div class="event-description info-page-content">
        <?= $event->getDetails(); ?>
    </div>

    <?php if (($event->status == \mpf\modules\calendar\models\Event::STATUS_OPEN) && (count($event->requirements))) { ?>
        <div class="event-requirements">
            <h3><?= Page::get()->translate("Required Roles"); ?></h3>
            <?php foreach ($event->requirements as $requirement) { ?>
                <div class="event-requirement">
                    <?= $requirement->getTitle(); ?>

                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>
