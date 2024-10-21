<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\UploadForm;
use yii\web\UploadedFile;

class UploadController extends Controller
{
    public function actionUpload()
    {
        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->upload_files = UploadedFile::getInstances($model, 'upload_files');

            if ($model->upload()) {
                Yii::$app->session->setFlash('success', 'Archivos subidos correctamente.');
                return $this->render('upload', ['model' => $model]);
            }
        }

        return $this->render('upload', ['model' => $model]);
    }

}

