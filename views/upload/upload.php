<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Subir Archivos PDF';
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success">
        <?= Yii::$app->session->getFlash('success') ?>
    </div>
<?php endif; ?>

<div class="upload-form">
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>

    <?= $form->field($model, 'upload_files[]')->fileInput(['multiple' => true, 'accept' => 'application/pdf']) ?>

    <?= Html::submitButton('Subir archivos', ['class' => 'btn btn-primary']) ?>

    <?php ActiveForm::end(); ?>
</div>

<!-- Agregamos el botón para descargar el PDF unido -->
<?php if ($model->isMerged): ?>
    <div class="alert alert-info mt-3">
        El archivo PDF se ha unido correctamente. 
        <?= Html::a('Descargar PDF combinado', ['uploads/merged.pdf'], ['class' => 'btn btn-success']) ?>
    </div>
<?php endif; ?>
