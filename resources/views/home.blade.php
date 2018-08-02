@extends('layouts.layui')

@section('title')
    所有csv文件
@endsection

@section('content')
    <table id="all_csv_file_table_id" lay-filter="all_csv_file_table_filter"></table>
    <!-- 工具栏 -->
    <script type="text/html" id="operateBar">
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="delete">删除</a>
        <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="download">下载</a>
    </script>
    <!-- 工具栏 End -->
@endsection

@section('script')
    <script>
        // layui.use(['table', 'jquery'], function () {
        var table = layui.table,
            $ = layui.jquery;
        //第一个实例
        table.render({
            elem: '#all_csv_file_table_id'
            , height: 315
            , url: '{{route('allCSVFile')}}' //数据接口
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'id', title: '文件ID', width: '10%', sort: true, fixed: 'left'}
                , {field: 'folder_name', title: '文件名', edit: 'text', width: '15%'}
                , {field: 'content', title: '文件内容', width: '25%'}
                , {field: 'header_id', title: '头文件ID', width: '10%'}
                , {field: 'header_name', title: '头文件名', width: '15%'}
                , {field: 'type', title: '文件类型', width: '10%', sort: true}
                , {
                    field: 'operate',
                    title: '操作',
                    width: '15%',
                    fixed: 'right',
                    align: 'center',
                    toolbar: '#operateBar'
                }
            ]]
        });

        //监听操作工具条
        table.on('tool(all_csv_file_table_filter)', function (obj) {
            var data = obj.data;
            var tr = obj.tr; //获得当前行 tr 的DOM对象
            if (obj.event === 'delete') {
                layer.confirm('真的删除该行？', function (index) {
                    obj.del();
                    layer.close(index);
                    //请求服务器删除该条记录
                    $.get("{{route('deleteCSVFile')}}" + "?id=" + obj.data.id, function (data, status) {
                        if (data.code == 0) {
                            alert('操作成功 msg : ' + data.msg);
                        } else if (data.code == 1) {
                            alert('操作失败 msg : ' + data.msg);
                        }
                    });
                });
            } else if (obj.event === 'edit') {
                if (obj.data.type == "csv头文件")
                    window.location.href = "{{route('CSVHeaderFile')}}" + "?id=" + obj.data.id;
            } else if (obj.event === 'download') {
                if (obj.data.type == "csv头文件")
                    window.location.href = "{{route('downloadCSVHeaderFile')}}" + '/' + obj.data.id;
            }
        });

        table.on('edit(all_csv_file_table_filter)', function (obj) { //注：edit是固定事件名，test是table原始容器的属性 lay-filter="对应的值"
            console.log(obj.value); //得到修改后的值
            console.log(obj.field); //当前编辑的字段名
            console.log(obj.data); //所在行的所有相关数据

        });
        // });
    </script>
@endsection