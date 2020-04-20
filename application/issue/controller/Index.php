<?php
namespace app\issue\controller;
header("Content-Type:text/html; charset=utf-8");//防止输出乱码
use think\Controller;
header("Access-Control-Allow-Origin: *"); //解决跨域

class Index extends Controller
{
	// 验证登录状态
	public function log_status(){
		// 验证cookie是否开启，没有则前往登录
		if(cookie('name') == null || cookie('phone') == null)
		{
			return view('login');
		}
	}
	// 登录接口,开启cookie以备答题提交 接口
	public function login(){
		cookie('name',input('name'),18000);
		cookie('phone',input('phone'),18000);
		echo json_encode(array('status'=>(int)1,'msg'=>'登录成功'));exit;
	}
	
	// 答题主页
    public function index()
    {	//验证登录状态
		$this -> log_status();	
        return view();
    }
	
	// 今日答题验证 接口
	public function today_status(){
		// 验证今天是否答题
		$today = db('issue_user') -> where('name',cookie('name')) -> where('phone',cookie('phone')) -> whereTime('date','today') -> find();
		if($today){
			echo json_encode(array('status'=>(int)1,'msg'=>'今日以答题'));exit;
		}else{
			echo json_encode(array('status'=>(int)2,));exit;
		}
	}
	
	// 获取题目 接口
	public function get_issue(){
		// 获取所有题目id
		$result = db('issue_list') -> column('id');
		// 从中随机抽选10个id
		$a = array_rand($result,10);
		// $a为键值,循环获取值
		for($i=0;$i<count($a);$i++){
			$id_arr[]=$result[$a[$i]];
		}
		// 查询抽取题目信息
		$result = db('issue_list') -> where('id','in',$id_arr) -> select();
		// 将选项字符串拆分为数组
		if($result){
			foreach($result as $k=>$v){
				$result[$k]['option'] = explode(',',$result[$k]['option']);
				array_pop($result[$k]['option']);
				foreach($result[$k]['option'] as $key=>$val){
					$result[$k]['option'][$key] = explode('. ',$result[$k]['option'][$key]);
				}
			}
			echo json_encode(array('status'=>(int)1,'issue'=>$result));exit;
		}else{
			echo json_encode(array('status'=>(int)2,'msg'=>'未找到问题'));exit;
		}
	}
	
	//答题结束插入用户答题信息 接口
	public function insert_user_info(){
		$data['name'] = cookie('name');
		$data['phone'] = cookie('phone');
		$data['issue_id'] = input('issue_id');  //题目id
		$data['sorce'] = input('sorce');	//得分
		$data['date'] = time();		//提交时间
		$result = db('issue_user') -> insertGetId($data);	//插入获取自增id得到
		if($result){
			echo json_encode(array('status'=>(int)1,'msg'=>'插入成功','user_info_id'=>$result));exit;
		}else{
			echo json_encode(array('status'=>(int)2,'msg'=>'插入失败'));exit;
		}
	}
	
	// 拆红包 接口
	public function open_pack(){
		// 通过数据库查询还有多少红包
		$result = db('issue_red_pack') -> find();
		$money = $result['residual_amount'];
		$num = $result['residual_num'];
		switch($result['status']){
			case 2 :echo json_encode(array('status'=>(int)2,'msg'=>'红包已发放完毕'));exit;
			case 3 :echo json_encode(array('status'=>(int)3,'msg'=>'活动尚未开始开始'));exit;
			case 4 :echo json_encode(array('status'=>(int)4,'msg'=>'活动已结束'));exit;
			default:
				// 判断红包金额或者数量是否发完了
				if($money<=0||$num<=0){
					echo json_encode(array('status'=>(int)2,'msg'=>'红包已发完'));exit;
				}else{
					$data = $this -> checked($money,$num);
					// 更新用户答题获得红包
					db('issue_user') -> where('id',input('id')) -> update(['red_packet'=>$data['get_money']*100]);
					// 红包金额递减、红包数量递减
					db('issue_red_pack') -> where('id',1) -> setDec('residual_amount',$data['get_money']*100);
					db('issue_red_pack') -> where('id',1) -> setDec('residual_num');
					echo json_encode(array('status'=>(int)1,'get_money'=>$data['get_money'],'data'=>$data));exit;
				}
		}
	}
	
	public function checked($money,$num){
		// 如果只剩一个红包剩余钱都给这个人
		if($num==1){
			$data['get_money'] = $money;
			$data['money'] = $money - $data['get_money'];
		}else{
			// 设定最小值为30分
			$min = 30;
			// 最大值为平均值的2倍,保证红包沿着平均线正态分布
			$max = round(($money/$num)*2,2);
			// 通过随机数获取抽中金额(单位:/分)
			$get_money = mt_rand($min,$max-$min);//避免抢到最后有人没有金额，保留最小值到最后
			// 避免浮点运算,均在100倍下整型运算
			$data['get_money'] =  $get_money/100;
			$data['money'] = floor($money - $get_money)/100;
		}
		$data['num'] = --$num;
		return $data;
	}
	
	// 退出登录
	public function lin_out(){
		cookie('name',null);
		cookie('phone',null);
		return view('login');
	}
	
	// 获取用户答题记录 接口
	public function get_record(){
		$result = db('issue_user') -> where('name',cookie('name')) -> where('phone',cookie('phone')) -> select();
		// 遍历时间转换
		foreach($result as $k=>$v){
			$result[$k]['date'] = date('m/d H:i',$result[$k]['date']);
		}
		echo json_encode(array('status'=>(int)1,'data'=>$result));exit;
	}
}
?>