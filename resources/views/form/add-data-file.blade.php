<div style="margin: 15px;">
    <form class="layui-form">
        <div class="layui-form-item">
            <label class="layui-form-label">文件名</label>
            <div class="layui-input-block">
                <input type="text" name="file_name" placeholder="请输入文件名" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">选择头文件</label>
            <div class="layui-input-block">
                <select name="header_id" lay-verify="required" lay-search>
                    <option value=""></option>
                    @foreach($files as $file)
                        <option value="{{$file->id}}">{{$file->id}}&nbsp;&nbsp;{{$file->folder_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button lay-filter="submit" lay-submit style="display: none;"></button>
    </form>
</div>