<?php
use DasRoteQuadrat\BetterContentEditor\Models\Settings;
use System\Classes\MediaLibrary;
use Cms\Helpers\File as FileHelper;
use Illuminate\Http\Request;
use DasRoteQuadrat\BetterContentEditor\Models\Images;

Route::group(['prefix' => 'contenteditor'], function () {
    Route::post('image/upload', 'DasRoteQuadrat\BetterContentEditor\Controllers\ImageController@upload');
    Route::post('image/save', 'DasRoteQuadrat\BetterContentEditor\Controllers\ImageController@save');

    // Additional styles route
    Route::get('styles', 'DasRoteQuadrat\BetterContentEditor\Controllers\AdditionalStylesController@render');
});

Route::post('/dasrotequadrat/image/upload', function (Request $request) {

    if (BackendAuth::getUser()) {
        try {
            if (!Input::hasFile('image')) {
                throw new ApplicationException('File missing from request');
            }

            $uploadedFile = Input::file('image')[0];
            $fileName = $uploadedFile->getClientOriginalName();

            // Convert uppcare case file extensions to lower case
            $extension = strtolower($uploadedFile->getClientOriginalExtension());
            $fileName = File::name($fileName).'.'.$extension;

            // File name contains non-latin characters, attempt to slug the value
            if (!FileHelper::validateName($fileName)) {
                $fileNameSlug = Str::slug(File::name($fileName), '-');
                $fileName = $fileNameSlug.'.'.$extension;
            }
            if (!$uploadedFile->isValid()) {
                throw new ApplicationException($uploadedFile->getErrorMessage());
            }

            $path = Settings::get('image_folder', 'contenteditor');
            $path = MediaLibrary::validatePath($path);

            MediaLibrary::instance()->put(
                $path.'/'.$fileName,
                File::get($uploadedFile->getRealPath())
            );

            list($width, $height) = getimagesize($uploadedFile);

            $item = Images::firstOrCreate(['item' => Input::input('item')]);
            $item->url = MediaLibrary::instance()->getPathUrl($path.'/'.$fileName);
            $item->save();

            return Response::json([
                'url'      => MediaLibrary::instance()->getPathUrl($path.'/'.$fileName),
                'filename' => $fileName,
                'size'     => [
                    $width,
                    $height
                ]
            ]);
        }
        catch (Exception $ex) {
            return $ex;
        }

    }

})->middleware('web');
