@extends('layouts.layui')

@section('content')
    <blockquote class="layui-elem-quote">
        <a>CSV数据文件编辑&nbsp;({{$file->folder_name}}) &nbsp;&nbsp;</a>
        <button class="layui-btn layui-btn-small" id="add">
            <i class="layui-icon">&#xe654;</i>
            <!-- 新增 -->
        </button>
        <button class="layui-btn layui-btn-small" id="save">
            <!-- 保存 -->
            <i class="layui-icon">保存</i>
        </button>
    </blockquote>

    <table id="csv_data_file_table_id" class="layui-table" lay-data="{height:500, url:'{{route('allCSVDataFileItem', $id)}}', limit:99999}" lay-filter="csv_data_file_table_filter">
        <thead>
        <tr>
            <th lay-data="{field:'id', width:80, sort: true}">ID</th>
            <th lay-data="{field:'no', width:100, sort: true, event:'edit'}">用例编号</th>
            <th lay-data="{field:'res', width:100}">测试结果</th>
            @foreach($headerItems as $item)
                <th lay-data="{field:'{{$item['id']}}', width:150, event:'edit'}">{{$item['operateName']}}:{{$item['operateType']}}</th>
            @endforeach
            <th lay-data="{field: 'operate', title: '操作', width: '150', fixed: 'right', align: 'center', toolbar: '#operateBar'}"></th>
        </tr>
        </thead>
    </table>

    <!-- 工具栏 -->
    <script type="text/html" id="operateBar">
        <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="delete">删除</a>
        <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="copy">复制</a>
    </script>
    <!-- 工具栏 End -->
@endsection

@section('script')
<script>
    var table = layui.table,
        layerTips = parent.layer === undefined ? layui.layer : parent.layer, //获取父窗口的layer对象
        layer = layui.layer, //获取当前窗口的layer对象
        $ = layui.jquery,
        laydate = layui.laydate;

    //监听操作工具条
    var editBoxIndex = -1;
    table.on('tool(csv_data_file_table_filter)', function (obj) {
        var data = obj.data;
        var tr = obj.tr; //获得当前行 tr 的DOM对象
        if (obj.event === 'delete') {
            layer.confirm('真的删除该行？', function (index) {
                obj.del();
                layer.close(index);
                //请求服务器删除该条记录
                $.get("{{route('deleteCSVDataFileItem', $id)}}" + "?item_id=" + obj.data.id, function (data, status) {
                    if (data.code == 0) {
                        layer.msg(data.msg);
                    } else if (data.code == 1) {
                        layer.msg(data.msg);
                    }
                });
            });
        } else if (obj.event === 'edit') {
            if(editBoxIndex != -1)
                return;
            editBoxIndex = 0;
            $.get('{{route('editCSVDataFileItem', $id)}}'+'?content_item_id='+obj.data.id, null, function(form) {
                editBoxIndex = layer.open({
                    type: 1,
                    title: '编辑',
                    content: form,
                    btn: ['保存', '取消'],
                    shade: false,
                    offset: ['100px', '30%'],
                    area: ['800px', '500px'],
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
                        //渲染时间选择器
                        //弹出窗口成功后渲染表单
                        var form = layui.form;
                        form.render();
                        form.on('submit(submit)', function(data) {
                            //这里可以写ajax方法提交表单
                            data.field.res = '';
                            $.post('{{route('editCSVDataFileItem',$id)}}'+'?content_item_id='+obj.data.id, data.field, function (data) {
                                if (data.code == 0) {
                                    table.reload('csv_data_file_table_id',{
                                        url: '{{route('allCSVDataFileItem', $id)}}'
                                    });
                                    layer.close(editBoxIndex);
                                    layer.msg(data.msg);
                                } else if (data.code == 1) {
                                    layer.msg(data.msg);
                                }
                            })
                            return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
                        });
                    },
                    end: function() {
                        editBoxIndex = -1;
                    }
                });
            });
        }else if(obj.event === 'copy'){
            $.get("{{route('copyCSVHeaderFileItem', $id)}}" + "?item_id=" + obj.data.id, function (data, status) {
                if (data.code == 0) {
                    table.reload('csv_data_file_table_id',{
                        url: '{{route('allCSVDataFileItem', $id)}}'
                    });
                    layer.msg(data.msg);
                } else if (data.code == 1) {
                    alert('操作失败 msg : ' + data.msg);
                }
            });
        }

    });

    $('#save').on('click', function () {
        //请求服务器保存
        $.get("{{route('saveCSVDataFileItem', $id)}}", function (data, status) {
            if (data.code == 0) {
                layer.msg(data.msg);
            } else if (data.code == 1) {
                layer.msg(data.msg);
            }
        });
    });

    //添加头文件
    var addBoxIndex = -1;
    $('#add').on('click', function () {
        if(addBoxIndex !== -1)
            return;
        //本表单通过ajax加载 --以模板的形式，当然你也可以直接写在页面上读取
        $.get('{{route('editCSVDataFileItem', $id)}}', null, function(form) {
            addBoxIndex = layer.open({
                type: 1,
                title: '新增',
                content: form,
                btn: ['保存', '取消'],
                shade: false,
                offset: ['100px', '30%'],
                area: ['800px', '500px'],
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
                    //渲染时间选择器
                    laydate.render({
                        elem: '#date'
                    });
                    //弹出窗口成功后渲染表单
                    var form = layui.form;
                    form.render();
                    form.on('submit(submit)', function(data) {
                        //这里可以写ajax方法提交表单
                        data.field.res = '';
                        $.post('{{route('editCSVDataFileItem',$id)}}', data.field, function (data) {
                            if (data.code == 0) {
                                table.reload('csv_data_file_table_id',{
                                    url: '{{route('allCSVDataFileItem', $id)}}'
                                });
                                layer.close(addBoxIndex);
                                layer.msg(data.msg);
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