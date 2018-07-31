<?php
return [
    'EMPTY_HEADER_ITEM_STRUCT' => '{"delay": 0, "xpath": "", "operateName": "", "operateType": ""}',//空头子项部的结构
    'HEADER_ORDER' => [0=>'operateName', 1=>'operateType', 2=>'xpath', 3=>'delay'],                      //头部字段的顺序
    'OPERATE_TYPE' => [
        '表单操作' => [
            'input'=>'输入框输入',
            'time_selector'=>'时间选择器',
            'dropdown'=>'下拉单',
            'radiogroup'=>'单选框组',
        ],
        '通用操作'=>[
            'button'=>'单击按钮',
            'page'=>'跳页',
        ],
        '结果检测'=>[
            'exist'=>'存在状态',
            'visible'=>'可见性',
            'message'=>'文字匹配',
        ]
    ],
    'OPERATE_TYPE_INPUT_METHOD' => [
        'input' => 'text',
        'message' => 'text',
        'time_selector' => 'time_selector',
        'dropdown' => 'number',//[1,n]
        'radiogroup' => 'number',//[1,n]
        'page' => 'number',//[1,n]
        'button' => '',
        'exist' => 'truefalse',
        'visible' => 'truefalse',
    ],

    'FILE_TYPE_CSV_HEADER' => 0,
    'FILE_TYPE_CSV_DATA' => 1,
];