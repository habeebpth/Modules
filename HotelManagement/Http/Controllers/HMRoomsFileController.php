<?php

namespace Modules\HotelManagement\Http\Controllers;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\HotelManagement\Entities\HmRoomFile;

class HMRoomsFileController extends AccountBaseController
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

            $this->files = HmRoomFile::where('hm_room_id', $request->hm_room_id)->orderByDesc('id')->get();
            $view = view('hotelmanagement::hm-rooms.files.show', $this->data)->render();

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

            $file = new HmRoomFile();
            $file->hm_room_id = $request->hm_room_id;

            $filename = Files::uploadLocalOrS3($fileData, HmRoomFile::FILE_PATH . '/' . $request->hm_room_id);

            $file->filename = $fileData->getClientOriginalName();
            $file->hashname = $filename;
            $file->size = $fileData->getSize();
            $file->save();
        }
    }

    public function destroy(Request $request, $id)
    {
        $file = HmRoomFile::findOrFail($id);

        Files::deleteFile($file->hashname, HmRoomFile::FILE_PATH . '/' . $file->hm_room_id);

        HmRoomFile::destroy($id);

        $this->files = HmRoomFile::where('hm_room_id', $file->hm_room_id)->orderByDesc('id')->get();

        $view = view('hotelmanagement::hm-rooms.files.show', $this->data)->render();

        return Reply::successWithData(__('messages.deleteSuccess'), ['view' => $view]);
    }

    public function download($id)
    {
        $file = HmRoomFile::whereRaw('md5(id) = ?', $id)->firstOrFail();
        return download_local_s3($file, HmRoomFile::FILE_PATH . '/' . $file->hm_room_id . '/' . $file->hashname);

    }

}
