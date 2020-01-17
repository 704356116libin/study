<?php
namespace App\Admin\Controllers;

use App\Models\Transfer;
use App\Http\Controllers\Controller;
use App\Tools\TransferTool;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Admin;

class TransfersController extends Controller
{
    use HasResourceActions;
    protected $transferTool;
    public function __construct()
    {
        $this->transferTool = new TransferTool();
    }

    public function index(Content $content)
    {
        return $content
            ->header('转账记录')
            ->description('description')
            ->body($this->grid());
    }

    public function create(Content $content)
    {
        return $content
            ->header('新增转账')
            ->description('description')
            ->body($this->form());
    }

    public function show(Content $content)
    {
        return $content
            ->header('转账预览')
            ->description('description')
            ->body($this->form());
    }

    public function grid()
    {
        $grid = new Grid(new Transfer);
        $grid->model()->orderBy('created_at','desc');
        $grid->id('ID')->sortable();
        $grid->column('user.name', '转账人');
        $grid->column('company.name', '转账公司');
        $grid->money('金额')->sortable();
        $grid->state('到账状态')->display(function ($value){
           return Transfer::$transferState[$value];
        });
        $grid->created_at('转账时间')->sortable();



        //自定义js
$script = <<<EOT

$('.grid-row-pass').unbind('click').click(function() {
        var id = $(this).data('id');
        $.get('./transfers/review',
            {
                type:'pass',
                id:id,
                _token:LA.token,
            },function (data) {
                if (typeof data === 'object') {

                    if (data.status === 'success') {
                        alert(data.message);
                        $.pjax.reload('#pjax-container'); 
                    } else {
                        alert(data.message);
                    }
                }
            }
        );
    }); 
EOT;
        //引入js
        Admin::script($script);
        //禁用创建按钮
        $grid->disableCreateButton();
        //禁用修改和删除按钮
        $grid->actions(function ($action){
            //禁用删除和编辑按钮
            $action->disableDelete();
            $action->disableEdit();
            $action->disableView();
            //自定义按钮
            $id = $action->getKey();
            $transfer = Transfer::where('id',$id)->first();
            $str = '';
            if($transfer->state == 1){
                $str = "<div class='mb-5'><a class='btn btn-xs action-btn btn-success grid-row-pass' data-id=".$id."><i class='fa fa-check'></i> 通过</a></div>";
            }
            $action->append($str);

        });
        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
        return $grid;
    }

    protected function form()
    {
        $form = new Form(new Transfer);


    }

    /**
     * 转账审核处理
     * @return mixed
     */
    public function review()
    {
        $type = request()->type;
        $id = request()->id;
        switch ($type){
            case 'pass':
//                return $this->transferTool->setTransferTest($id,['state'=>2]);
                return $this->transferTool->setTransfer($id,['state'=>2]);
                break;
            case 'refusal':
                break;

        }
    }
//    public function setTransferState($id)
//    {
//
//    }


}