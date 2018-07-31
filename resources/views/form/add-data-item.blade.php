<div style="margin: 15px;">
    <form class="layui-form">
        @csrf
        <div class="layui-form-item">
            <label class="layui-form-label">用例编号</label>
            <div class="layui-input-block">
                <input value="{{isset($contentItems)?$contentItems['no']:''}}" type="text" name="no" placeholder="请输入用例编号" autocomplete="off" class="layui-input">
            </div>
        </div>
        @foreach($headerItems as $headerItem)
            <div class="layui-form-item">
                <label class="layui-form-label">{{$headerItem['operateName']}}:{{$headerItem['operateType']}}</label>
                @switch(config('constants.OPERATE_TYPE_INPUT_METHOD')[$headerItem['operateType']])
                    @case('text')
                        <div class="layui-input-block">
                            <input name="{{$headerItem['id']}}" value="{{isset($contentItems)?$contentItems[$headerItem['id']]:''}}" class="layui-input" type="text" placeholder="请输入" autocomplete="off">
                        </div>
                    @break
                    @case('time_selector')
                        <div class="layui-input-block">
                            <input type="text" value="{{isset($contentItems)?$contentItems[$headerItem['id']]:''}}" name="{{$headerItem['id']}}" id="date" autocomplete="off" class="layui-input">
                        </div>
                    @break
                    @case('number')
                        <div class="layui-input-block">
                            <input name="{{$headerItem['id']}}" value="{{isset($contentItems)?$contentItems[$headerItem['id']]:''}}" class="layui-input" lay-verify="" type="text" placeholder="请输入" autocomplete="off">
                        </div>
                    @break
                    @case('truefalse')
                    <div class="layui-input-block">
                        <select name="{{$headerItem['id']}}" lay-verify="">
                            <option value=""></option>
                            <option value="true_false" {{isset($contentItems)?($contentItems[$headerItem['id']]=='true_false'?"selected='selected'":''):''}}>是->否</option>
                            <option value="false_true" {{isset($contentItems)?($contentItems[$headerItem['id']]=='false_true'?"selected='selected'":''):''}}>否->是</option>
                            <option value="true_true" {{isset($contentItems)?($contentItems[$headerItem['id']]=='true_true'?"selected='selected'":''):''}}>是->是</option>
                            <option value="false_false" {{isset($contentItems)?($contentItems[$headerItem['id']]=='false_false'?"selected='selected'":''):''}}>否->否</option>
                        </select>
                    </div>
                    @break
                    @case('')
                        <input style="display: none;" value="{{isset($contentItems)?$contentItems[$headerItem['id']]:''}}" name="{{$headerItem['id']}}" class="layui-input" lay-verify="" type="text" placeholder="请输入" autocomplete="off">
                    @break
                @endswitch
            </div>
        @endforeach
        <button lay-filter="submit" lay-submit style="display: none;"></button>
    </form>
</div>