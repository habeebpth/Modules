<?php

namespace Modules\HotelManagement\Http\Controllers;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\HotelManagement\Entities\PropertyFile;

class PropertyFileController extends AccountBaseController
{
    /**
     * @param Request $request
     * @return mixed|void
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(Request $request)
    {

        if ($request->hasFile('file')) {

            $this->storeFiles($request);

            $this->files = PropertyFile::where('property_id', $request->property_id)->orderByDesc('id')->get();
            $view = view('hotelmanagement::hm-properties.files.show', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'view' => $view]);
        }
    }

    public function storeMultiple(Request $request)
    {
        if ($request->hasFile('file')) {
            $this->storeFiles($request);
        }
    }

    private function storeFiles($request)
    {
        foreach ($request->file as $fileData) {

            $file = new PropertyFile();
            $file->property_id = $request->property_id;

            $filename = Files::uploadLocalOrS3($fileData, PropertyFile::FILE_PATH . '/' . $request->property_id);

            $file->filename = $fileData->getClientOriginalName();
            $file->hashname = $filename;
            $file->size = $fileData->getSize();
            $file->save();
        }
    }

    public function destroy(Request $request, $id)
    {
        $file = PropertyFile::findOrFail($id);

        Files::deleteFile($file->hashname, PropertyFile::FILE_PATH . '/' . $file->property_id);

        PropertyFile::destroy($id);

        $this->files = PropertyFile::where('property_id', $file->property_id)->orderByDesc('id')->get();

        $view = view('hotelmanagement::hm-properties.files.show', $this->data)->render();

        return Reply::successWithData(__('messages.deleteSuccess'), ['view' => $view]);
    }

    public function download($id)
    {
        $file = PropertyFile::whereRaw('md5(id) = ?', $id)->firstOrFail();
        return download_local_s3($file, PropertyFile::FILE_PATH . '/' . $file->property_id . '/' . $file->hashname);

    }

}
