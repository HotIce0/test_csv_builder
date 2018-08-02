<?php

namespace App\Http\Controllers;

use App\ContentItem;
use App\File;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CSVDataController extends Controller{
    public function CSVDataManage(Request $request){
        return view('csv_data');
    }

    public function CSVDataManageALL(Request $request){
        $files = File::leftJoin('file as fileo', 'file.header_id', '=', 'fileo.id')
        ->where([
                ['file.user_id', '=', $request->user()->id],
                ['file.type', '=', config('constants.FILE_TYPE_CSV_DATA')],
            ])
            ->select('file.id', 'file.type', 'file.folder_name', 'file.content', 'file.header_id', 'fileo.folder_name as header_name')
            ->offset(($request->page - 1) * $request->limit)
            ->limit($request->limit)
            ->get();
        $count = File::where([
                ['file.user_id', '=', $request->user()->id],
                ['file.type', '=', config('constants.FILE_TYPE_CSV_DATA')],
            ])->count();
        return response()->json([
            'code' => 0,
            'count' => $count,
            'data' => $files->toArray()
        ]);
    }

    public function CSVDataManageADD(Request $request){
        $headerFile = File::find($request->header_id);
        $file = new File();
        if($headerFile->file_name->search("_once_head") != -1){
            $file->folder_name = $request->file_name."_once_data";
        }else {
            $file->folder_name = $request->file_name."_data";
        }
        $file->type = config('constants.FILE_TYPE_CSV_DATA');
        $file->header_id = $request->header_id;
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

    public function CSVDataManageRename(Request $request){
        $file = File::find($request->id);
        $file->folder_name = $request->file_name;
        try{
            $file->save();
            return response()->json([
                'code' => 0,
                'msg' => '重命名数据文件成功!',
            ]);
        }catch (\Exception $e){
            return response()->json([
                'code' => 1,
                'msg' => '重命名数据文件失败，文件名已存在!',
            ]);
        }
    }

    public function CSVDataFile(Request $request){
        $file = File::find($request->id);
        //获取该数据文件对应的头文件ID
        $headerFileID = File::find($request->id)->header_id;
        //获取对应头文件的所有Content Item
        $headerFileContentItems = ContentItem::where('file_id', '=', $headerFileID)
            ->get();
        $headerItems = array();
        foreach ($headerFileContentItems as $contentItem){
            array_push($headerItems, [
                'id'=>$contentItem->id,
                'operateName'=>json_decode($contentItem->content,true)['operateName'],
                'operateType'=>json_decode($contentItem->content,true)['operateType'],
            ]);
        }
        return view('csv_data_edit', [
            'id' => $request->id,
            'headerItems' => $headerItems,
            'file' => $file,
        ]);
    }

    public function allCSVDataFileItem($id, Request $request){
        //获取该数据文件对应的头文件ID
        $headerFileID = File::find($request->id)->header_id;
        //获取对应头文件的所有Content Item
        $headerFileContentItems = ContentItem::where('file_id', '=', $headerFileID)
            ->get();
        //所有列
        $headerItems = array();
        foreach ($headerFileContentItems as $contentItem){
            $arrayContent = json_decode($contentItem->content,true);
            array_push($headerItems, [
                'id'=>$contentItem->id,
                'operateName'=>$arrayContent['operateName'],
                'operateType'=>$arrayContent['operateType'],
            ]);
        }
        //获取该数据文件，所有数据项（测试用例）
        $dataFileContentItems = ContentItem::where('file_id', '=', $id)
            ->get();
        //解析所有的数据项
        $dataItems = array();
        foreach ($dataFileContentItems as $contentItem){
            $arrayContent = json_decode($contentItem->content,true);
            $item = [
                'id'=>$contentItem->id,
            ];
            foreach ($arrayContent as $key=>$value){
                $item[$key] = $value;
            }
            array_push($dataItems, $item);
        }
        return response()->json([
            'code' => 0,
            'count' => count($dataItems),
            'data' => $dataItems,
        ]);
    }

    public function deleteCSVDataFileItem($id, Request $request){
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

    public function editCSVDataFileItem($id, Request $request){
        $header_id = File::find($id)->header_id;
        //获取对应头文件的所有Content Item
        $headerFileContentItems = ContentItem::where('file_id', '=', $header_id)
            ->get();
        //所有列
        $headerItems = array();
        foreach ($headerFileContentItems as $contentItem){
            $arrayContent = json_decode($contentItem->content,true);
            array_push($headerItems, [
                'id'=>$contentItem->id,
                'operateName'=>$arrayContent['operateName'],
                'operateType'=>$arrayContent['operateType'],
            ]);
        }
        if($request->isMethod('get')){
            $contentItems = null;
            if($request->content_item_id !== null){
                $dataFileContentItem = ContentItem::find($request->content_item_id);
                $contentItems = json_decode($dataFileContentItem->content, true);
            }
            return view('form.add-data-item', [
                'headerItems'=>$headerItems,
                'contentItems'=>$contentItems,
            ]);
        }elseif ($request->isMethod('post')){
            if($request->content_item_id == null){
                $contentItem = new ContentItem();

                $fields = $request->all();
                array_pull($fields,'_token');
                $contentItem->content = json_encode($fields);
                $contentItem->file_id = $id;
                if ($contentItem->save())
                    return response()->json([
                        'code' => 0,
                        'msg' => '项添加成功!',
                    ]);
                else
                    return response()->json([
                        'code' => 1,
                        'msg' => '项添加失败!',
                    ]);
            }else{
                $contentItem = ContentItem::find($request->content_item_id);
                $fields = $request->all();
                array_pull($fields,'_token');
                $contentItem->content = json_encode($fields);
                if ($contentItem->save())
                    return response()->json([
                        'code' => 0,
                        'msg' => '项修改成功!',
                    ]);
                else
                    return response()->json([
                        'code' => 1,
                        'msg' => '项修改失败!',
                    ]);
            }
        }
    }

    public function saveCSVDataFileItem($id, Request $request){
        //查询数据文件
        $dataFile = File::find($id);
        $dataFileContentItems = ContentItem::where([
            ['file_id', '=', $dataFile->id]
        ])->get();
        //查询头部文件
        //获取对应头文件的所有Content Item
        $headerFileContentItems = ContentItem::where('file_id', '=', $dataFile->header_id)
            ->get();
        //所有列
        $headerItems = array();
        foreach ($headerFileContentItems as $contentItem){
            $arrayContent = json_decode($contentItem->content,true);
            array_push($headerItems, [
                'id'=>$contentItem->id,
                'operateType'=>$arrayContent['operateType'],
            ]);
        }
        //解析所有数据
        $data = '';
        foreach ($dataFileContentItems as $dataFileContentItem){
            $dataContentTemp = json_decode($dataFileContentItem->content, true);
            $data.=($dataContentTemp['no'].',');
            for ($i = 0; $i < count($headerItems); $i++){
                if($headerItems[$i]['operateType'] == 'button')
                    $data.=',';
                else
                    $data.=(','.($dataContentTemp[$headerItems[$i]['id']]==''?"#None":$dataContentTemp[$headerItems[$i]['id']]));
            }
            $data.="\r\n";
        }
        $dataFile->content = $data;
        if ($dataFile->save())
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

    public function copyCSVHeaderFileItem($id, Request $request){
        $contentItem = ContentItem::find($request->item_id);
        $itemNew = new ContentItem();
        $itemNew->file_id = $id;
        $itemNew->content = $contentItem->content;
        if ($itemNew->save())
            return response()->json([
                'code' => 0,
                'msg' => '数据项复制成功!',
            ]);
        else
            return response()->json([
                'code' => 1,
                'msg' => '数据项复制失败!',
            ]);
    }
}