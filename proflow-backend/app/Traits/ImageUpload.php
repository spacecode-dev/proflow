<?php

namespace App\Traits;

use URL;
use Storage;
use Illuminate\Http\File;

trait ImageUpload
{

    //api image
    public function addImage($data, $model, $attribute)
    {

        if ($data->hasFile($attribute)) {
            $file = $data->file($attribute);
            $fileName = time() . '.' . $file->getClientOriginalExtension();

            $imagePath =  Storage::putFileAs($attribute, new File($file), $fileName);
            $imageresizePath = Storage::putFileAs($attribute . '/tmp', new File($file), $fileName);

            $img = \Image::make($file->getRealPath());
            // save thumbnail image
            $img->resize(200, 200, function ($constraint) {
                $constraint->aspectRatio();
            })->save(storage_path() . '/app/public/' . $imageresizePath);
            $model->$attribute = $fileName;
            $model->save();
        }
    }


    public function updateIssueFileImage($data, $model, $attribute, $folderName)
    {
      
        self::deleteIssueFileImage($folderName, $model->$attribute);
        if ($data->hasFile($attribute)) {
            $file = $data->file($attribute);
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $imagePath =  Storage::putFileAs($folderName, new File($file), $fileName);
            $model->$attribute = $fileName;
            $model->save();
        }
    }

    public function deleteIssueFileImage($folderName, $fileName) {
        Storage::delete($folderName.'/'. $fileName);
    }


    public function updateImage($foldername, $model, $attribute)
    {
            self::addImage($foldername, $model, $attribute);
    }

   

}
