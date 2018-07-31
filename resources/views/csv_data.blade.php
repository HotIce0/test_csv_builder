@extends('layouts.layui')

@section('content')
    <blockquote class="layui-elem-quote">
        <a>CSV数据文件管理&nbsp; &nbsp;&nbsp;</a>
        <a href="javascript:;" class="layui-btn layui-btn-small" id="add">
            <i class="layui-icon">&#xe608;</i> 添加
        </a>
    </blockquote>

    <table id="csv_data_file_table_id" lay-filter="csv_data_file_table_filter"></table>
    <!-- 工具栏 -->
    <script type="text/html" id="operateBar">
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑文件</a>
        <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="delete">删除</a>
        <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="download">下载</a>
        <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="downloadOnce">下载(一次执行)</a>
    </script>
    <!-- 工具栏 End -->
@endsection

@section('script')
    <script>
        var table = layui.table,
            layerTips = parent.layer === undefined ? layui.layer : parent.layer, //获取父窗口的layer对象
            layer = layui.layer, //获取当前窗口的layer对象
            $ = layui.jquery;

        table.render({
            elem: '#csv_data_file_table_id'
            , height: 500
            , url: '{{route('CSVDataManageALL')}}' //数据接口
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'id', title: 'ID', width: '10%', sort: true, fixed: 'left'}
                , {field: 'folder_name', title: '文件名', edit: 'text', width: '15%'}
                , {field: 'content', title: '文件内容', width: '20%'}
                , {field: 'header_id', title: '头文件ID', width: '15%'}
                , {field: 'header_name', title: '头文件名', width: '15%'}
                , {field: 'operate', title: '操作', width: '25%', fixed: 'right', align: 'center', toolbar: '#operateBar'}
            ]]
        });

        //监听操作工具条
        table.on('tool(csv_data_file_table_filter)', function (obj) {
            var data = obj.data;
            var tr = obj.tr; //获得当前行 tr 的DOM对象
            if (obj.event === 'delete') {
                layer.confirm('真的删除该行？', function (index) {
                    obj.del();
                    layer.close(index);
                    //请求服务器删除该条记录
                    $.get("{{route('deleteCSVFile')}}" + "?id=" + obj.data.id, function (data, status) {
                        if (data.code == 0) {
                            layer.msg(data.msg);
                        } else if (data.code == 1) {
                            layer.msg(data.msg);
                        }
                    });
                });
            } else if (obj.event === 'edit') {
                window.location.href = "{{route('CSVDataFile')}}" + "?id=" + obj.data.id;
            }  else if(obj.event === 'download'){
                window.location.href = "{{route('downloadCSVFile')}}"+"/"+obj.data.id+'?once=0&is_head=0';
            } else if (obj.event === 'downloadOnce'){
                window.location.href = "{{route('downloadCSVFile')}}" + '/' + obj.data.id+'?once=1&is_head=0';
            }
        });

        table.on('edit(csv_data_file_table_filter)', function(obj){ //注：edit是固定事件名，test是table原始容器的属性 lay-filter="对应的值"
            var contentData = obj.data;
            //请求服务器新增记录
            $.get("{{route('CSVDataManageRename')}}"+"?id="+contentData.id+'&file_name='+contentData.folder_name, function (data, status){
                if(data.code == 0){
                    layer.msg(data.msg);
                }else if(data.code == 1){
                    table.reload('csv_data_file_table_id',{
                        url: '{{route('CSVDataManageALL')}}'
                    });
                    layer.msg(data.msg);
                }
            });
        });

        //添加数据文件
        var addBoxIndex = -1;
        $('#add').on('click', function () {
            if(addBoxIndex !== -1)
                return;
            //本表单通过ajax加载 --以模板的形式，当然你也可以直接写在页面上读取
            $.get('{{route('addDataFile')}}', null, function(form) {
                addBoxIndex = layer.open({
                    type: 1,
                    title: '新增CSV数据文件',
                    content: form,
                    btn: ['保存', '取消'],
                    shade: false,
                    offset: ['0px', '0%'],
                    area: ['600px', '500px'],
                    zIndex: 19950924,
                    maxmin: true,
                    yes: function(index) {
                        //触发表单的提交事件
                        $('form.layui-form').find('button[lay-filter=submit]').click();
                    },
                    full: function(elem) {
                        var win = window.top === window.self ? window : parent.window;
                        $(win).on('resize', function() {
                            var $this = $(this);
                            elem.width($this.width()).height($this.height()).css({
                                top: 0,
                                left: 0
                            });
                            elem.children('div.layui-layer-content').height($this.height() - 95);
                        });
                    },
                    success: function(layero, index) {
                        //弹出窗口成功后渲染表单
                        var form = layui.form;
                        form.render();
                        form.on('submit(submit)', function(data) {
                            //这里可以写ajax方法提交表单
                            $.get('{{route('CSVDataManageADD')}}'+'?file_name='+data.field.file_name+'&header_id='+data.field.header_id, function (data) {
                                if (data.code == 0) {
                                    table.reload('csv_data_file_table_id',{
                                        url: '{{route('CSVDataManageALL')}}'
                                    });
                                    layer.msg(data.msg);
                                    location.reload(); //刷新
                                } else if (data.code == 1) {
                                    layer.msg(data.msg);
                                }
                            })
                            return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
                        });
                    },
                    end: function() {
                        addBoxIndex = -1;
                    }
                });
            });
        });
    </script>
@endsection