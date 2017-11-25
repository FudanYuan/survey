<?php
/**
 * 问卷--控制器
 * created by Jeremy
 * 2017年11月25日
 */
namespace app\controller;

class Questionnaire extends Common
{

    public $exportCols = [];
    public $colsText = [];

    /**
     * 问卷信息
     * @return mixed
     */
    public function index()
    {
        return view('', []);
    }
    
    /**
     * 获取问卷信息列表
     */
    public function getQuestionnaireList(){
        $params = input('post.');
        $ret = ['error_code' => 0, 'data' => [], 'msg' => ""];
//        $user_id = $this->getUserId();
        $cond = [];
//        $cond['hospital_id'] = ['=', $user_id];
        $list = D('Questionnaire')->getList($cond);
        $page = input('post.current_page',0);
        $per_page = input('post.per_page',0);
        //分页时需要获取记录总数，键值为 total
        $ret['params'] = $params;
        $ret["total"] = count($list);
        //根据传递过来的分页偏移量和分页量截取模拟分页 rows 可以根据前端的 dataField 来设置
        $ret["data"] = array_slice($list, ($page-1)*$per_page, $per_page);
        $ret['current_page'] = $page;
        $this->jsonReturn($ret);
    }

    /**
     * 获取问卷信息
     */
    function info(){
        $id = input('get.id');
        return view('', ['id' => $id]);
    }

    /**
     * 获取问卷详情
     */
    public function getQuestionnaireInfo(){
        $id = input('post.id');
        $ret = ['error_code' => 0, 'msg' => ''];
        $list = D('Questionnaire')->getById($id);
        $user_id = $this->getUserId();
        $user_info = D('UserAdmin')->getById($user_id);
        $doctor_id = $user_info['doctor_id'];
        $hospital_office = D('Doctor')->getById($doctor_id);
        $hospital_office_id = $hospital_office['hospital_office_id'];
        $hospital_office = D('HospitalOffice')->getById($hospital_office_id);
        $hospital_id = $hospital_office['hospital_id'];
        $office_id = $hospital_office['office_id'];
        $hospital_info = D('Hospital')->getById($hospital_id);
        $office_info = D('Office')->getById($office_id);
        $ret['info'] = $list;
        $ret['hospital'] = ['name' => $hospital_info['name']];
        $ret['office'] = ['name' => $office_info['name']];
        $this->jsonReturn($ret);
    }
    /**
     * 新建问卷信息
     */
    public function create(){
        $params = input('post.');
        if(!empty($params)) {
            $ret = ['error_code' => 0, 'msg' => '新建成功'];
            $gender = input('post.gender','');
            $in_hospital_time = input('post.in_hospital_time');
            $params['gender'] = $gender;
            $params['in_hospital_time'] = strtotime($in_hospital_time);
            $ret['params'] = $params;
            $res = D('Questionnaire')->addData($params);
            if(!empty($res['errors'])) {
                $ret['error_code'] = 1;
                $ret['msg'] = '新建失败';
                $ret['errors'] = $res['errors'];
            }
            $this->jsonReturn($ret);
        }
        return view('',[]);
    }

    /**
     * 编辑问卷信息
     * @return \think\response\View
     */
    public function edit(){
        $id = input('get.id');
        $params = input('post.');
        $Questionnaire = D('Questionnaire')->getById($id);
        if(!empty($params)){
            $ret = ['error_code' => 1, 'msg' => '编辑成功'];
            $gender = input('post.gender','');
            $in_hospital_time = input('post.in_hospital_time');
            $params['gender'] = $gender;
            $params['in_hospital_time'] = strtotime($in_hospital_time);
            $Questionnaire_id = $params['Questionnaire_id'];
            unset($params['Questionnaire_id']);
            $ret['params'] = $params;
            $res = D('Questionnaire')->saveData($Questionnaire_id,$params);
            if(!empty($res['errors'])) {
                $ret['error_code'] = 1;
                $ret['msg'] = '编辑失败';
                $ret['errors'] = $res['errors'];
            }
            $this->jsonReturn($ret);
        }else{
            $in_hospital_time = date('Y-m-d H:i:s',$Questionnaire['in_hospital_time']);
            $Questionnaire['in_hospital_time'] = $in_hospital_time;
            mydump($Questionnaire);
            return view('',['Questionnaire' => $Questionnaire]);
        }
    }
    
    /**
     * 删除问卷信息
     */
    public function remove(){
        $ret = ['error_code' =>0, 'msg' => '删除成功'];
        $ids = input('post.ids');
        try{
            $res = D('Questionnaire')->remove(['id' => ['in', $ids]]);
        }catch(MyException $e){
            $ret['error_code'] = 1;
            $ret['msg'] = '删除失败';
        }
        $this->jsonReturn($ret);
    }


    /**
     * 导入问卷星
     */
    public function getPostData(){
        $data = file_get_contents("php://input");//input('post.');
        $now = time();
        file_put_contents('./public/data/questionnaire/'.$now.'.txt', $data);
    }
}