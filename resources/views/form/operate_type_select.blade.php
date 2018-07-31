<html>
<head></head>
<body>
<div style="margin: 15px;">
    <form class="layui-form">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">操作类型</label>
                <div class="layui-input-inline">
                    <select name="operate_type" lay-verify="required">
                        <option value=""></option>
                        @foreach($operateType as $key=>$value)
                            <optgroup label="{{$key}}">
                                @foreach($value as $subKey=>$subValue)
                                    <option value="{{$subKey}}">{{$subValue}}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <button style="display: none;" lay-submit="" lay-filter="submit"></button>
    </form>
</div>
</body>
</html>