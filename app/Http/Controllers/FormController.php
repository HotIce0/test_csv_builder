<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Http\Request;

class FormController extends Controller{
    public function operateTypeSelect(Request $request){
        return view('form.operate_type_select', [
            'operateType' => config('constants.OPERATE_TYPE'),
        ]);
    }

    public function addDataFile(Request $request){
        $files = File::where([
            ['type', '=', config('constants.FILE_TYPE_CSV_HEADER')],
            ['user_id', '=', $request->user()->id],
        ])->select('id', 'folder_name')
            ->get();
        return view('form.add-data-file', [
            'files' => $files,
        ]);
    }
}