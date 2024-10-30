<?php
namespace app\controllers;

use Yii;
use app\models\UploadForm;
use yii\web\UploadedFile;
use yii\web\Controller;
use Mpdf\Mpdf;
use setasign\Fpdi\Fpdi;
use yii\web\Response;


class UploadController extends Controller
{
    public function actionIndex()
    {
        $model = new UploadForm();
        
        // Pasar el modelo a la vista
        return $this->render('upload', ['model' => $model]);
    }

    public function actionSingleUpload()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new UploadForm();
        $model->upload_files = UploadedFile::getInstances($model, 'upload_files');

        if ($model->upload_files && $model->validate()) {
            $savedFiles = [];
            foreach ($model->upload_files as $file) {
                $filePath = 'uploads/' . $file->baseName . '.' . $file->extension;
                if ($file->saveAs($filePath)) {
                    $savedFiles[] = $filePath;
                }
            }
            // Guardar los archivos en la sesión para usarlos después
            Yii::$app->session->set('uploadedFiles', $savedFiles);
            return ['success' => true, 'name' => $file->baseName];
        }
        return ['success' => false];
    }


    public function actionMergePdfs()
    {
        // Define la ruta del directorio de archivos PDF
     //   $uploadPath = Yii::getAlias('@webroot') . '/uploads/';

       $uploadPath = Yii::getAlias('@webroot/uploads/');
        $convertedPath = $uploadPath . 'converted/';
        
        //$uploadPath = Yii::getAlias('@webroot/uploads/');
    $pdfFiles = glob($uploadPath . '*.pdf'); // Busca todos los archivos PDF en la carpeta uploads

    if (empty($pdfFiles)) {
        Yii::$app->session->setFlash('error', 'Nooo se han encontrado archivos PDF para combinar en la carpeta de uploads.');
        return $this->redirect(['upload/index']);
    }

        // Crear el directorio de conversión si no existe
        if (!is_dir($convertedPath)) {
            mkdir($convertedPath, 0777, true);
        }
        
        foreach (glob($uploadPath . '*.pdf') as $filePath) {
            $outputPath = $convertedPath . basename($filePath);
            $command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$outputPath $filePath";
            // exec($command);

            $output = []; // Para almacenar la salida del comando
            $returnVar = 0; // Variable para el código de retorno

            // Ejecuta el comando y captura la salida y el código de retorno
            exec($command, $output, $returnVar);

            // Verifica si hubo algún error
            if ($returnVar !== 0) {
                // echo "error";
                // var_dump( $output);
                // die();
                // Hay un error; puedes hacer algo con la salida o el código de retorno
                Yii::$app->session->setFlash('error', 'Error al ejecutar el comando Ghostscript: ' . implode("\n", $output));
            } else {
                // El comando se ejecutó correctamente
                echo "exito $output";
                die();
                Yii::$app->session->setFlash('success', 'Archivo convertido exitosamente.');
            }
        }
        


        $pdfFiles = glob($convertedPath . '*.pdf'); // Obtiene todos los archivos PDF en el directorio

        // Verifica que existan archivos PDF en la carpeta
        if (empty($pdfFiles)) {
            Yii::$app->session->setFlash('error', 'No se han encontrado archivos PDF para combinar en la carpeta de uploads.');
            return $this->redirect(['upload/index']);
        }

        // Inicializa FPDI
        $pdf = new Fpdi();

        try {
            // Itera sobre cada archivo PDF y lo agrega al documento combinado
            foreach ($pdfFiles as $filePath) {
                $pageCount = $pdf->setSourceFile($filePath); // Carga el archivo PDF

                // Importa cada página del archivo PDF
                for ($i = 1; $i <= $pageCount; $i++) {
                    $templateId = $pdf->importPage($i);
                    $size = $pdf->getTemplateSize($templateId);

                    // Agrega una nueva página con el tamaño de la página actual
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                }
            }

            // Guarda el archivo PDF combinado
            $mergedFilePath = $uploadPath . 'pdf_combined.pdf';
            $pdf->Output($mergedFilePath, 'F');

            Yii::$app->session->setFlash('success', 'Los archivos PDF se han combinado correctamente.');
            
            // Redirige a la acción de descarga del archivo combinado
            //return $this->redirect(['upload/download', 'filename' => 'pdf_combined.pdf']);
            return $this->redirect(['upload/index', 'mergedPdf' => 'pdf_combined.pdf']);

        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al combinar los archivos PDF: ' . $e->getMessage());
            return $this->redirect(['upload/index']);
        }
    }

    public function actionMergePdfs_con_mpdf()
    {
        // Define la ruta del directorio de archivos PDF
        $uploadPath = Yii::getAlias('@webroot') . '/uploads/';
        $pdfFiles = glob($uploadPath . '*.pdf'); // Obtiene todos los archivos PDF en el directorio

        // Verifica que existan archivos PDF en la carpeta
        if (empty($pdfFiles)) {
            Yii::$app->session->setFlash('error', 'No se han encontrado archivos PDF para combinar en la carpeta de uploads.');
            return $this->redirect(['upload/index']);
        }

        try {
            // Inicializa mPDF
            $mpdf = new Mpdf(['mode' => 'utf-8']);

            // Itera sobre cada archivo PDF y lo agrega al documento combinado
            foreach ($pdfFiles as $filePath) {
                $pageContent = file_get_contents($filePath);
                $mpdf->WriteHTML($pageContent);
            }

            // Guarda el archivo PDF combinado
            $mergedFilePath = $uploadPath . 'pdf_combined.pdf';
            $mpdf->Output($mergedFilePath, \Mpdf\Output\Destination::FILE);

            Yii::$app->session->setFlash('success', 'Los archivos PDF se han combinado correctamente.');
            
            // Redirige a la acción de descarga del archivo combinado
            return $this->redirect(['upload/download', 'filename' => 'pdf_combined.pdf']);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al combinar los archivos PDF: ' . $e->getMessage());
            return $this->redirect(['upload/index']);
        }
    }



    // public function actionDownloadMergedPdf()
    // {
    //     $mergedPdfPath = Yii::$app->request->get('file');
    //     if (file_exists($mergedPdfPath)) {
    //         return Yii::$app->response->sendFile($mergedPdfPath);
    //     }
    //     throw new \yii\web\NotFoundHttpException("Archivo no encontrado");
    // }


    public function actionDownloadMergedPdf()
    {
        $uploadPath = Yii::getAlias('@webroot/uploads/');
        $mergedFilePath = $uploadPath . 'pdf_combined.pdf';

        if (!file_exists($mergedFilePath)) {
            Yii::$app->session->setFlash('error', 'El archivo combinado no está disponible.');
            return $this->redirect(['upload/index']);
        }

        // Configura la respuesta para la descarga del archivo
        Yii::$app->response->sendFile($mergedFilePath, 'pdf_combined.pdf', ['inline' => true])->send();

        // Elimina todos los archivos PDF en el directorio /uploads
        foreach (glob($uploadPath . '*.pdf') as $file) {
            if (is_file($file)) {
                unlink($file); // Elimina cada archivo
            }
        }

        // Evita que cualquier otra salida interfiera
        Yii::$app->end();
    }

}
