<?php

namespace App\Http\Controllers;

use App\ContentItem;
use App\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class CSVHeaderController extends Controller
{
    public function CSVHeaderManage(Request $request)
    {
        return view('csv_header');
    }

    public function CSVHeaderManageALL(Request $request)
    {
        $files = File::where([
            ['user_id', '=', $request->user()->id],
            ['type', '=', config('constants.FILE_TYPE_CSV_HEADER')]])
            ->select('id', 'folder_name', 'content')
            ->offset(($request->page - 1) * $request->limit)
            ->limit($request->limit)
            ->get();
        $count = File::where([
            ['user_id', '=', $request->user()->id],
            ['type', '=', config('constants.FILE_TYPE_CSV_HEADER')]])
            ->count();
        return response()->json([
            'code' => 0,
            'count' => $count,
            'data' => $files->toArray()
        ]);
    }

    public function CSVHeaderManageADD(Request $request){
        $file = new File();
        $file->folder_name = $request->file_name;
        $file->type = config('constants.FILE_TYPE_CSV_HEADER');
        $file->user_id = $request->user()->id;
        try{
            $file->save();
            return response()->json([
                'code' => 0,
                'msg' => '新增头文件成功!',
            ]);
        }catch (\Exception $e){
            return response()->json([
                'code' => 1,
                'msg' => '新增头文件失败，文件名已存在!',
            ]);
        }
    }

    public function CSVHeaderManageRename(Request $request){
        $file = File::find($request->id);
        $file->folder_name = $request->file_name;
        try{
            $file->save();
            return response()->json([
                'code' => 0,
                'msg' => '重命名头文件成功!',
            ]);
        }catch (\Exception $e){
            return response()->json([
                'code' => 1,
                'msg' => '重命名头文件失败，文件名已存在!',
            ]);
        }
    }

    public function CSVHeaderManageOperateType(Request $request){
        $item = ContentItem::find($request->id);
        $content = json_decode($item->content, true);
        $content['operateType'] = $request->operate_type;
        $item->content = json_encode($content);
        try{
            $item->save();
            return response()->json([
                'code' => 0,
                'msg' => '修改操作类型成功!',
            ]);
        }catch (\Exception $e){
            return response()->json([
                'code' => 1,
                'msg' => '修改操作类型失败!',
            ]);
        }
    }

    //编辑
    public function CSVHeaderFile(Request $request)
    {
        $file = File::find($request->id);
        return view('csv_header_edit', [
            'id' => $request->id,
            'file' => $file,
        ]);
    }

    public function allCSVHeaderFileItem($id, Request $request)
    {
        $items = ContentItem::where('file_id', '=', $id)
            ->select('id', 'content')
            ->get();
        //把content中的属性解析到item中
        $itemsArray = $items->toArray();
        for ($i = 0; $i < count($itemsArray); $i++) {
            $obj = json_decode($itemsArray[$i]["content"]);
            foreach ($obj as $key => $value) {
                $itemsArray[$i][$key] = $value;
            }
            array_pull($itemsArray[$i], 'content');
        }
        return response()->json([
            'code' => 0,
            'count' => 1,
            'data' => $itemsArray
        ]);
    }

    public function addCSVHeaderFileItem($id, Request $request)
    {
        $itemNew = new ContentItem();
        $itemNew->file_id = $id;
        $itemNew->content = config('constants.EMPTY_HEADER_ITEM_STRUCT');
        if ($itemNew->save())
            return response()->json([
                'code' => 0,
                'msg' => '新项添加成功!',
            ]);
        else
            return response()->json([
                'code' => 1,
                'msg' => '新项添加失败!',
            ]);
    }

    public function deleteCSVHeaderFileItem($id, Request $request)
    {
        $item = ContentItem::where([
            ['file_id', '=', $id],
            ['id', '=', $request->item_id]
        ])
            ->select('id', 'content')
            ->first();
        if ($item == null) {
            return response()->json([
                'code' => 1,
                'msg' => '项不存在或者您没有对该项的操作权限!',
            ]);
        }
        if ($item->delete())
            return response()->json([
                'code' => 0,
                'msg' => '项删除成功!',
            ]);
        else
            return response()->json([
                'code' => 1,
                'msg' => '项删除失败!',
            ]);
    }

    public function editCSVHeaderFileItem($id, Request $request)
    {
        $data = json_decode($request->item_content, true);
        $item = ContentItem::where([
            ['file_id', '=', $id],
            ['id', '=', $data['id']]
        ])
            ->first();
        array_pull($data, 'id');

        if ($item == null) {
            return response()->json([
                'code' => 1,
                'msg' => '项不存在或者您没有对该项的操作权限!',
            ]);
        }
        $item->content = json_encode($data);
        if ($item->save())
            return response()->json([
                'code' => 0,
                'msg' => '修改项添加成功!',
            ]);
        else
            return response()->json([
                'code' => 1,
                'msg' => '修改项添加失败!',
            ]);
    }

    public function saveCSVHeaderFileItem($id, Request $request)
    {
        $items = ContentItem::where('file_id', '=', $id)
            ->get()
            ->toArray();
        $csvData = '';
        $keyArray = config('constants.HEADER_ORDER');

        foreach ($keyArray as $value) {
            $csvData .= 'no,res';
            for ($i = 0; $i < count($items); $i++) {
                $csvData .= ',' . (json_decode($items[$i]['content'], true)[$value]);
            }
            $csvData .= "\r\n";
        }

        $file = File::find($id);
        $file->content = $csvData;
        if ($file->save())
            return response()->json([
                'code' => 0,
                'msg' => '文件内容保存成功!',
            ]);
        else
            return response()->json([
                'code' => 1,
                'msg' => '文件内容保存失败!',
            ]);
    }

    public function downloadCSVHeaderFile($id, Request $request)
    {
        $file = File::find($id);
        Storage::disk('public')->put($request->session()->getId() . '.txt', $file->content);
        return Storage::download('public/' . $request->session()->getId() . '.txt');
    }
}