<?php
/**
 * 评审通相关标识
 */
return [
    'oss_directory' => '评审通附件',
    /*
     * 评审通的状态标识
     */
    'state' => [
        'under_way' => '评审中',
        'wait_receive' => '待接收',
        'wait_appoint' => '待指派',
        'wait_approval' => '待审核',
        'approval_refuse' => '审核未通过',
        'approval_agree' => '审核通过',
        'finish' => '已完成',
        'archived' => '已归档',
        'recalled' => '已召回',
        'cancled' => '已作废',
        'backed' => '被打回',

        'retracted' => '已撤回',

        'refuse_receive' => '拒绝接收',//用来标记参与人已接收
        'received' => '已接收',

    ],
    /*
     * 评审通中需要的审批标识
     */
    'approval_state' => [
        'begin_start' => 'begin_start', //发起评审通-审批标识
        'editor'=>'editor',//编辑评审通-审批标识
        'appoint' => 'appoint', //指派评审通-审批标识
        'transfer_duty' => 'transfer_duty', //转移负责人评审通-审批标识
        'transfer_join' => 'transfer_join', //转移参与人评审通-审批标识
        'deliver' => 'deliver', //递交评审通-审批标识
        'recall' => 'recall', //召回评审通-审批标识
        'cancle' => 'cancle', //作废评审通-审批标识
        'back' => 'back', //打回评审通-审批标识
        'retract' => 'retract', //撤回评审通-审批标识
         'finish'=>'finish',//完成评审通-审批标识
         'archive'=>'archive',//归档评审通-审批标识
    ],
    /*
     *相关按钮的状态标识数组
     */
    'btn_status' => [
        'btn_receive' => false, //接收按钮状态标识
        'btn_refuse_receive' => false, //拒绝接收按钮状态标识
        'btn_editor' => false, //编辑按钮状态标识
        'btn_appoint' => false, //指派按钮状态标识
        'btn_transfer_duty' => false, //移交负责人按钮状态标识
        'btn_transfer_join' => false, //移交参与人按钮状态标识
        'btn_deliver' => false, //递交按钮状态标识
        'btn_recall' => false, //召回按钮状态标识
        'btn_cancle' => false, //作废按钮状态标识
        'btn_back' => false, //打回按钮状态标识
        'btn_retract' => false, //撤回按钮状态标识
        'btn_finish' => false, //完成按钮状态标识
        'btn_archive' => false, //归档按钮状态标识
        'btn_export' => false, //导出报告
        //        'btn_data_alter'=>false,//数据修正按钮状态标识
    ],
    /*
     *评审通人员标识组
     */
    'user_type' => [
        'duty_user' => 'duty_user', //负责人
        'transfer_duty_user' => 'transfer_duty_user', //转移负责人
        'inside_join_user' => 'inside_join_user', //内部参与人
        'transfer_join_user' => 'transfer_join_user', //转移内部参与人

        'pst' => 'pst', //代表评审通自身(源)
        'company_partner' => 'company_partner', //代表评审通本身待接收
        'outside_user' => 'outside_user', //评审通外部联系人标识
    ],
    /*
     * 评审通操作记录标识
     */
    'operate_type' => [
        'create_pst' => '发起评审', //评审通发起创建的操作标识
        'create_approval' => '创建审批', //发起审批操作标识

        'receive' => '接收评审', //接收评审操作标识
        'refuse_receive' => '拒绝接收评审', //接收评审操作标识

        'receive_duty' => '接收负责人', //负责人接收动作标识
        'refuse_duty' => '拒绝接收负责人', //拒绝负责人接收动作标识

        'receive_transfer_duty' => '接收转移负责人', //接收转移负责人动作标识
        'refuse_transfer_duty' => '拒绝接收转移负责人', //拒绝接收转移负责人动作标识

        'receive_inside_join' => '接收参与人', //参与人接收动作标识
        'refuse_inside_join' => '拒绝接收参与人', //参与人接收动作标识

        'receive_transfer_inside_join' => '接收转移参与人', //参与人接收动作标识
        'refuse_transfer_inside_join' => '拒绝接收转移参与人', //参与人接收动作标识

        'appoint' => '指派人员', //指派人员动作标识
        'transfer_duty' => '转移负责人', //转移负责人动作标识
        'transfer_join' => '转移参与人', //转移动作标识
        'deliver' => '递交', //递交动作标识
        'recall' => '召回', //召回动作标识
        'cancle' => '作废', //作废动作标识
        'retract' => '撤回', //撤回动作标识
        'finish' => '完成', //完成动作标识
        'archive' => '归档', //归档动作标识
        'approval_type' => [
            'begin_start' => '发起评审审核', //发起评审通-审批标识
            'agree_begin_start' => '通过发起评审审核', //通过发起评审通-审批标识
            'refuse_begin_start' => '未通过发起评审审核', //拒绝发起评审通-审批标识

            'appoint' => '指派审核', //指派评审通-审批标识
            'agree_appoint' => '通过指派审核', //通过指派评审通-审批标识
            'refuse_appoint' => '未通过指派审核', //未通过指派评审通-审批标识

            'transfer_duty' => '转移负责人审核', //转移负责人评审通-审批标识
            'agree_transfer_duty' => '通过转移负责人审核', //通过转移负责人评审通-审批标识
            'refuse_transfer_duty' => '未通过转移负责人审核', //未通过转移负责人评审通-审批标识

            'transfer_join' => '转移参与人审核', //转移参与人评审通-审批标识
            'agree_transfer_join' => '通过转移参与人审核', //通过转移参与人评审通-审批标识
            'refuse_transfer_join' => '未通过转移参与人审核', //未通过转移参与人评审通-审批标识

            'deliver' => '递交审核', //递交评审通-审批标识
            'agree_deliver' => '通过递交审核', //通过递交评审通-审批标识
            'refuse_deliver' => '未通过递交审核', //未通过递交评审通-审批标识

            'recall' => '召回审核', //召回评审通-审批标识
            'agree_recall' => '召回审核', //通过召回评审通-审批标识
            'refuse_recall' => '召回审核', //未通过召回评审通-审批标识

            'cancle' => '作废审核', //作废评审通-审批标识
            'agree_cancle' => '通过作废审核', //作废评审通-审批标识
            'refuse_cancle' => '未通过作废审核', //作废评审通-审批标识

            'back' => '打回审核', //打回评审通-审批标识
            'agree_back' => '通过打回审核', //通过打回评审通-审批标识
            'refuse_back' => '未通过打回审核', //未通过打回评审通-审批标识

            'retract' => '撤回审核', //撤回评审通-审批标识
            'agree_retract' => '通过撤回审核', //通过撤回评审通-审批标识
            'refuse_retract' => '未通过撤回审核', //未通过撤回评审通-审批标识
        ],
    ],
    /*
     * 分类分页查询时的相关配置
     */
    'search_type' => [
        'my_join' => 'my_join', //我参与的
        'my_duty' => 'my_duty', //我负责的
        'my_publish' => 'my_publish', //我发起的
        'cc_my' => 'cc_my', //抄送我的
        'all' => 'all', //所有与我相关的
    ],
    /*
     * 默认的通知方式
     */
    'default_notification_way' => [
        'need_notify' => 1,
        'need_email' => 0,
        'need_sms' => 0,
    ],
    /*
     * =======================================评审通中相关字段的数据格式(参考)=========================================================
     * join_user_data--参与人信息字段数据格式
     * transfer_join_data--被转移参与人信息字段数据格式,原参与人id与被转移参与人id相对应
     * duty_user_data--负责人json数据
     * transfer_duty_data--转移负责人json数据
     */
    'join_user_data' => [
        'join_user_ids' => [
            'beside_user_id' => [], //外部联系人id数组
            'inside_user_id' => [], //内部参与人id数组
            'inside_receive_state' => [], //内部参与人接收状态数组
            'company_partner_ids' => [], //合作伙伴id数组
        ],
        'join_form_data' => [], //前端传递的表单选择数据
    ],
    'transfer_join_data' => [
        1 => [
            'transfer_user_id' => 4, //被转移人员id,键为1 为发起转移的人员id
            'receive_state' => '待接收'
        ],
        2 => [
            'transfer_user_id' => 6,
            'receive_state' => '待接收'
        ],
    ],
    'duty_user_data' => [
        'duty_user_id' => 1, //负责人id
        'duty_receive_state' => '待接收', //负责人接收状态
        'duty_form_data' => [], //负责人表单选择数据
    ],
    'transfer_duty_data' => [
        'duty_user_id' => 1, //负责人id
        'duty_receive_state' => '待接收', //负责人接收状态
        'duty_form_data' => [], //负责人表单选择数据
    ],
    'join_pst_form_data' => [
        'form_data' => [], //存放参与人表单信息
        'opinion' => '', //存放意见
    ],
    /*
     * =======================================评审通中审批extra_data数据基本格式=========================================================
     */
    'extra_data' => [],
];
