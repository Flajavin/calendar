<?= \app\components\htmltools\Page::title('Categorii Evenimente', [
    [
        'url' => ['eventscategories', 'index'],
        'label' => 'Lista'
    ],
    [
        'url' => ['eventscategories', 'add'],
        'label' => 'Categorie Noua',
        'htmlOptions' => ['class' => 'selected']
    ]
]); ?>

<?= \mpf\widgets\form\Form::get([
    'name' => 'save',
    'model' => $model,
    'theme' => 'default-wide',
    'fields' => [
        'name_ro',
        'name_en',
        'html_class_suffix'
    ]
])->display(); ?>
