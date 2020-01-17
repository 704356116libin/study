<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;
use Illuminate\Support\Facades\DB;


/**
 * 分页工具类
 */
class PageTool
{
    static private $pageTool;
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
    }

    /**
     * 单例模式
     */
    static public function getPageTool(){
        if(self::$pageTool instanceof PageTool)
        {
            return self::$pageTool;
        }else{
            return self::$pageTool = new PageTool();
        }
    }

    /**
     * 防止被克隆
     */
    private function _clone(){

    }

    /**
     * 分页生成(测试)
     * @param $request
     */
   public function pageTest($request){
       $count=DB::select('select count(id) count from users ')[0]->count;
       $pageSize=10;
       $offet=3;//页面偏移量
       $page=empty($request->page)?1:$request->page;//当前页码
       $allPage=ceil($count/$pageSize);//总页码
       settype($allPage,'int');//转换类型
       $records=DB::select('select id,name,tel,email from users order by id limit  '.($page-1)*$pageSize.','.$pageSize);
       $content='<table border="1px" >
                        <tr>
                        <th>id</th><th>name</th><th>tel</th><th>email</th>
                        </tr>';//表格内容
       foreach ($records as $record){
           $content.='<tr>';
           $content.='<td>'.$record->id.'</td>';
           $content.='<td>'.$record->name.'</td>';
           $content.='<td>'.$record->tel.'</td>';
           $content.='<td>'.$record->email.'</td>';
           $content.='</tr>';
       }
       $content.='</table><br>';
       echo $content;
       $pageContent='';//分页条
       if($page!=1) {
           $pageContent.='<a href=\'/aaaa/?page='.(1).'\'><<首页</a> ';
       }else{
           $pageContent.='<a href=\'#\'><<首页</a> ';
       }//首页处理逻辑
       if($page>1) {
           $pageContent.='<a href=\'/aaaa/?page='.($page-1).'\'>上一页</a> ';
       }else{
           $pageContent.='<a href=\'#\'>上一页</a> ';
       }//上一页处理逻辑
       for ($i=$offet;$i>=1;$i--){
           if($page-$i>=1){
               $pageContent.='<a href=\'/aaaa/?page='.($page-$i).'\'>'.($page-$i).'</a> ';
           }
       }
       $pageContent.='<a href=\'/aaaa/?page='.($page).'\'>'.$page.'</a> ';
       for ($i=1;$i<=$offet;$i++){
           if($page+$i<=$allPage){
               $pageContent.='<a href=\'/aaaa/?page='.($page+$i).'\'>'.($page+$i).'</a> ';
           }
       }
       if($page<$allPage){
           $pageContent.='<a href=\'/aaaa/?page='.($page+1).'\'>下一页</a> ';
       } else{
           $pageContent.='<a href=\'#\'>下一页</a> ';
       }//下一页处理逻辑
       if($page!=$allPage) {
           $pageContent.='<a href=\'/aaaa/?page='.($allPage).'\'>末页>></a>';
       }else{
           $pageContent.='<a href=\'#\'>末页>></a> ';
       }//末页处理逻辑
       $pageContent.='<p >当前第'.$page.'页 共'.$allPage.'页</p>';
       echo $pageContent;
   }
}