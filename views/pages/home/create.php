<?php
/* @var $categories \app\models\EventCategory[] */
/* @var $model \app\models\Event */
?>
<?= \app\components\htmltools\Page::title('Events Calendar', [
    [
        'url' => ['events', 'index'],
        'label' => 'Events List'
    ],
    [
        'url' => ['events', 'create'],
        'label' => 'Create Event',
        'htmlOptions' => ['class' => 'selected']
    ]
]); ?>

    <h2><?= \app\components\htmltools\Page::get()->translate('Load From Template'); ?></h2>

<?= \mpf\widgets\form\Form::get([
    'name' => 'load_template',
    'theme' => 'default-wide',
    'method' => 'GET',
    'formHtmlOptions' => ['enctype' => 'multipart/form-data'],
    'fields' => [
        [
            'name' => 'template',
            'type' => 'select',
            'options' => isset($templates) ? $templates : [' - none found -']
        ]
    ],
    'submitButton' => [
        'name' => '',
        'label' => 'Load Template'
    ]
])->display(); ?>

    <h2><?= \app\components\htmltools\Page::get()->translate('Event Details'); ?></h2>

<?= \mpf\widgets\form\Form::get([
    'name' => 'save',
    'model' => $model,
    'theme' => 'default-wide',
    'fields' => [
        'title',
        [
            'name' => 'details',
            'type' => 'markdown'
        ],
        [
            'name' => 'category_id',
            'type' => 'select',
            'options' => \mpf\helpers\ArrayHelper::get()->transform($categories, ['id' => 'name_' . (\mpf\WebApp::get()->request()->getLanguage() == 'ro' ? 'ro' : 'en')])
        ],
        [
            'name' => 'icon',
            'type' => 'image',
            'urlPrefix' => $model->iconLocationUrl
        ],
        [
            'name' => 'event_time',
            'type' => 'dateTime'
        ],
        [
            'name' => 'event_end_time',
            'type' => 'dateTime'
        ],
        [
            'name' => 'visibility',
            'type' => 'select',
            'options' => \app\models\Event::getVisibilityOptions()
        ],
        [
            'name' => 'requirementsUpdates',
            'type' => 'modelRelation',
            'legendLabel' => \app\components\htmltools\Page::get()->translate('Role'),
            'modelClass' => \app\models\EventRequirement::className(),
            'fields' => [
                'title_ro',
                'title_en',
                'min_number',
                'max_number',
                'recommended_number'
            ]
        ],
        [
            'name' => 'saveTemplate',
            'type' => 'checkbox'
        ],
        'templateName'
    ]
])->display();
