@extends('layouts.layui')

@section('content')
    <blockquote class="layui-elem-quote">
        <a>CSV头文件编辑&nbsp;({{$file->folder_name}}) &nbsp;&nbsp;</a>
        <button class="layui-btn layui-btn-small" id="addHeader">
            <i class="layui-icon">&#xe654;</i>
            <!-- 新增 -->
        </button>
        <button class="layui-btn layui-btn-small" id="saveHeader">
            <!-- 保存 -->
            <i class="layui-icon">保存</i>
        </button>
    </blockquote>
    <table id="csv_header_file_table_id" lay-filter="csv_header_file_table_filter"></table>
    <!-- 工具栏 -->
    <script type="text/html" id="operateBar">
        <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="delete">删除</a>
    </script>
    <!-- 工具栏 End -->
@endsection

@section('script')
    <script>
        // layui.use(['table', 'jquery'], function(){
        var table = layui.table,
            layerTips = parent.layer === undefined ? layui.layer : parent.layer, //获取父窗口的layer对象
            layer = layui.layer, //获取当前窗口的layer对象
            $ = layui.jquery;
        //第一个实例
        table.render({
            elem: '#csv_header_file_table_id'
            , height: 500
            , url: '{{route('allCSVHeaderFileItem', $id)}}' //数据接口
            , cols: [[ //表头
                {field: 'id', title: 'ID', width: '10%', sort: true, fixed: 'left'}
                , {field: 'operateName', title: '操作名称', edit: 'text', width: '15%'}
                , {field: 'operateType', title: '操作类型', width: '30%', event: 'operateTypeEvent'}
                , {field: 'delay', title: '延时', edit: 'text', width: '10%'}
                , {field: 'xpath', title: '元素路径xpath', edit: 'text', width: '25%'}
                , {field: 'operate', title: '操作', width: '10%', fixed: 'right', align: 'center', toolbar: '#operateBar'}
            ]]
            , id: 'csv_header_file_table_id'
            , limit: 99999
        });
        var operateTypeIndex = -1;
        //监听操作工具条
        table.on('tool(csv_header_file_table_filter)', function (obj) {
            var data = obj.data;
            var tr = obj.tr; //获得当前行 tr 的DOM对象
            if (obj.event === 'delete') {
                layer.confirm('真的删除该行？', function (index) {
                    obj.del();
                    layer.close(index);
                    //请求服务器删除该条记录
                    $.get("{{route('deleteCSVHeaderFileItem', $id)}}" + "?item_id=" + obj.data.id, function (data, status) {
                        if (data.code == 0) {

                        } else if (data.code == 1) {
                            alert('操作失败 msg : ' + data.msg);
                            table.reload('csv_header_file_table_id', {
                                url: '{{route('allCSVHeaderFileItem', $id)}}'
                            });
                        }
                    });
                });
            } else if (obj.event === 'operateTypeEvent') {
                //修改操作类型
                if(operateTypeIndex !== -1)
                    return;
                else
                    operateTypeIndex = 1;
                //本表单通过ajax加载 --以模板的形式，当然你也可以直接写在页面上读取
                $.get('{{route('operateTypeSelect')}}', null, function(form) {
                    operateTypeIndex = layer.open({
                        type: 1,
                        title: '修改操作类型',
                        content: form,
                        btn: ['保存', '取消'],
                        shade: false,
                        offset: ['100px', '30%'],
                        area: ['400px', '500px'],
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
                                $.get('{{route('CSVHeaderManageOperateType')}}'+'?id='+obj.data.id+'&operate_type='+data.field.operate_type, function (data) {
                                    if (data.code == 0) {
                                        table.reload('csv_header_file_table_id',{
                                            url: '{{route('allCSVHeaderFileItem', $id)}}'
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
                            operateTypeIndex = -1;
                        }
                    });
                });
            }
        });


        //保存按钮操作
        $("#saveHeader").on('click', function () {
            //请求服务器保存
            $.get("{{route('saveCSVHeaderFileItem', $id)}}", function (data, status) {
                if (data.code == 0) {
                    layer.msg(data.msg);
                } else if (data.code == 1) {
                    layer.msg(data.msg);
                }
            });
        });

        //新增按钮操作
        $("#addHeader").on('click', function () {
            //请求服务器新增记录
            $.get("{{route('addCSVHeaderFileItem', $id)}}", function (data, status) {
                if (data.code == 0) {
                    table.reload('csv_header_file_table_id', {
                        url: '{{route('allCSVHeaderFileItem', $id)}}'
                    });
                } else if (data.code == 1) {
                    alert('操作失败 msg : ' + data.msg);
                }
            });
        });

        table.on('edit(csv_header_file_table_filter)', function (obj) { //注：edit是固定事件名，test是table原始容器的属性 lay-filter="对应的值"
            var contentData = obj.data;
            delete contentData['LAY_TABLE_INDEX']
            //请求服务器新增记录
            $.get("{{route('editCSVHeaderFileItem', $id)}}" + "?item_content=" + JSON.stringify(contentData), function (data, status) {
                if (data.code == 0) {

                } else if (data.code == 1) {
                    alert('操作失败 msg : ' + data.msg);
                }
            });
        });

        // });
    </script>
@endsection