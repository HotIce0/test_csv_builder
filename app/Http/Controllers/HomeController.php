<?php

namespace App\Http\Controllers;

use App\ContentItem;
use App\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function downloadCSVFile($id, Request $request)
    {
        $file = File::find($id);
        //$filePath = $request->user()->id.'/'.$request->session()->getId().'/'.$file->folder_name.($request->once==1?"_once":"").($request->is_head==1?"_head":"_data").'.csv';
        $filePath = $request->user()->id.'/'.$request->session()->getId().'/'.$file->folder_name.'.csv';
        Storage::disk('public')->put($filePath, $file->content);
        return Storage::disk('public')->download($filePath);
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('home');
    }
    public function allCSVFile(Request $request)
    {
        $files = File::leftJoin('file as fileo', 'file.header_id', '=', 'fileo.id')
            ->where('file.user_id', '=', $request->user()->id)
            ->select(DB::raw("file.id,file.folder_name,file.content,file.header_id,(case file.type when 0 then 'csv头文件' when 1 then 'csv数据文件' when 2 then 'csv链接文件' end)type,fileo.folder_name as header_name"))
            ->offset(($request->page - 1) * $request->limit)
            ->limit($request->limit)
            ->get();
        return response()->json([
            'code' => 0,
            'count' => 1,
            'data' => $files->toArray()
        ]);
    }
    public function deleteCSVFile(Request $request){
        $file = File::where([
                ['id', '=', $request->id],
                ['user_id', '=', $request->user()->id]
            ])->first();
        //判断拥有，还有存在
        if($file == null){
            return response()->json([
                'code' => 1,
                'msg' => '文件不存在或者您没有对该csv文件的操作权限!',
            ]);
        }
        if($file->delete())
            return response()->json([
                'code' => 0,
                'msg' => 'csv文件删除成功!',
            ]);
        else
            return response()->json([
                'code' => 1,
                'msg' => 'csv文件删除失败!',
            ]);
    }
}
