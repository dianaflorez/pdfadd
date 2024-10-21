<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Acerca de la app';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        Esta aplicación permite unir varios pdfs
    </p>

    <!-- <code><?= __FILE__ ?></code> -->
</div>
