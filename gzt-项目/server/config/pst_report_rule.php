<?php
/**
 * 评审通报告生成规则配置
 */
return [
    /*
     * 规则类型
     */
    'rule_type'=>[
        'Label'=>'Label',//标签类型的规则
        'Date'=>'Date',//日期类型的规则
        'Input'=>'Input',//输入框类型的规则
        'Increasement'=>'Increasement',//自增类型的规则
    ],
    /*
     * ===============================编号生成规则数据格式===============================================
     */
    'rule_data'=>[
       'rule_data'=> [
            [
               'type'=>'Label',//此段规则的类型
               'value'=>'TZ',//标签值
            ],
            [
                'type'=>'Data',//此段规则的类型
                'value'=>'Ymd',//日期格式化类型,--可为 Y,Y-m,Ymd,Y-m-d 四种格式
            ],
            [
                'type'=>'Label',//此段规则的类型
                'value'=>'BQ',//标签值
            ],
            [
                'type'=>'Increasement',
                'begin_number'=>1,
                'length'=>6,
                'step'=>2,
            ],
           [
               'type'=>'Increasement',
               'begin_number'=>1,
               'length'=>6,
               'step'=>2,
           ]
       ],
        'join_char'=>'-',//连接符
    ]
];