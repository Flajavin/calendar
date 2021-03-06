<?= \app\components\htmltools\Page::title('Categorii Evenimente', [
    [
        'url' => ['eventscategories', 'index'],
        'label' => 'Categorii',
        'htmlOptions' => ['class' => 'selected']
    ],
    [
        'url' => ['eventscategories', 'add'],
        'label' => 'Categorie Noua'
    ],
    [
        'url' => ['events', 'index'],
        'label' => 'Evenimente'
    ],
    [
        'url' => ['eventstemplates', 'index'],
        'label' => 'Sabloane'
    ]
]); ?>


<?= \mpf\widgets\datatable\Table::get([
    'dataProvider' => $model->getDataProvider(),
    'columns' => [
        'name_ro',
        'name_en',
        'html_class_suffix',
        [
            'class' => 'Actions',
            'buttons' => [
                'edit' => ['class' => 'Edit'],
                'delete' => ['class' => 'Delete']
            ],
            'headerHtmlOptions' => ['style' => 'width:60px;']
        ]
    ]
])->display(); ?>
