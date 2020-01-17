<?php

namespace App\Exports;
use Illuminate\Http\Response;
use Knp\Snappy\Pdf;
class ApprovalExportPdf
{
    /**
     * @param $data
     * 将html文件转化pdf下载
     */
    public function ApprovalExportPdf($data)
    {
//        $path=$_SERVER['DOCUMENT_ROOT'].'/../resources/views/aaaa.blade.php';
//        $file=fopen($path,"r");
//        dd($file);

        $name=$data['name'];
        $approval_number=$data['approval_number'];
        $approval_method=$data['approval_method'];
        $sponsor_name=$data['sponsor_data']['user_name'];
        $end_status=$data['end_status'];
        $filename = $data['name'].'-'.$data['sponsor_data']['user_name'].'-'.$data['approval_number'];

        $content=$this->content($data['content']);//提取表单数据
        $process_template="<tr><td>审批内容</td><td>";
        //表单数据拼接
        foreach ($content as $value){
            $process_template=$process_template.$value[0].":".$value[1]."</br>";
        }
        $process_template=$process_template."</td></tr>";

        //审批流程数据拼接
        foreach ($data['process_template'] as $k=>$value){
            $approvers=count($value['data']);//该级审批人数量
            $process_template=$process_template."<tr><td rowspan=$approvers>第".($k+1)."级审批-".$value['approval_type']."</td>";
            foreach ($value['data'] as $userData){
                $process_template=$process_template."<td>审批人:".$userData['user_name']."</br>
                                                    审批状态:".$userData['status']."</br>
                                                    审批完成时间:".$userData['time']."</td></tr><tr>";
            }
        }
        $process_template=$process_template."</tr>";
        //调用插件 ,执行转化pdf操作
        $snappy = new Pdf(base_path('vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64'));
        $html ='<!doctype html>
        <html lang="en">
        <head>
        <meta charset="UTF-8">
                     <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                                 <meta http-equiv="X-UA-Compatible" content="ie=edge">
        </head>
        <body>
          <table border="1" style="width:100%;margin:auto;text-align:center">
          <tr><td colspan="2">审批申请</td></tr>
            <tr><td>发起人:</td><td>'.$sponsor_name.'</td></tr><tr><td>审批编号:</td><td>'.$approval_number.'</td></tr>
            <tr><td>审批名称:</td><td>'.$name.'</td></tr><tr><td>流程类型:</td><td>'.$approval_method.'</td></tr>
            '.$process_template.'
             <tr><td>最终审批结果</td><td>'.$end_status.'</td></tr>
          </table>
        </body>
        </html>';

        return new Response(
            $snappy->getOutputFromHtml($html),
            200,
            array(
                'Content-Type' => 'application/pdf',
                'filename' => urlencode($filename).'.pdf'
            )
        );
    }

    //审批内容
    public function content($data)
    {
        $approval_content=[];
        if($data!=null){
            foreach ($data as $v){
                if(!isset($v['value'])){
                    continue;
                }
                if ($v['type']=='INPUT'){//单行文本
                    $approval_content[]=[$v['field']['label'],$v['value']];
                }elseif ($v['type']=='MONEY'){//金额
                    $approval_content[]=[$v['field']['label'],$v['value']];
                }elseif ($v['type']=='DATEPICKER'){//日期
                    $approval_content[]=[$v['field']['label'],$v['value']];
                }elseif ($v['type']=='DATERANGE'){//日期区间
                    $approval_content[]=[$v['field']['label'],$v['value'][0].'~'.$v['value'][1]];
                }
            }
        }
        return $approval_content;
    }

}
