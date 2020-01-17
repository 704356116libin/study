<?php
namespace App\Exports;
use Encore\Admin\Form\Field\Date;
use Illuminate\Http\Response;
use PhpOffice\PhpWord\Element\TrackChange;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class PstReportExportWord
{
    public function exportWord($data,&$files,$user_id)
    {
        $html=$data['text'];
        $html=$this->html();
        $html=str_replace('{{$变量}}','零零零零',$html);
        $html=str_replace('<h3 style="','<p style="font-size:20px;',$html);
        $html=str_replace('<h3>','<p style="font-size:20px;text-align:center;">',$html);
        $html=str_replace('</h3>','</p>',$html);
//        dd($html);

        /**
         * 实例化一个容器
         */
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        /**
         * 设置页眉页脚
         */
        $header = $section->addHeader();//设置页眉
        $header->addText(
            $data['header']
            , ''
            , array('bidi' => true));//页眉添加文本内容
        $footer = $section->addFooter();//设置页脚
        $footer->addText(
            $data['footer']
            , ''
            , array('bidi' => true));//页脚添加文本内容

        //将HTML格式的正文添加到容器中
        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $html, false, false);

        //创建,保存
        $objWriter = IOFactory::createWriter($phpWord);
        $objWriter->save($data['name'] .$user_id. '.docx');
        //循环获取打包的文件名
        $files=array_merge($files,glob($data['name'] .$user_id.'.docx'));
    }
    public function html()
    {
        $html='<h3><strong style="font-size: 20px">官方电话</strong>h3h3h33h3h3</h3><h3 style="text-align:center;"><span style="line-height:1.5">清源水净化有限公司双电源厂区工程</span></h3><h3 style="text-align:center;"><span style="line-height:1.5">审核报告</span></h3><p style="text-align:center;"><span style="color:#f32784"><span style="line-height:1.5">{{文号}}</span></span></p><p><strong><span style="line-height:1.5">长葛市财政局投资评审中心:</span></strong></p><p style="text-indent:2em;"><span style="line-height:1.5">我单位接受贵单位委托，对清源水净化有限公司双电源厂区工程进行了审核，上述工程项目相关资料由贵单位提供，我们的责任是根据《河南省通用安装工程预算定额》(HA02-31-2016)及相关配套文件的规定，按照客观、公正、公平、合理的原则，组织有关专业技术人员对此项工程造价进行审核，并发表审核意见，出具审核报告。在审核过程中，我们根据贵单位提供的资料，专业技术人员会同相关单位及相关人员，认真地分析、认真计算，对工程量的计算、定额的套用、材料分析、工程取费、材料价格的调整等必要的审核程序严格审核，现已审核结束，并将审核结果报告如下：</span></p><p><span style="line-height:1.5"><strong>    一、工程概况：</strong></span></p><p style="text-indent:2em;"><span style="line-height:1.5">    本工程为清源水净化有限公司双电源厂区工程，工程内容含高压开闭所安装、户外高压计量箱、顶管和电缆线路工程等。</span></p><p><span style="line-height:1.5"><strong>    二、审核范围：</strong></span></p><p style="text-indent:2em;"><span style="line-height:1.5">   清源水净化有限公司双电源厂区工程提供施工图及预算内的全部内容。</span></p><p><span style="line-height:1.5"><strong>    三、审核依据：</strong></span></p><p style="text-indent:2em;"><span style="line-height:1.5">1、依据建设单位提供的图纸及预算；</span></p><p style="text-indent:2em;"><span style="line-height:1.5">2、<span style="color:#f32784">{{水利审核依据}}</span>；</span></p><p style="text-indent:2em;"><span style="line-height:1.5">3、《河南省通用安装工程预算定额》(HA02-31-2016)及配套的定额综合解释和现行的有关造价文件</span></p><p style="text-indent:2em;"><span style="line-height:1.5">4、人工费价格执行豫建标定【2018】40号文；</span></p><p style="text-indent:2em;"><span style="line-height:1.5">5、税金根据豫建设标【2018】22号文，按10%计入；</span></p><p style="text-indent:2em;"><span style="line-height:1.5">6、材料价格依据《许昌工程造价信息》2018年第六期，信息价中没有的材料，其价格参考市场价进行调整；</span></p><p style="text-indent:2em;"><span style="line-height:1.5">7、现行的法律法规、标准图集、规范、工艺标准、材料做法等。</span></p><p> <span style="line-height:1.5"><strong>   四、审核原则：</strong></span></p><p style="text-indent:2em;"><span style="line-height:1.5">    客观、公平、公正、实事求是。</span></p><p><span style="line-height:1.5"><strong>    五、审核方法：</strong></span></p><p style="text-indent:2em;"><span style="line-height:1.5">    根据该工程实际情况，我们采取了普查的方法对该工程招标控制价进行了审核。</span></p><p> <span style="line-height:1.5"><strong>六、审核结果：</strong></span></p><p style="text-indent:2em;"><span style="line-height:1.5">    清源水净化有限公司双电源厂区工程审核结果为：原报送审金额<span style="color:#f32784">{{送审金额}}</span>元，审定金额：<span style="color:#f32784">{{审定金额}}</span>元，审减金额  元。</span></p><p> </p><p></p><p><span style="line-height:1.5">编制人 ：                                                 审核人:</span></p><p style="text-align:right;"><span style="line-height:1.5">                       河南英华咨询有限公司</span></p><p style="text-align:right;"> </p><p style="text-align:right;"><span style="line-height:1.5">2019年 2月12日</span></p>';
        return $html;
    }
}
