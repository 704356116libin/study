<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ApprovalExport
{
    public $rowNum;

    public function whereLimit($data)
    {
//        $spreadsheet=new Spreadsheet();
//        $spreadsheet->setActiveSheetIndex(0);
//        $Spreadsheet->setActiveSheetIndexByName('sss');
//        $objSheet=$a->getActiveSheet();//获取sheet对象
//        $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true)->setVerticalCentered(false);
//        $objSheet->setCellValue('A1','aaa');//填充单元格
//        $objSheet->mergeCells('D1:F1');
//        $spreadsheet->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);//默认文字水平居中
//        $spreadsheet->getDefaultStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);//默认文字垂直居中
//        $a->getDefaultStyle()->getFont()->setName('Arial');//getDefaultStyle()默认样式
//        $a->getDefaultStyle()->getFont()->setSize(8);

        $spreadsheet = new Spreadsheet();//实例化spreadsheet类,并随之生成一个sheet
        $objSheet = $spreadsheet->getActiveSheet();//获取sheet对象

        //获取数据部分
//        dd($data);
        //设置水平居中,边框属性
        $styleArray = [
            'alignment' => [//设置水平,垂直居中对齐
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];

        //填充数据
        $objSheet->setCellValue('A1','审批申请');
        $objSheet->mergeCells('A1:D1');//合并单元格
        $objSheet->setCellValue('A2','申请人:')->setCellValue('B2',$data['sponsor_data']['user_name'])->setCellValue('C2','申请时间:')->setCellValue('D2',$data['sponsor_data']['time']);
        $objSheet->setCellValue('A3','申请类型:')->setCellValue('B3',$data['sponsor_data']['type'])->setCellValue('C3','流程类型:')->setCellValue('D3',$data['approval_method']);

        $content=$this->content($data['content']);
        $objSheet->setCellValue('A'.(4),'内容摘要');
        foreach ($content as $k=>$v){//摘要内容
            $objSheet->setCellValue('C'.($k+4),$v[0].':')->setCellValue('D'.($k+4),$v[1]);
        }
        $row_index=4+count($content);
        $index=0;
        foreach ($data['process_template'] as $v){
            $row_index=$row_index+3;
            $objSheet->setCellValue('A'.($row_index-2),'第'.$v['approval_level'].'级审批-'.$v['approval_type'])->setCellValue('B'.($row_index-2),$v['approval_type'])->setCellValue('C'.($row_index-2),'审批人:')->setCellValue('D'.($row_index-2),$v['data'][0]['user_name'])
                                                                                                                                                                        ->setCellValue('C'.($row_index-1),'该级审批状态:')->setCellValue('D'.($row_index-1),$v['class_status'])
                                                                                                                                                                        ->setCellValue('C'.$row_index,'审批时间:')->setCellValue('D'.$row_index,$v['level_end_time']);
            $this->rowNum=$row_index;
//            dd($row_index);
            $objSheet->getStyle('A1:D1')->applyFromArray($styleArray);//设置边框属性
            $objSheet->mergeCells('A'.($row_index-2).':B'.($row_index-1));//合并单元格
        }

        $objSheet->setCellValue('A'.($this->rowNum+1),'最终审批结果:')->setCellValue('B'.($this->rowNum+1),$data['end_status']);//最终审批结果
        $objSheet->mergeCells('B'.($row_index+1).':D'.($row_index+1));//合并单元格
        $objSheet->mergeCells('A'.(4).':B'.(count($content)+4));//合并单元格

//        $objSheet->getColumnDimension('D')->setAutoSize(true);//自动计算列宽
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(40);
        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);//默认列宽
//        $objSheet->getRowDimension('1')->setRowHeight(100);//设置行高
        $objSheet->getDefaultRowDimension()->setRowHeight(25);//默认行高
//        $objSheet->getStyle('A1:A'.$this->rowNum)//设置文字颜色
        $objSheet->getStyle('A2:D'.($this->rowNum+1))->applyFromArray($styleArray);//设置边框属性
//            ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $spreadsheet->getActiveSheet()->getStyle('A1:A'.($this->rowNum+1))->getFont()->setBold(true)->setName('Arial')
            ->setSize(12);
        $spreadsheet->getActiveSheet()->getStyle('C1:C'.$this->rowNum)->getFont()->setBold(true)->setName('Arial')
            ->setSize(12);


        //一下是导出sheet操作
        $objWriter = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = $data['name'].'-'.$data['sponsor_data']['user_name'].'-'.$data['approval_number'];
        $this->browserExport('excel07', urlencode($filename));
        $objWriter->save("php://output");

        exit();

    }

    /**
     * 导出excel
     * @param $type
     * @param $fileName
     */
    public function browserExport($type, $fileName)
    {
        if ($type == "excel07") {
            header('Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器将要输出excel07文件
        } else {
            header('Content-Type:application/vnd.ms-excel');//告诉浏览器将要输出excel03文件
        }
        header('Content-Disposition:attachment;filename="' . $fileName  . '.xlsx"');//告诉浏览器将要输出的文件名称
        header('filename:'.$fileName);
        header('Cache-Control:max-age=0');//禁止缓存
    }
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
