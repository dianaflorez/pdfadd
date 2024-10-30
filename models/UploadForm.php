<?php
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    public $upload_files;

    public function rules()
    {
        return [
            [['upload_files'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf', 'maxFiles' => 1],
        ];
    }
    
    public function upload($filePath)
    {
        return $this->upload_files->saveAs($filePath);
    }
}
