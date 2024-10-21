<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\file\FileInput;

/** @var yii\web\View $this */

$this->title = 'Agregar PDF';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        Esta aplicación permite unir varios pdfs
    </p>

    <!-- <code><?= __FILE__ ?></code> -->
</div>

<?php
$form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data']
]); ?>

<?= $form->field($model, 'pdfFiles[]')->widget(FileInput::classname(), [
    'options' => ['multiple' => true, 'accept' => 'application/pdf'],
    'pluginOptions' => ['allowedFileExtensions' => ['pdf']]
]); ?>

<?= Html::submitButton('Subir y Unir PDFs', ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end(); ?>
