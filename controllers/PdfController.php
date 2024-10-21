<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\PdfForm;
use yii\web\UploadedFile;
use setasign\Fpdi\Fpdi;

class PdfController extends Controller
{
    public function actionUpload()
    {
        $model = new PdfForm();

        if (Yii::$app->request->isPost) {
            $model->pdfFiles = UploadedFile::getInstances($model, 'pdfFiles');
            $uploadedFiles = $model->upload();
            
            if ($uploadedFiles !== false) {
                // Unir los PDFs subidos
                $outputFile = 'uploads/unido.pdf';
                $this->mergePdfs($uploadedFiles, $outputFile);
                Yii::$app->session->setFlash('success', 'Los PDFs han sido unidos correctamente.');
                return $this->redirect(['ver', 'file' => $outputFile]);
            } else {
                Yii::$app->session->setFlash('error', 'Hubo un problema al subir los archivos.');
            }
        }

        return $this->render('upload', ['model' => $model]);
    }

    private function mergePdfs($pdfFiles, $outputFile)
    {
        $pdf = new FPDI();
        foreach ($pdfFiles as $file) {
            $pageCount = $pdf->setSourceFile($file);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tplIdx = $pdf->importPage($i);
                $pdf->AddPage();
                $pdf->useTemplate($tplIdx);
            }
        }
        $pdf->Output($outputFile, 'F');
    }

    public function actionVer($file)
    {
        return Yii::$app->response->sendFile($file);
    }
}
