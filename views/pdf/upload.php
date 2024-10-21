<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'], // Necesario para subir archivos
]);

echo $form->field($model, 'upload_files[]')->fileInput(['multiple' => true, 'accept' => 'application/pdf']);

echo Html::submitButton('Subir archivos', ['class' => 'btn btn-primary']);

ActiveForm::end();

