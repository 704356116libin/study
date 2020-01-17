<?php

namespace App\Http\Controllers\Server;

use App\Events\NotifiyEvent;
use App\Exports\UsersExport;
use App\Http\Resources\CompanyNoticeResource;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\FileResource;
use App\Http\Resources\notice\CompanyNoticeDetailResource;
use App\Http\Resources\pst\PstDetailResource;
use App\Http\Resources\user\UserBaseResource;
use App\Http\Resources\user\UserSimpleResource;
use App\Http\Resources\UserResource;
use App\Models\Approval;
use App\Models\Basic;
use App\Models\Company;
use App\Models\CompanyNotice;
use App\Models\CompanyNoticeColumn;
use App\Models\CompanyPartnerRecord;
use App\Models\Department;
use App\Models\Dynamic;
use App\Models\Notification;
use App\Models\OssFile;
use App\Models\Permission;
use App\Models\Pst;
use App\Models\PstProcessTemplate;
use App\Models\PstTemplate;
use App\Models\PstTemplateType;
use App\Models\Role;
use App\Models\User;
use App\Models\UserOss;
use App\Notifications\Notifiy;
use App\Repositories\BasicRepository;
use App\Repositories\CompanyDepartmentRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\OssFileRepository;
use App\Repositories\PstRepository;
use App\Repositories\UserRepository;
use App\Tools\ApprovalTool;
use App\Tools\CompanyNoticeColumnTool;
use App\Tools\CompanyNoticeTool;
use App\Tools\CompanyOssTool;
use App\Tools\DemoTool;
use App\Tools\DepartmentTool;
use App\Tools\DynamicTool;
use App\Tools\FunctionTool;
use App\Tools\GuzzleHttpTool;
use App\Tools\InstanceTool;
use App\Tools\NotifyTool;
use App\Tools\PstTool;
use App\Tools\RoleAndPerTool;
use App\Tools\RoleTool;
use App\Tools\UserTool;
use App\Tools\WebSocketTool;
use Gregwar\Captcha\CaptchaBuilder;
use GuzzleHttp\Client;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Token;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Swoole\Coroutine\Channel;
use swoole_client;
use swoole_table;


