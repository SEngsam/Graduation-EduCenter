<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function saveImage(Request $request, $folder, $oldName = null)
    {
        if (!$request->hasFile('image')) {
            return $oldName;
        }

        if ($oldName) {
            $this->deleteImage($folder, $oldName);
        }

        $name = time() . '_' . $request->file('image')->hashName();
        $request->file('image')->storeAs($folder, $name, 'uploads');

        return $name;
    }

    protected function deleteImage($folder, $name)
    {
        if ($name) {
            Storage::disk('uploads')->delete($folder . '/' . $name);
        }
    }
}
