<?php
namespace app\admin\controller;
header("Content-Type:text/html; charset=utf-8");//防止输出乱码
use think\Controller;

class Issue extends Controller{
	public function verify($a,$b){
		$Common = controller('Common');//实例化common控制器
		$Common -> lin_in();//验证登录
		$Common -> indexes($a,$b);//传入索引
	}
//	+——————————————————————————
//	|  首页
//	+——————————————————————————

	// 首页显示所有题目
	public function index(){
		//导入索引，验证登录
		$this -> verify(4,0);
		// 根据传递标题进行模糊搜索
		$where=[];
		if(input('tittle')){
			$where['issue'] = array('like','%'.input('tittle').'%');
		}
		$result = db('issue_list') -> where($where) -> order('id esc') -> paginate(10,false,['query'=>request()->param()]);
		$page = $result -> render();  //获取分页
		$this -> assign('intro',$result);
		$this -> assign('page',$page);
		return view();
	}
	
	// 添加题目接口
	public function issue_add(){
		$result = db('issue_list') -> insert(input());
		if($result){
			echo json_encode(array('status'=>1,"msg"=> '添加成功'));exit;
		}else{
			echo json_encode(array('status'=>2,"msg"=> '添加失败'));exit;
		}
	}
	
	// 修改题目信息提交接口
	public function issue_change_commit(){
		// 接受传参
		$data['issue'] = input('issue');
		$data['option'] = input('option');
		$data['true_option'] = input('true_option');
		// 根据id进行更新
		$result = db('issue_list') ->where('id',input('id')) -> update($data);
		if($result){
			echo json_encode(array('status'=>1,"msg"=> '修改成功'));exit;
		}else{
			echo json_encode(array('status'=>2,"msg"=> '修改失败'));exit;
		}
	}
	
	// 题目信息删除接口
	public function issue_del(){
		db('issue_list') -> where('id',input('id')) -> delete();
		echo json_encode(array('status'=>1,"msg"=> '删除成功'));exit;
	}
	
	// 题目信息修改信息获取接口
	public function issue_edit(){
		$result = db('issue_list') -> where('id',input('id')) -> find();
		$result['option'] = explode(',',$result['option']);
		array_pop($result['option']);
		for($i=0;$i<count($result['option']);$i++){
			$result['option'][$i] = explode('. ',$result['option'][$i]);
		}
		echo json_encode($result);exit;
	}
	
//	+——————————————————————————
//	|  用户答题信息页面
//	+——————————————————————————
	
	// 用户答题信息赋值
	public function issue_record(){
		//导入索引，验证登录
		$this -> verify(4,1);
		// 根据传参模糊搜索
		$where=[];
		if(input('name')){
			$where['name'] = array('like','%'.input('name').'%');
		}
		if(input('phone')){
			$where['phone'] = array('like','%'.input('phone').'%');
		}
		$result = db('issue_user') -> where($where) -> order('id esc') -> paginate(10,false,['query'=>request()->param()]);
		$page = $result -> render();  //获取分页
		$this -> assign('intro',$result);
		$this -> assign('page',$page);
		return view();
	}
	
	//用户答题信息删除接口
	public function user_del(){
		db('issue_user') -> where('id',input('id')) -> delete();
		echo json_encode(array('status'=>1,"msg"=> '删除成功'));exit;
	}
	
//	+——————————————————————————
//	|  红包信息页面
//	+——————————————————————————

	// 红包信息赋值
	public function red_pack_set(){
		//导入索引，验证登录
		$this -> verify(4,2);
		$result = db('issue_red_pack') -> where($where) -> order('id esc') ->select();
		$this -> assign('intro',$result);
		return view();
	}
	
	// 红包信息删除
	public function pack_del(){
		db('issue_red_pack') -> where('id',input('id')) -> delete();
		echo json_encode(array('status'=>1,"msg"=> '删除成功'));exit;
	}
	
	// 添加红包接口
	public function red_pack_add(){
		$result = db('issue_red_pack') -> insert(input());
		if($result){
			echo json_encode(array('status'=>1,"msg"=> '添加成功'));exit;
		}else{
			echo json_encode(array('status'=>2,"msg"=> '添加失败'));exit;
		}
	}
	
	// 修改红包获取信息接口
	public function red_pack_edit(){
		$result = db('issue_red_pack') -> where('id',input('id')) -> find();
		echo json_encode($result);exit;
	}
	
	// 红包信息更改提交接口
	public function red_pack_change_commit(){
		// 获取变更值
		$data['name'] = input('name');
		$data['money'] = input('money');
		$data['num'] = input('num');
		$data['residual_amount'] = input('residual_amount');
		$data['residual_num'] = input('residual_num');
		$data['status'] = input('status');
		// 根据id进行更新
		$result = db('issue_red_pack') ->where('id',input('id')) -> update($data);
		if($result){
			echo json_encode(array('status'=>1,"msg"=> '修改成功'));exit;
		}else{
			echo json_encode(array('status'=>2,"msg"=> '修改失败'));exit;
		}
	}
	
	
}