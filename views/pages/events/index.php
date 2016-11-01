<?= \app\components\htmltools\Page::title('Evenimente', [
    [
        'url' => ['events', 'index'],
        'label' => 'Lista',
        'htmlOptions' => ['class' => 'selected']
    ],
    [
        'url' => ['eventscategories', 'index'],
        'label' => 'Categorii'
    ],
    [
        'url' => ['eventstemplates', 'index'],
        'label' => 'Sabloane'
    ]
]); ?>


<?= \mpf\widgets\datatable\Table::get([
    'dataProvider' => $model->getDataProvider(),
    'multiSelect' => true,
    'multiSelectActions' => [
        'validate' => [
            'label' => 'Validate (/Open)',
            'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/16x16/actions/dialog-ok-apply.png'),
            'url' => \mpf\WebApp::get()->request()->createURL("events", "actions")
        ],
        'close' => [
            'label' => 'Close',
            'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/16x16/actions/dialog-cancel.png'),
            'url' => \mpf\WebApp::get()->request()->createURL("events", "actions")
        ],
        'delete' => [
            'label' => 'Delete',
            'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/16x16/actions/edit-delete.png'),
            'shortcut' => 'Shift+Delete',
            'url' => \mpf\WebApp::get()->request()->createURL("events", "delete"),
            'confirmation' => 'Are you sure?'
        ]
    ],
    'columns' => [
        'title',
        'category_id' => [
            'filter' => \mpf\helpers\ArrayHelper::get()->transform(\mpf\modules\calendar\models\EventCategory::findAll(), ['id' => 'name_ro']),
            'value' => function (\mpf\modules\calendar\models\Event $event) {
                return $event->category->getName();
            }
        ],
        'author_id' => [
            'filter' => \mpf\helpers\ArrayHelper::get()->transform(\app\models\User::findAll(), ['id' => 'name']),
            'value' => function (\mpf\modules\calendar\models\Event $event) {
                return $event->author->name;
            }
        ],
        'added_time' => [
            'class' => 'Date'
        ],
        'event_time' => [
            'class' => 'Date'
        ],
        'event_end_time' => [
            'class' => 'Date'
        ],
        'status' => [
            'filter' => \mpf\modules\calendar\models\Event::$statusesLabels,
            'value' => function (\mpf\modules\calendar\models\Event $event) {
                return $event->getStatus();
            }
        ],
        'visibility' => [
            'filter' => \mpf\modules\calendar\models\Event::$visibilitiesLabels,
            'value' => function (\mpf\modules\calendar\models\Event $event) {
                return $event->getVisibility();
            }
        ],
        [
            'class' => 'Actions',
            'buttons' => [
                'edit' => ['class' => 'Edit'],
                'view' => ['class' => 'View', 'url' => "\\mpf\\WebApp::get()->request()->createURL('events', 'view', ['id' => \$row->id], '')", 'htmlOptions' => ['target' => '_blank']],
                'validate' => [

                ]
            ],
            'headerHtmlOptions' => ['style' => 'width:60px;']
        ]
    ]
])->display(); ?>