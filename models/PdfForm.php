<?php
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class PdfForm extends Model
{
    public $pdfFiles;

    public function rules()
    {
        return [
            [['pdfFiles'], 'file', 'extensions' => 'pdf', 'maxFiles' => 10], // Limita a 10 archivos PDF
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $paths = [];
            foreach ($this->pdfFiles as $file) {
                $path = 'uploads/' . $file->baseName . '.' . $file->extension;
                $file->saveAs($path);
                $paths[] = $path;
            }
            return $paths;
        }
        return false;
    }
}
