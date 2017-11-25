<?php
/**
 * 科室模型
 * Author FeiYu
 * Create 2017.11.5
 */
namespace app\model;
use think\Model;
class Office extends Model{
    protected $table = 'consultation_office';
    protected $pk = 'id';
    protected $fields = array(
        'id', 'name','describe','status','create_time','update_time'
    );
    protected $type = [
        'id' => 'integer',
        'create_time' => 'integer',
        'update_time' => 'integer'
    ];

    /**
     * 获取科室信息列表
     * @param array $cond
     */
    public function getList($cond = []){
        if(!isset($cond['status'])){
            $cond['status'] = ['<>', 2];
        }
        $res = $this->field('id, name,describe,status,create_time')
            ->order('create_time desc')
            ->where($cond)
            ->select();
        return $res;
    }

    /**
     * 获取医院信息根据ID
     * @param $id
     * @return mixed
     */
    public function getById($id){
        $res = $this->field('*')
            ->where(['id' => $id])
            ->find();
        return $res;
    }

    public function getOffice($select,$cond){
        if(!isset($cond['status'])){
            $cond['status'] = ['<>', 2];
        }
        $res = $this->field($select)
            ->where($cond)
            ->select();
        return $res;
    }

    /**
     * 获取医院对应科室
     * @param $select
     * @param $cond
     * @return mixed
     */
    public function getOfficeByHospital($select,$cond){
        $res = $this->alias('a')->field($select)
            ->join('consultation_hospital_office b','b.office_id = a.id')
            ->where($cond)
            ->select();
        return $res;
    }


    /////未修改///
    /**
     * 更新科室信息
     * @param $id
     * @param $data
     * @return array
     */
    public function saveData($id, $data){
        $ret = [];
        $errors = $this->filterField($data);
        $ret['errors'] = $errors;
        if(empty($errors)){
            $data['update_time'] = time();
            $this->save($data, ['id' => $id]);
        }
        return $ret;
    }

    /**
     * 添加科室信息
     * @param $data
     * @return array
     */
    public function addData($data){
        $ret = [];
        $errors = $this->filterField($data);
        $ret['errors'] = $errors;
        if(empty($errors)){
            $this->save($data);
        }
        return $ret;
    }

    /**
     * 批量增加科室信息
     * @param $dataSet
     * @return array
     */
    public function addAllData($dataSet){
        $ret = [];
        foreach ($dataSet as $data) {
            $errors = $this->filterField($data);
            $ret['errors'] = $errors;
            if(!empty($errors)){
                return $ret;
            }
        }
        $ret['result'] = $this->saveAll($dataSet);
        return $ret;
    }

    /**
     * 删除科室信息
     * @param array $cond
     * @return false|int
     * @throws MyException
     */
    public function remove($cond = []){
        $res = $this->save(['status' => 2], $cond);
        if($res === false) throw new MyException('2', '删除失败');
        return $res;
    }

    /**
     * 标记为已读
     * @param array $cond
     * @return false|int
     * @throws MyException
     */
    public function markRead($cond = []){
        $res = $this->save(['status' => 1], $cond);
        if($res === false) throw new MyException('2', '标记失败');
        return $res;
    }

    /**
     * 过滤必要字段
     * @param $data
     * @return array
     */
    private function filterField($data){
        $ret = [];
        $errors = [];
        if(isset($data['source_user_id']) && !$data['source_user_id']){
            $errors['source_user_id'] = '发送用户不能为空';
        }
        if(isset($data['target_user_id']) && !$data['target_user_id']){
            $errors['target_user_id'] = '接收用户不能为空';
        }
        if(isset($data['title']) && !$data['title']){
            $errors['title'] = '标题不能为空';
        }
        if(isset($data['content']) && !$data['content']){
            $errors['content'] = '内容不能为空';
        }
        if(isset($data['operation']) && !$data['operation']){
            $errors['operation'] = '操作不能为空';
        }
        if(isset($data['priority']) && !$data['priority']){
            $errors['priority'] = '优先级不能为空';
        }
        return $errors;
    }
}
?>