class DemoController extends Controller
{
    use DemoTrait;
    private $http;
    private $functionTool;
    /**
     * DemoController constructor.
     * @param $http
     */
    public function __construct()
{
    $this->http = new Client();
    $this->functionTool=FunctionTool::getFunctionTool();
}
    /**
     * PHP文件操作
     */
    public function file(){
        //以指定模式打开文件
        $file=fopen("../.env",'rb');
        //读取一行数据
        dump(fgets($file));
        fclose($file);
        //直接读取文件
//        readfile('../app/Tools/EmailTool.php');
        //读取文件并按行存到数组中,读取失败则返回false
        $data=file('../.env');
//        dump($data);
        //直接读取文件为字符串
//        dump(file_get_contents('../.env'));

        /**
         * 读取文件内容
         */
        $fopen=fopen('../demo.txt','rb');
        while (!feof($fopen)){
            echo fgets($fopen).'<br/>';
        }
        fclose($fopen);
//        $fopen=fopen('../demo.txt','rb');
//        echo fread($fopen,6);
//        echo '<p>';
//        echo fread($fopen,filesize('../demo.txt'));//输出文件的剩余内容
        /**
         * 文件写入,原始方式
         */
//        $file=fopen('../demo1.txt','wb');
        $str='此情可待成追忆 只是当时已惘然';
//        fwrite($file,$str);
//        fclose($file);
        file_put_contents('../demo2.txt',$str);
        readfile('../demo2.txt');//直接读取文件

        copy('../demo2.txt','../demo3.txt');//文件复制
        unlink('../demo3.txt');//删除文件
        dump(pathinfo('../demo1.txt'));//获取文件的信息
       echo realpath('../demo1.txt');
//        dump( stat('../demo1.txt'));

        /**
         * 目录操作
         */
        $path='D:\/As';
        if(is_dir($path)){
            if($dire=opendir($path))echo $dire;
        }else{
            echo '<br/>路径错误';
        }
        /**
         * 文件锁
         */
        $file_name='../demo1.txt';
        $fd=fopen($file_name,'w');
        flock($fd,LOCK_EX);
        fwrite($fd,'hello');
        flock($fd,LOCK_UN);
        fclose($fd);
        readfile($file_name);
        move_uploaded_file('','../demo');//将上传文件存储到指定位置
    }
    /**
     *
     * @param Request $request
     */
    public function bbbb(Request $request){
        preg_match(config('regex.file_extension'),'asd.fgh.jkl',$matches);//截取文件扩展名
        dd($matches);
        dd('asdffgsdfg,asfasf,2,214,,safr'===true);
        dd(RoleAndPerTool::get_user_c_per(1,1));
        dd( Role::find(3)->getAllPermissions()->pluck('name')->toArray());
        dd(RoleTool::user_has_c_per(1,1,['asdasda',9],'any'));
        dd(DB::table('company_user_role')->where('user_id',1)
            ->where('company_id',1)
            ->pluck('role_id')->toArray());
//        dd(Role::find(1)->hasPermissionTo(['c_super_manage_per','c_oss_upload_per']));

        $company_id=5;
        $roles=User::find(1)->roles->filter(function ($r)use($company_id){
            $c_id=($r->companies)[0]->id;
            return !($c_id===$company_id);
        });
        foreach ($roles as $role){
            dump($role->companies);
        }
        dd(123);
//        echo '通过用户找公司->角色';
//        dd(Permission::select('name')
//            ->whereNotIn('name',['c_super_manage_per','c_normal_manage_per'])
//            ->get()->pluck('name')->toArray());
//        $per_names=BasicRepository::getBasicRepository()->getBasicData(config('basic.c_basic_permissions'))
//            ->load('permissions:name')->permissions->toArray();
//        $names=[];
//        foreach ($per_names as $v){
//            $names[]=$v['name'];
//        }
//        dd($names);

        dd((Company::find(5)->load('roles:id')->roles)->pluck('id')->toArray());//拉取某公司所拥有的角色信息
        dd(Role::find(30)->companies);
        //给基础表相应的记录添加基础角色信息
//        Basic::find(3)->assignRole(Role::whereIn('id',[1,2,3,4,5,6])->get()->pluck('name'));
        $roles=BasicRepository::getBasicRepository()->getBasicData(config('basic.c_basic_roles'))->load('roles')->roles;
        dump($roles[0]->getAttributes());
        dump($roles[0]->permissions);
        //授权超级管理demo
//          $role=Role::find(1);
//          $role->givePermissionTo(Permission::all()->pluck('name')->toArray());
        //部分权限授权demo
        $role=Role::find(6);
        $role->givePermissionTo(Permission::select('name')
            ->whereNotIn('id',[3,4,9,11])
            ->get()->pluck('name')->toArray()
        );
        dd('授权成功');
        //更新关联模型关系
        $role=Role::find(1);
        $role->manage_department()->attach(30);
        $role->save();
        dump(123);
//        Basic::find(2)->givePermissionTo(Permission::all()->pluck('name')->toArray());
//        dump(User::find(1)->load('company')->company[0]->load('roles')->roles);

//        dump(User::find(1)->load('company')->company[1]->load('roles')->roles);
//
//        echo '找某个公司下拥有的职务/角色';
//        dump(Company::find(2)->load('roles'));//找某个公司下拥有的职务/角色
//
//        echo '找某个角色拥有的权限';
//        dump(Role::find(5)->permissions);
//
//        echo '找某个公司下拥有的权限';
//        dump(Company::find(2)->load('permissions'));//找某个公司下拥有的职务/角色
//
//        echo '通过角色找公司';
//        dump(Role::find(5)->load('companies'));//通过角色找公司
//
//        echo '通过用户找角色&通过角色找用户';
//        dump(User::find(1)->roles);
//        dump(Role::find(1)->load('users:id,tel,email'));

//        echo '找到某公司下的用户/并进行筛选';
//        $users=Company::find(1)->users->filter(function ($u){
//            return $u->tel_verified===1;
//        });

//        echo '新建一个权限';
//        dump( Per::insertGetId(['name' => 'company_vip','description'=>'这是一个企业付费权限 ','guard_name'=>'company']));

//        echo '给某个公司下的某个角色分配一个权限';
//        $role=Company::find(2)->roles->filter(function ($role){
//            return $role->name==='总裁';
//        });
//        $role[0]->givePermissionTo(Per::findByName('company_vip','company'));

//        echo '某公司新建一个职务并分配相应的权限';
//        $role1 = Role::find(14);
//        $role2 = Role::find(7);
//        $company=Company::find(2);
////        $company->syncRoles([$role1,$role2]);
//        $company->assignRole($role1);
//
//        $role= Role::find(14);
//        $permissions=Company::find(2)->permissions;
//        $role->syncPermissions($permissions);

//        echo '移除公司某个职务(需开启事务)';
//        $company=Company::find(2);
//        $role=$company->roles->filter(function ($role){
//            return $role->name!='actor';
//        });
////        $company->removeRole($role[0]);
//        Role::find(7)->delete();

//        echo '给某个公司直接分配权限';
//        $company=Company::find(2);
//        dd($company->getDirectPermissions());
//        $company->givePermissionTo(Per::findById(15,'company'));
//        DB::beginTransaction();

//        echo '返回拥有指定role/permission的model集合';
//        dump(Company::role(14)->get());
//        dump(Company::permission('company_vip')->get());//

//        echo '回收公司指定的权限';
//        $company=Company::find(2);
//        DB::beginTransaction();
//        try{
//            $role=$company->roles->filter(function ($role){
//               if($role->hasPermissionTo(14)){
//                   return $role;
//               }
//            });
//            foreach ($role as $r){
//                $r->revokePermissionTo(14);
//            }
//            $company->revokePermissionTo(14);
//            DB::commit();
//            echo '回收崇高';
//        }catch (\Exception $e){
//            echo '回收bu崇高';
//            DB::rollBack();
//        }

//        echo '废弃公司职务';
//        $company=Company::find(1);
//        $roles=$company->load('roles')->roles->filter(function ($role){
//            return $role->id===6&&$role->name==='普通员工';
//        });
//        DB::beginTransaction();
//        try{
//            foreach ($roles as $role){
//                foreach ($role->permissions as $per){
//                    $role->revokePermissionTo($per);
//                }
//                $company->removeRole($role);
//                Role::find($role->id)->delete();
//            }
//        }catch (\Exception $e){
//            DB::rollBack();
//        }




//        echo '判断是否有指定的权限';
//        $company=Company::find(2);
//        dump($company->hasPermissionTo(2));
//        dump($company->hasAnyPermission([1,2,15]));
//        dump($company->hasAllPermissions([1,2,15]));

//        $company=Company::find(2);
//        $role=Role::findById(14,'company');
//        dump($role);
//        dump($company->hasRole(14));

        try{
//            $role = Role::create(['name' => 'send_sms','description'=>'发送短信的权限','guard_name'=>'company']);
//            $permission = Permission::create(['name' => 'fuck fuck','description'=>'fuckPermission ','guard_name'=>'company']);
//            $role->givePermissionTo($permission);
////            $permission->assignRole($role);
            DB::commit();
        }catch(\Exception $e){
            dd($e);
            DB::rollBack();
        }

    }
    /**
     * 排序测试
     */
    public function sortTest(Request $request){
        //以order字段获取数据
        CompanyNoticeColumn::ordered()->get()->pluck('id')->toArray();
        //测试交换排序
        $column1=CompanyNoticeColumn::find(1);
        $column2=CompanyNoticeColumn::find(4);
        CompanyNoticeColumn::swapOrder($column1,$column2);
        dd(CompanyNoticeColumn::ordered()->get()->pluck('id')->toArray());


//        return response()->download('https://dulifei.oss-cn-beijing.aliyuncs.com/dulifei/pdf/shanxi.pdf', '66.pdf');//返回下载
//          $top=CompanyNotice::
//                    where('is_top',1)
//                    ->where('is_show',1)
//                    ->ordered('updated_at','desc')
//                    ->get();
//          $top=DB::table('company_notice')
//                    ->where('is_top',1)
//                    ->where('company_id',1)
//                    ->where('is_show',1)
//                    ->orderBy('updated_at','desc')
//                    ->unionAll(
//                        DB::table('company_notice')
//                            ->where('is_top',0)
//                            ->where('company_id',1)
//                            ->where('is_show',1)
//                            ->orderBy('order')
//                     )
//                    ->get()
        ;
//          dd($top->filter(function ($n){
//              return $n->id>5;
//          }));
//          $now_page=$request->now_page;
//          $page_size=2;
//          $notices=new CompanyNoticeCollection(collect(array_slice($top->toArray(),($now_page-1)*$page_size,$page_size)));
//          return json_encode([
//              'page_count'=>ceil(count($top)/$page_size),
//              'page_size'=>$page_size,
//              'now_page'=>$now_page,
//              'data'=>$notices,
//          ]);

//       return new CompanyNoticeCollection(collect($top));
//          return new CompanyNoticeColleciton(collect($top));

//            dd(array_slice($top->toArray(),3,2),count($top));
//          $top=collect(DB::select('select * from company_notice'));

//          dd($top);
//          dd(CompanyNotice::all());
//          return new Users(User::all());

//        dd($notices[0]->resource->id);
//        echo \GuzzleHttp\json_encode();

//          return new CompanyNoticeCollection(collect(array_slice($top->toArray(),3,2)));

//        $datas=CompanyNotice::where('is_top',1)
//                            ->where('is_show',1)
//                            ->get()
//                            ->union(
//                                CompanyNotice::find(10)
//                            );

//        echo FunctionTool::getFunctionTool()->encrypt_id(25);
//        echo FunctionTool::getFunctionTool()->decrypt_id('H8Gxkt240193');
    }
    /**
     * 无限级遍历
     */
    public function bianli(){
       $user= User::find(1);
       $user->current_company_id=8;
       $user->save();
       dd(123);
//         Redis::hset('aaa','aaaa','1111');
         dd(Redis::hget('aaa','asdasd'));
         dd(111);
         $arr=[1,2,3];
         dd(in_array('3.01',$arr));

//        Department::find(29)->users()->attach(1);//多对多关系添加
//        DepartmentTool::getDepartmentTool()->initCompanyTree(1);
        dd(123);
        // 基础组织结构树
        $basic=[
            'type'=>'root',
            'name'=>'',
            'children'=>[
                [
                    'name'=>'人事部',
                    'type'=>'node',
                    'children'=>[
                        [
                            'name'=>'HR',
                            'type'=>'node',
                            'children'=>[
                            ]
                        ]
                    ]
                ],
                [
                    'name'=>'财务部',
                    'type'=>'node',
                    'children'=>[]
                ],
                [
                    'name'=>'经理',
                    'type'=>'node',
                    'children'=>[]
                ]
            ]
        ];
        dd(json_encode($basic));
        dd(CompanyDepartmentRepository::getDepartmentRepository()->getAllAncestorsNode(29)->toArray());
        dd(CompanyDepartmentRepository::getDepartmentRepository()->getChildrenNode(29));
          $user=User::all();
          UserSimpleResource::$company_id=5;
          return UserSimpleResource::collection($user);
          $goal='人';
          $info=json_decode(Company::find(5)->department_json->info,true)['data'];
          $record=[
              'users'=>[],
              'node'=>[],
          ];//搜索记录数组搜出来的人员信息/非人员信息分开存储
          $index_str=[];//部门索引数组
          $index=[];//部门索引数组
          DepartmentTool::getDepartmentTool()->searchDataInTree($goal,$info,$record,$index_str,$index);
          dd($record);
//        $data=[
//            'users'=>[],
//            'children'=>$data,
//        ];
//        dd($data);
//        $data=DepartmentResource::collection(Department::
//            descendantsAndSelf(29)->toTree());
//        $data=DepartmentResource::collection($data)->toArray(123);
//        return $data ;
//        dd(json_encode(['departments'=>[32,33],'user_ids'=>[3,4]]));
//        dd(Department::fixTree());
//        dd(Department::find(30)->descendants->pluck('id')->toArray());
//        dd(Department::create(['name'=>'测试'])->saveAsRoot());
//        dd(FunctionTool::decrypt_id_array(['FtN9Wa11545','Im55ZV21072']));
//        Department::fixTree();//修正树结构

//        dd($depart->saveAsRoot());
//        if ($depart->save()) {
//             dd($depart->hasMoved());
//        }
        //追加节点
//        $depart=Department::find(7);//找到测试部门根节点
//        $depart->appendNode(Department::create(['name'=>'运维测试'])) ;

//         $node=Department::create(['name'=>'硬件测试']);
//         $node->prependToNode($depart)->save();
//        $depart->prependNode($node);

//        echo '拿到企业1下的顶层部门';
//        $root_department=Department::children;
//        $root_department=Department::where('company_id',1)->where('parent_id',null)->get();
//        dd($root_department[0]->appendNode(Department::create(['name'=>'depth3-3'])));
//        foreach ($root_department as $department){
//
//        }
//        echo '拿到部门下的孩子';
//        $children=$root_department[0]->descendants;
//        $children[2]->appendNode(Department::create(['name'=>'depth2']));

//        echo '指定节点追加父节点';
//        $department=Department::find(13);
//        $department->appendNode(Department::create(['name'=>'depth3']));

//          echo '在某节点的前后插入一个节点(非根节点)';
//          $department=Department::find(15);
//          $department->afterNode(Department::create(['name'=>'fuck','parent_id'=>$department->parent_id]))->save();

//          echo '按序取数据';
//          Department::find(15)->up(2);
//          dd(Department::where('parent_id',13)->defaultOrder()->get()->toArray());

//          $departments=Department::find(1)->descendants()->pluck('id');
//          echo '生成树结构';

//        echo '生成某个深度的树结构';
//        $nodes=Department::select(['name','id','company_id'])
//                        ->withDepth()
//                        ->having('depth','!=',0)
//                        ->with('users')
//                        ->defaultOrder()
//                        ->get()
//                        ->toTree()
//                        ->toArray();
//        return json_encode($nodes);
//        $traverse = function ($categories, $prefix = '-') use (&$traverse) {
//            foreach ($categories as $category) {
//                echo PHP_EOL.$prefix.' '.$category->name;
//
//                $traverse($category->children, $prefix.'-');
//            }
//        };
//        $traverse($nodes);


//        echo '生成某个节点的树结构(携带关系&以及子关系)';
//        $node=Department::withDepth()
//                          ->with('users')
//                          ->find(13);
//        if(!$node->isLeaf()){
//            $nodes=Department::select(['name','id','company_id'])
//                ->withDepth()
//                ->having('depth','=',($node->depth)+1)
//                ->with('users.company:name')
//                ->defaultOrder()
//                ->get()
//                ->toTree()
//                ->toArray();
//
//            foreach ($nodes as $index=>$node){
//                if(!(count($node['users'])==0)){
//                    foreach($node['users'] as $key=>$user){
//
//                      $user[$key]['id']=$this->functionTool->encrypt_id( $user['id']);
//                    }
//                }
//            }
//            return $nodes;
//        }

//        if(!$nodes->isLeaf()){
//
//        }


//          echo '预加载';



//        echo '拿到某节点的深度depth';
//        dump(Department::withDepth()->find(5)->depth);
//        echo '拿到指定深度的节点';
//        dump(Department::withDepth()->having('depth','=',0)->defaultOrder()->get()->toArray());
//        echo '拿到树的最大深度';
//        dump(Department::withDepth()->max('depth')->get());
//        echo '拿到子节点的父节点信息';
//        dump($child[0]->ancestors->toArray());
//        echo '判断某节点是否有孩子/祖先/根节点';
//        dump(count($child[0]->descendants));
//        dump($child[0]->isLeaf());
//        dump(count($child[0]->ancestors));
//        dump($child[0]->isRoot());


    }
    /**
     *redis:缓存测试
     */
    public function redis(Request $request){
        $company=Company::find(1);
        $company->oss;
        $company->oss->fill(['now_size'=>0,'all_size'=>$company->oss->all_size+1]);
        $company->oss->save();
        dd(User::find(1)->departments);
       dd(Hash::check('123456..','$2y$10$MhEwldgDkiHDlN1wtIZKoe/Z20ERnEwOzbudWH6xIzNVgg2ubv4xa')) ;
//         dd($this->get_count(0));
           return UserSimpleResource::collection(User::all());
//        dd(UserTool::getUserTool()->getCompanyIds(1));
        //反射机制测试
//        $instance=new \ReflectionClass(DemoController::class);
//        dump($instance->hasMethod('redis'));//判断某个类是否有某个方法
//        $b='App\Http\Controllers\Server\DemoController';
//        $a=new $b();
//        dd($a);
        //静态类测试
//        $record=Notification::whereIn('type',['c_notice','bbbbb'])
//            ->where('user_id',1)
//            ->orderBy('created_at','desc')
//            ->first();
//        //获取对应的功能记录
//        $model=$record->model;
//        //动态调用资源类,抽取相应的数据
//        $model_type=$record->model_type;
//        $source_name=config('dynamic.class_resource.'.$model_type);
//        //PHP反射获取类
//        $rs=InstanceTool::newInstance($source_name,$model);//动态加载
//        dd(['data'=>$rs->toArray(1)]);
//        return $rs;
//        $rs=new \App\Http\Resources\dynamic\CompanyNotice($record);
//        $source=$instance->newInstance($source_name);
//        dd($source->collection($record));
//        return $rs;
//        dd(Notification::find(3)->model );
//        dd($request->get('hjsafhgashf'));
//       CompanyNoticeColumnTool::getCompanyNoticeColumnTool()->initCompanyColumn(5);
//          dd(json_decode(''));
//        dd(config('notify.notify_way.active'));
//        $data=User::skip(1)->take(3)->get();
//        $data=User::offset(5)->limit(5)->get();
//        dd($data);
//          dd(config('notify.clas s_type.'.CompanyNotice::class));
        dd(Redis::hgetall('u_info'));
        Redis::hset('u_info',1,'66666');
        Redis::hset('u_info',1,'44444');
//          $notice=Notification::find(1);
//          dd(get_class($notice->model));
//          dd(json_encode(NotifyTool::getNotifyTool()->loadUnPushedRecord(1)));
//        UserTool::getUserTool()->zxc();
//        $arr=[
//            'a'=>UserTool::getUserTool(),
//        ];
//        $ref=new \ReflectionClass('App\\Tools');
//        dd(666);
//        $i=0;
//        while($i<1000){
//            event(new NotifiyEvent(new Notifiy(User::find(1),['model_id'=>1,'model_type'=>CompanyNotice::class,
//                'type'=>'notice','message'=>'这是公告内容'])));//添加通知测试
//            $i++;
//        }

//
//        dd(CompanyNotice::find(2)->notify);
//        $id='id';
//        dd(User::find(1)->getAttribute('roles'));

//        dd(UserTool::getUserTool()->getRandomUser());
//        $u=json_decode(Redis::hget('h1','demo'));
//        $u->name=666;
        /* dd(Redis::hexists('h1','demo'));
         dd(Redis::hset('h1','demo',User::find(1)));*/
//        dd(json_decode(Redis::hget('h1','demo'))->id);//获取哈希表中某个键相应的值
//        dd(Redis::hgetall('u_info'));//获取哈希表中所有表元素
//        dd(Redis::hkeys('h2'));//获取哈希表中所有键
    }
    public function strogeTemplet($file,Request $resquest)
    {
        if ($file->isValid()) {//文件是否上传成功
            $extenName=$file->getClientOriginalExtension();//文件扩展名
            $realPath = $file->getRealPath();//文件临时路径
            $filename =   '666.' . $extenName;//存放的文件名
            try{
                Storage::putFile('photos', new File('/path/to/photo'));
                Storage::disk('oss')->put('/demo/'.$filename, file_get_contents($realPath));
                return  '上传成功!';
            }catch (\Exception $e){
                dd($e);
                return  '上传失败请重试!';
            }
        } else {
            return  '上传失败请重试!';
        }
    }
    /**
     * 文件上传测试
     */
    public function oss(Request $request){
        dd(PstTemplateType::find(1)->pstTemplates);
        dd(PstProcessTemplate::find(1)->processType->name);
        dd( PstRepository::getCompanyProcessTemplate(1));
//        dd(json_encode(['概算','预算','工程控制价','其他']));
//        dd(json_encode(['市政工程','园林','市政工程','土木工程']));
        dd(json_encode(['尽情发挥','紧急','十万火急']));
       dd( User::where('id',[1,2])
                ->orWhere('id',[5,6])
                ->get()->toArray());
        $arr=[1,2,3];
        $this->test($arr);
        dd($arr);
        dd(OssFile::find(4)->delete());
        dd(
            User::where('name','not regexp','_')
                ->pluck('name')
                ->toArray()
        );
        dd(FileResource::collection(OssFileRepository::getFilesByDirectory('company/company1/公告附件/'))->toArray(1));
        $file=OssFile::find(1);
        Storage::disk('oss')->copy($file->oss_path,'company/company1/公告附件/345.xlsx');
//        dd(Storage::get('mysql性能优化.docx'));
        dd(123);
        dd(User::find(9)->oss);
        $rootPath=config('oss.user.path').'user'.(5).'/';
        dd($rootPath);
        dd(Storage::makeDirectory('user/user2/')); // Create a directory.);
        dd(is_null($request->khfkhdsjf));
        dd( Storage::deleteDirectory('666/'));
        dd(Storage::makeDirectory('demo/'));
        dd(UserOss::find(1)->user);
        dd(User::find(1)->load('oss'));
        //        return Storage::disk('oss')->download('https://dulifei.oss-cn-beijing.aliyuncs.com/gzt_test/666.png');
//        $file=$request->file('file');//拿到上传的文件
//        $this->strogeTemplet($file,$request);
//        $size=$file->getSize();//拿到上传文件的size,单位为
//        $regex='/(txt|doc|docx|doct|png|jpg|jpeg|pdf|xls|xlsx|zip|svg)$/';//能够上传的文件格式
//        if(preg_match($regex,$file->getClientOriginalExtension())==0){
//            $error='文件格式不合法,只允许 txt,doc,docx,png,jpg,jpeg,pdf,xls格式的文件!';
//            return redirect('/uploadtemplate');
//        }elseif ($size>5*1024*1024){
//            $error='上传文件不能超过5M';
//            return redirect('/uploadtemplate');
//        }
        /**
         * 下载测试
         */
//        $name=Storage::disk('oss')->allFiles('gzt_test')[0];
//        $name=explode('/',$name)[1];
//        header("Content-Type: application/force-download;charset=utf-8;"); //告诉浏览器强制下载
//        header("Content-Transfer-Encoding: binary");
//        header("Content-Disposition: attachment; filename=$name");   //attachment表明不在页面输出打开，直接下载
//        header("Expires: 0");
//        header("Cache-control: private");
//        header("Pragma: no-cache"); //不缓存页面
//        readfile('https://dulifei.oss-cn-beijing.aliyuncs.com/gzt_test/666.png');

        /**
         * 获取指定目录下的文件
         */
        dump(Storage::files('/company'));
        $files=Storage::allFiles('/company/子目录');
        dump($files);
        echo  '判断某文件是否存在';
        dump(Storage::exists('path/to/file/file.jpg'));
        dump(Storage::exists('company/红包.jpg'));
        dump(Storage::getVisibility('company/子目录/红包.jpg'));
        /**
         * 获取文件
         */
        dump(base64_encode(Storage::get('company/子目录/红包.jpg')));
        /**
         * 获取文件信息
         */
        $size=0;
        foreach ($files as $file){
            $size+=Storage::size($file);
        }
        dump(ceil(($size/1024)).'k');
        /**
         * 文件重命名
         */
//        dump( Storage::rename('company/子目录/hongbao.jpg','company/子目录/红包.jpg'));
        /**
         * 新建一个文件夹
         */
        dump(Storage::makeDirectory('company/子目录/子子目录2')); // Create a directory.);
        /**
         *获取子目录信息
         */
        dump(Storage::directories('company/'));

        // 自动为文件名生成唯一的 ID...
//        Storage::putFile('photos', new File('/path/to/photo'));
        echo '移动文件';
        dump(Storage::move('company/子目录/红包.jpg', 'company/子目录/子子目录/红包.jpg'));
//        dump(Storage::deleteDirectory('/')); // Create a directory.);

    }
    /**
     *二维码测试
     * @param Request $request
     */
    public function code(Request $request){
//        $code=base64_encode(QrCode::
//            format('png')
//            ->size(100)
//            ->margin(0)
//            ->generate('www.dulifei.com/notice'));
//         return 'data:image/png;base64,'.$code;
////        return base64_encode($code);
//        return view('test.file', compact( 'code'));
    }
    /**
     * excel测试
     * @param Request $request
     */
    public function excel(Request $request){
        dd(User::find(1)->follow_notices->detch(1));
        $file=OssFile::create(['name'=>'456123','oss_path'=>'hsjdkfhkhg']);
        dd($file);
        dd(CompanyNotice::find(2)->files);
//        $refect=new \ReflectionClass(Company::class);
//        $company=$refect->newInstance(['name'=>'ddddd']);
//        dd($company->save());
        dd(CompanyNoticeColumn::find(1)->cnotice);
        dd(CompanyNoticeColumn::where('company_id',1)
            ->where('name','456')
            ->first()) ;
//        return Excel::download(new UsersExport, 'users.xlsx');
        Excel::import(new DemoImport(), '/demo.xlsx');

        return redirect('/')->with('success', 'All good!');
    }
    /**
     *关键数据加密/解密
     */
    public function secret(Request $request){
        $option=$request->get('option','en');
        if($option=='en'){
            return FunctionTool::encrypt_id($request->id);
        }else{
            return FunctionTool::decrypt_id($request->id);
        }
    }
    /**
     * 头像测试
     */
    public function avator(Request $request){
        $avt=new Avatar();
        echo  $avt->create(iconv("UTF-8","UTF-8",'彬'))->toBase64();
    }
    /**
     * 模型关系测试
     */
    public function relation(){
        dd(User::where('id','>',0 )->get()->pluck('id')->toArray());
        $roles=Company::find(1)->roles;
        foreach ($roles as $role){
            dump($role->hasAllPermissions([4,5,6]));
        }

        dd(swoole_table::TYPE_INT);
        $server=new WebSocketServer();
        $server->run();
        dd(1);
        $c=new Channel();
        dd(Channel::class);
        dd(Websocket::emit('message', 'this is a test'));
//        $serve=new swoole_server('127.0.0.1',9501);
        dd(phpinfo());
        dd(CompanyOssTool::getCompanyOssTool()->makeRootPath(Company::find(1)));
//        DB::table('company_notice_look_record')
//            ->insert(['user_id'=>1,'notice_id'=>1,'time'=>date('Y-m-d H:i:s',time())]);
//        dd(123);
////        dd(CompanyNotice::find(2)->look_users()->save(User::find(1),['time'=>date('Y-m-d H:i:s')]));
////        dd(((CompanyNotice::find(2)->look_users)[0]->company)[0]->pivot);
//        $notice=CompanyNotice::find(FunctionTool::decrypt_id('Sih59u21072'));
//        dd((($notice->look_users)[0]->company)[0]);

        $client = ClientBuilder::create()->build();
//        $params = [
//            'index' => 'a',
//            'type' => 'my_type',
//            'id' => 'my_id'
//        ];
//
//        $response = $client->get($params);
//        print_r($response);

        $params = [
            'index' => 'my_index',
            'body' => [
                'settings' => [
                    'number_of_shards' => 2,
                    'number_of_replicas' => 0
                ]
            ]
        ];

        $response = $client->indices()->create($params);
        print_r($response);

    }
    /**
     * 动态模块数据测试
     */
    public function dynamic(){
        $arr=[1,2,3,4];
        dd(array_key_exists(0,$arr));
        dd(array_values($arr));
        //取出用户的动态列表信息
        dd(json_decode(User::find(1)->dynamic->list_info,true)
            ,json_decode(User::find(1)->dynamic->list_info));
//        dd(json_decode(config('dynamic.chat_list'),true));
//        dd(NotifyTool::getNotifyTool()->getNewNotifyByType(1));
        dd(DynamicTool::getInstance()->makeListInfo());
        //数组头部插入测试
        $array=[
            'a'=>123,
            'b'=>3333,
        ];
//        array_unshift($array,['a1'=>999]);
        $arr=array_merge(['a1'=>5555],$array);
        dd($arr);
        dump(User::find(1)->dynamic);
    }
    /**
     * 递归求鸡蛋算法
     * @param $basic_count
     * @return mixed
     */
    public function get_count($basic_count){
        if($basic_count%1==0&&
            $basic_count%2==1&&
            $basic_count%3==0&&
            $basic_count%4==1&&
            $basic_count%5==4&&
            $basic_count%6==3&&
            $basic_count%7==0&&
            $basic_count%8==1&&
            $basic_count%9==0
        ){
            return $basic_count;
        }else{
            return $this->get_count($basic_count+7);
        }
    }
    public function test(array &$array){
        $array[0]=['a', 'b',];
    }
    /**
     * 评审通功能测试
     */
    public function pst(Request $request){
        dd(FunctionTool::getInitials('梦'));
        $user=User::find(38);
        $avatar_path=$user->oss->root_path.'avatar/';
        $avatar=Storage::disk('oss')->allFiles($avatar_path);
        dd($avatar);
        dd(Pst::where('transfer_duty_data',null)->toSql());

        $a=[1,2,3];
        foreach($a as &$v){}
        foreach($a as $v){}
        dd($a);
        echo  substr(strrchr('asdasdsadasd.affaf.567', '.'), 1);
        echo  pathinfo('asdasfasf.dfg',PATHINFO_EXTENSION);
        dd(PstTool::getNextReportNumber(1));
        dd(json_encode(config('pst_report_rule.rule_data')));
        $records=PstRepository::searchPstByState('all',1,1,1,10,false)['data'];
        dd($records->toArray());
        dd(Pst::select(['id','company_id'])->find(1));
        dd(FunctionTool::encrypt_id_array([1,2,3]));
        dd(PstTool::getPstOperateRecord([
            'pst_id'=>'aaaaaa11545',
            'now_page'=>1,
            'page_size'=>10,
        ]));
        //测试评审通审批回调方法的处理
        $need_data=json_decode(Approval::find(2)->extra_data,true);
        PstTool::approvalCallBack($need_data);
        dd(123);
        dd(json_encode([
            'need_data'=>[
                'pst_id'=>'aaaaaa11545',
                'target_id'=>FunctionTool::encrypt_id(1),//负责人id
            ]
        ]));
        dd(PstRepository::getCanRelationPst(1,1,1));
        $pstTool=PstTool::getPstTool();
        dd($pstTool->makeUserReceiveInfo(1));
        $pst=PstRepository::getPstOperateData(1);
        dd($pst->toArray());
        //测试详情资源文件
        $data=new PstDetailResource(Pst::find(1));
        dd($data->toArray(1));

        dd(json_decode(Pst::select('join_user_data')->find(4)->join_user_data,true));
        dd(json_decode(Pst::find(5)->cc_user_data,true)['cc_users']);

        dd(User::find(3)->exists());
        $approval=Approval::find(2);
        dd(empty(json_decode($approval->extra_data)));
        DynamicTool::getSingleListData(Pst::class.'pst_beside', 1, 'company_id',1,
            '评审通:发起一个评审通需要你的参与', '评审通参与邀请', '0-');
        dd(123);
        $pst=Pst::select('id', 'form_template','form_values','form_values->project_name as project_name',
            'join_user_data->join_user_ids as join_user_ids','cc_user_data->cc_user_ids as cc_user_ids',
            'duty_user_id','publish_user_id','company_id','created_at'
            )
            ->where('id',1)
            ->first();
        dd($pst->toArray());
       //测试获取 指定json的指定值
        dd(PstRepository::getTargetValue(1,'form_values',['c']));
        dd(RoleAndPerTool::get_company_target_per_users(1,['c_pst_receive_per']));
        //测试
        $text = 'John ';
        $text[10] = 'Doe';
        dd($text,strlen($text));
        //测试抽取相关人员的数据
        $pst=Pst::select(
            'join_user_data->join_user_ids as join_user_ids','cc_user_data->cc_user_ids as cc_user_ids',
            'duty_user_id'
        )
            ->where('id',2)
            ->first()
            ;

//        $pst=Pst::select('users_info->allow_users as allow_users')
//            ->where('id',1)
//            ->get();
//        $pst=Pst::select('users_info->allow_users as allow_users','users_info->cc_users as cc_users')
//            ->where('id',1)
//            ->where('users_info','!=','[]')
//            ->first()
//            ->toArray();
        $p=2;
        //测试where多字段联合查询
       $users=User::where([
          ['id','>',0],
          ['id','<',10],
       ])
       ->skip(($p-1)*1)
       ->take(1)
       ->get()
       ->toArray();
        //join_user_data json数据格式
        $join_arr=[
            '1'=>false,
            '5'=>false,
            '7'=>false,
            '8'=>false,
        ];
        return json_encode($join_arr);
    }
    /**
     * Laravel 数据库操作语句测试
     */
    public function dbTest(Request $request){
        dd(Pst::select('join_user_data->join_user_ids->inside_user_ids as inside_user_ids')
            ->where('id',1)
            ->first()->inside_user_ids);
        dd(DB::table('json_test')->whereJsonContains('json->join_user_ids->inside_user_ids',1)->toSql());
        dd(DB::table('pst')
            ->select('id','last_pst_id','template_id','publish_user_id','company_id','outside_user_id','removed',
                'need_approval','removed','form_template','form_values','process_template','approval_method','origin_data',
                'join_user_data','duty_user_id','cc_user_data','allow_user_ids','join_user_data->join_user_ids->inside_receive_state->state_'.(1).' as state')
            ->where('join_user_data->join_user_ids->inside_receive_state->state_'.(1),'待接收')
            ->whereJsonContains('join_user_data->join_user_ids->inside_user_ids',1)
            ->orderBy('created_at','desc')
            ->get()
        );
        dd(Pst::select('join_user_data->join_user_ids->inside_user_ids')
            ->where('id',1)
            ->first());
        echo 'json字段更新';
        Pst::where('id',1)->update(['join_user_data->join_user_ids->inside_receive_state->'.(1)=>true]);
        echo 'json类型的数据操作';
        $records=Pst::select('join_user_data->join_user_ids->inside_receive_state')->where('id','>',1)->get()->toArray();
        dd($records);
        echo '从数据表中获取单个列或行';
        dump(DB::table('users')->where('id',1)->value('name'));
        dump(User::find(1)->value('name'));
        echo '获取某一列的值';
        dump(User::all()->pluck('id'));
        echo '结果分块<br/>';
        User::orderBy('id')->chunk(2,function ($users){
            foreach ($users as $user){

            }

//            return false;用来终止分块的执行
        });
        echo '聚合';
//        dump(User::max('id'));
//        dump(User::avg('id'));
        echo '日期排序';
        dump(User::latest()->first()->toArray());
        echo 'json类型测试';
//        $count=Pst::whereJsonContains('users_info->allow_users',1)
//            ->count();
//        dump($count);
        echo '追加选择的列';
        $query=User::where('id','>',2);
        dump($query->addSelect('count(*)')->get());

    }
    /**
     *
     */
    public function mianshi(){
        //字符串反向输出
        $s='123456789';
        $o='';
        $i=0;
        while (isset($s[$i])&&$s[$i]!=null){
            $o=$s[$i++].$o;
        }
        echo $o.'<br/>';
        //转换驼峰命名
        $str='fang-jiang-hkj';
        $arr1=explode('-',$str);
        $var=[];
        foreach ($arr1 as $v){
            $var[]=ucwords($v);
        }
        echo implode('-',$var);
        //不使用第三个变量交换两个变量的值
        $a=1;
        $b=2;
//        $a.=$b;
//        $b=str_replace($b,'',$a);
//        $a=str_replace($b,'',$a);
        list($b,$a)=array($a,$b);
        echo $a.'-'.$b.'<br/>';
        $arr=[8,12,5,9,3,6];
    }
    public function fu_demo(){

    }
    /**
     * php测试
     */
    public function php(){
        $a=2;
        switch ($a){
            case 1:
            case 2:
                echo 'hello switch';
            break;
            default: echo 'default';
        }
        echo $this->demo();
        echo '函数名为'.__FUNCTION__;
    }
}
