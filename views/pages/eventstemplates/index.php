<?= \app\components\htmltools\Page::title('Sabloane Evenimente', [
    [
        'url' => ['eventstemplates', 'index'],
        'label' => 'Lista'
    ]
]); ?>


<?= \mpf\widgets\datatable\Table::get([
    'dataProvider' => $model->getDataProvider(),
    'columns' => [
        'title_ro',
        'title_en',
        'created_by',
        'created_time',
        [
            'class' => 'Actions',
            'buttons' => [
                'edit' => ['class' => 'Edit']
            ],
            'headerHtmlOptions' => ['style' => 'width:60px;'],
            'topButtons' => [
                'add' => ['class' => 'Add']
            ]
        ]
    ]
])->display(); ?>
