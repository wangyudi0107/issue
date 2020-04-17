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
	
	public function index(){
		//导入索引，验证登录
		$this -> verify(4,0);
		$where=[];
		if(input('tittle')){
			$where['issue'] = array('like','%'.input('tittle').'%');
		}
		
		$result = db('issue_list') -> where($where) -> paginate(10,false,['query'=>request()->param()]);
		$page = $result -> render();  //获取分页
		$this -> assign('intro',$result);
		$this -> assign('page',$page);
		return view();
	}
	
	public function issue_add(){
		$result = db('issue_list') -> insert(input());
		if($result){
			echo json_encode(array('status'=>1,"msg"=> '添加成功'));exit;
		}else{
			echo json_encode(array('status'=>2,"msg"=> '添加失败'));exit;
		}
	}
	
	//问题信息删除
	public function issue_del(){
		db('issue_list') -> where('id',input('id')) -> delete();
		echo json_encode(array('status'=>1,"msg"=> '删除成功'));exit;
	}
	
	public function issue_record(){
		//导入索引，验证登录
		$this -> verify(4,1);
		$where=[];
		if(input('name')){
			$where['name'] = array('like','%'.input('tittle').'%');
		}
		if(input('phone')){
			$where['phone'] = array('like','%'.input('tittle').'%');
		}
		
		$result = db('issue_user') -> where($where) -> paginate(10,false,['query'=>request()->param()]);
		$page = $result -> render();  //获取分页
		$this -> assign('intro',$result);
		$this -> assign('page',$page);
		return view();
	}
	
	//问题信息删除
	public function user_del(){
		db('issue_user') -> where('id',input('id')) -> delete();
		echo json_encode(array('status'=>1,"msg"=> '删除成功'));exit;
	}
	
	public function red_pack_set(){
		//导入索引，验证登录
		$this -> verify(4,2);
		$result = db('issue_red_pack') -> where($where) ->select();
		$this -> assign('intro',$result);
		
		return view();
	}
	
	//红包信息删除
	public function pack_del(){
		db('issue_red_pack') -> where('id',input('id')) -> delete();
		echo json_encode(array('status'=>1,"msg"=> '删除成功'));exit;
	}
}