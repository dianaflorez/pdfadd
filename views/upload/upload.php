<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Subir Archivos PDF Uno a Uno';
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="upload-form">
    <?php $form = ActiveForm::begin([
        'id' => 'single-upload-form',
        'options' => ['enctype' => 'multipart/form-data'],
        'action' => Url::to(['upload/single-upload']),
    ]); ?>

    <?= $form->field($model, 'upload_files[]')->fileInput([
        'multiple' => false,
        'accept' => 'application/pdf',
        'id' => 'file-input'
    ]) ?>

    <?php ActiveForm::end(); ?>
</div>

<div id="uploaded-files">
    <h3>Archivos Subidos:</h3>
    <ul id="file-list"></ul>
</div>

<!-- Botón para combinar archivos -->
<div>
    <?= Html::a('Combinar Archivos PDF', ['upload/merge-pdfs'], ['class' => 'btn btn-success', 'id' => 'merge-button']) ?>
</div>

<!-- Botón de descarga que solo se muestra cuando el archivo combinado está listo -->
    
<?php if (Yii::$app->request->get('mergedPdf')): ?>
    <?php if (file_exists(Yii::getAlias('@webroot/uploads/pdf_combined.pdf'))): ?>
        <div>
            <?= Html::a('Descargar Archivo Combinado', ['upload/download-merged-pdf'], [
                'class' => 'btn btn-primary',
                'id' => 'download-button',
                'target' => '_blank' // Abre en una nueva pestaña
            ]) ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php
$uploadUrl = Url::to(['upload/single-upload']);
$js = <<<JS
    $('#file-input').on('change', function() {
        var formData = new FormData($('#single-upload-form')[0]);
        
        $.ajax({
            url: '{$uploadUrl}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if (data.success) {
                    $('#file-list').append('<li>' + 'Archivo subido con éxito' + '</li>');
                    alert('Archivo subido');
                } else {
                    alert('Error al subir el archivo');
                }
            },
            error: function() {
                alert('Error al procesar la solicitud.');
            }
        });
    });
JS;

$this->registerJs($js);
?>
