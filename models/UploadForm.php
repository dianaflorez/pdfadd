<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;
use setasign\Fpdi\Fpdi;

class UploadForm extends Model
{
    public $upload_files;
    public $isMerged = false; // Propiedad para verificar si se ha unido el PDF

    public function rules()
    {
        return [
            [['upload_files'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf', 'maxFiles' => 10],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $files = [];
            foreach ($this->upload_files as $file) {
                $filePath = 'uploads/' . $file->baseName . '.' . $file->extension;
                $file->saveAs($filePath);
                $files[] = $filePath;
            }
            $this->isMerged = $this->mergePdfs($files); // Cambia esto para asignar el resultado
            return true;
        } else {
            return false;
        }
    }

    public function mergePdfs($files)
    {
        $pdf = new Fpdi();

        foreach ($files as $file) {
            $pageCount = $pdf->setSourceFile($file);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplId = $pdf->importPage($pageNo);
                $pdf->AddPage();
                $pdf->useTemplate($tplId);
            }
        }

        // Guarda el PDF combinado
        $outputPath = 'uploads/merged.pdf';
        $pdf->Output('F', $outputPath);
        
        // Retorna true si el PDF se creó exitosamente
        return file_exists($outputPath);
    }
}
