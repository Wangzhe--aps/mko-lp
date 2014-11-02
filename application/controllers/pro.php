<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pro extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		//Do your magic here
		$this->load->model('pro_info_model','pro_info');
		$this->load->model('user_info_model','user_info');
	}
	/**
	 * 加载发起项目视图
	 */
	public function index()
	{	
		$data['title']='发起项目';
		pro::test();
		die;
		$this->load->view('rise.html',$data,FALSE);
	}
	/**
	 * 同意发起项目
	 */
	public function agree_pro(){
		redirect('pro/s_rise');
	}
	/**
	 *  加载发起项目视图
	 */
	public  function s_rise(){
		$data['title'] = "发起项目";
		$this->load->view('rise.html',$data);
	}
	/**
	 * 发起项目
	 */
	public function p_rise(){
		//上传图片
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['max_size'] = '10000';
		$config['file_name'] = time() . mt_rand(1000,9999).'.jpg';
		
		$this->load->library('upload',$config);
		$status=$this->upload->do_upload('pro_img');
		$wrong=$this->upload->display_errors();
		$info=$this->upload->data();
		//缩略图
		$arr['source_image'] = $info['full_path'];
		$arr['create_thumb'] = FALSE;
		$arr['maintain_ratio'] = TRUE;
		$arr['width'] =368;
		$arr['height'] =256;

		$this->load->library('image_lib',$arr);
		$status=$this->image_lib->resize();

		$user_id=$this->session->userdata('user_id');

		$content =  htmlspecialchars($_POST['editorValue']);
		$data=array(
			'pro_title'=>$this->input->post('pro_title'),
			'pro_goal'=>$this->input->post('pro_goal'),
			'pro_dur'=>$this->input->post('pro_dur'),
			'pro_img'=>$info['file_name'],
			'pro_des'=>$this->input->post('pro_des'),
			'pro_video'=>$this->input->post('pro_video'),
			'user_id'=>$user_id,
			'pro_det'=>$content
			);
		$pro=$this->pro_info->check_pro_name($this->input->post('pro_title'));
		if(!empty($pro)){
			error('该项目已经存在');
		}
		$this->pro_info->add_pro($data);
		$pro_id=$this->db->insert_id();

		$user_info=array(
			'user_phone'=>$this->input->post('user_phone'),
			'user_real_name'=>$this->input->post('user_real_name'),
			'user_bank_name'=>$this->input->post('user_bank_name'),
			'user_subbank_name'=>$this->input->post('user_subbank_name'),
			'user_bank_holder'=>$this->input->post('user_bank_holder'),
			'user_bank_num'=>$this->input->post('user_bank_num')
			);
		$this->user_info->update_user($user_id,$user_info);
		redirect('welcome/thumb/'.$pro_id);
	}

	public function sure_donate(){

		$pro_id=$this->input->post('pro_id');
		$don_money=$this->input->post('don_money');
		$bank_id=$this->input->post('001');
		$agentbillid=substr(md5(rand(0,100000)),0,32);
		
		$project=$this->pro_info->check_pro($pro_id);
		$user=$this->pro_info->pro_user($pro_id);
	

		$pro_title=$project[0]['pro_title'];

		 // if(isset($riser)){
		 // 	$data['user_name']=$user[0]['user_name'];
		 // }else{
			// $data['user_name']='';
		 // }
		$user_name=$this->session->userdata('user_name');
		//获取IP
		$onlineip = "";
		
		if(getenv('HTTP_CLIENT_IP')){
		$onlineip=getenv('HTTP_CLIENT_IP');
		}elseif(getenv('HTTP_X_FORWARDED_FOR')){
		$onlineip=getenv('HTTP_X_FORWARDED_FOR');
		}else{
		$onlineip=getenv('REMOTE_ADDR');
		}
		//数据包中的数据获取
		$version=1;
		$agent_id="1852365";
		$agent_bill_id=$agentbillid;
		$agent_bill_time=date('YmdHis', time());
		$pay_type=0;
		$pay_code=$bank_id;
		$pay_amt=$don_money;

		//汇付宝返回信息跳转页URL
		$notify_url='http://www.allheart.cn/style/'."Notify.php";
		$return_url=site_url('pro/return_url');

		$user_ip=$onlineip;
		$goods_name=$pro_title;
		$goods_num=1;
		$goods_note='none';
		$remark=$pro_id.'/'.$user_name;
		
		$key = "78A19858A9FD41EE8CAFE170";
		//数据签名组成
		$signStr='';
		$signStr  = $signStr . 'version=' . $version;
		$signStr  = $signStr . '&agent_id=' . $agent_id;
		$signStr  = $signStr . '&agent_bill_id=' . $agent_bill_id;
		$signStr  = $signStr . '&agent_bill_time=' . $agent_bill_time;
		$signStr  = $signStr . '&pay_type=' . $pay_type;
		$signStr  = $signStr . '&pay_amt=' . $pay_amt;
		$signStr  = $signStr . '&notify_url=' . $notify_url;
		$signStr  = $signStr . '&return_url=' . $return_url;
		$signStr  = $signStr . '&user_ip=' . $user_ip;

		$signStr = $signStr . '&key=' . $key;
		//获取sign密钥
		$sign='';
		$sign=md5($signStr);

		//将数据发送到汇付宝接口进行处理
		$data['version']=$version;
		$data['agent_id']=$agent_id;
		$data['agent_bill_id']=$agent_bill_id;
		$data['agent_bill_time']=$agent_bill_time;
		$data['pay_type']=$pay_type;

		$data['pay_code']=$pay_code;
		$data['pay_amt']=$pay_amt;
		$data['notify_url']=$notify_url;
		$data['return_url']=$return_url;
		$data['user_ip']=$user_ip;
		$data['goods_name']=$goods_name;
		$data['goods_num']=$goods_num;
		$data['goods_note']=$goods_note;
		$data['is_test']=1;
		$data['remark']=$remark;
		$data['key']=$key;

		$data['sign']=$sign;

		$this->load->view('donate.html',$data);

	}
	public function return_url(){
		$result=$_GET['result'];
		$pay_message=$_GET['pay_message'];
		$agent_id=$_GET['agent_id'];
		$jnet_bill_no=$_GET['jnet_bill_no'];
		$agent_bill_id=$_GET['agent_bill_id'];
		$pay_type=$_GET['pay_type'];
		
		$pay_amt=$_GET['pay_amt'];
		$remark=$_GET['remark'];

		
		$returnSign=$_GET['sign'];
		$key = '78A19858A9FD41EE8CAFE170';
		
		$signStr='';
		$signStr  = $signStr . 'result=' . $result;
		$signStr  = $signStr . '&agent_id=' . $agent_id;
		$signStr  = $signStr . '&jnet_bill_no=' . $jnet_bill_no;
		$signStr  = $signStr . '&agent_bill_id=' . $agent_bill_id;
		$signStr  = $signStr . '&pay_type=' . $pay_type;
		
		$signStr  = $signStr . '&pay_amt=' . $pay_amt;
		$signStr  = $signStr .  '&remark=' . $remark;
		
		$signStr = $signStr . '&key=' . $key;
		
		$sign='';
		$sign=md5($signStr);
		
		$remark2=explode('/',$remark);
		$user_name = $remark2[1];
		$this->load->model('user_info_model','user_info');
		$user=$this->user_info->check_user_name($user_name);
		$user_id=$user[0]['user_id'];
		//请确保 notify.php 和 return.php 判断代码一致
		if($sign==$returnSign){
			$this->load->model('donate_model','donate');
			
			$data=array(
				'don_money'=>$pay_amt,
				'don_time'=>date('Y-m-d H:i',time()),
				'pro_id'=>$remark2[0],
				'user_id'=>$user_id
				);
			$this->donate->add_donate($data);

			success('welcome/thumb/'.$remark,"捐助成功");

		}   //比较MD5签名结果 是否相等 确定交易是否成功  成功显示给客户信息
		else{
			error('捐助失败');
		}
	}
	/**
	 * 项目列表
	 */
	public function donateList()
	{
		$perPage = 8;
		$data['title'] = "捐助项目";
		$this->load->library('pagination');
		$url = site_url().'/pro/donateList';
		$total = count($this->pro_info->check_all());

		$config['base_url'] = $url;   
		$config['total_rows'] = $total;//记录总数，这个没什么好说的了，就是你从数据库取得记录总数   
		$config['per_page'] = $perPage; //每页条数。额，这个也没什么好说的。。自己设定。默认为10好像。   
		$config['page_query_string'] = FALSE;   
		$config['first_link'] = '首页'; // 第一页显示   
		$config['last_link'] = '末页'; // 最后一页显示   
		$config['next_link'] = '下一页'; // 下一页显示   
		$config['prev_link'] = '上一页'; // 上一页显示   
		$config['num_links'] = 2;// 当前连接前后显示页码个数。意思就是说你当前页是第5页，那么你可以看到3、4、5、6、7页。   
		$config['uri_segment'] = 3;   
		$config['use_page_numbers'] = FALSE;  

		$this->pagination->initialize($config);
		$offset = $this->uri->segment(3);
		$this->db->limit($perPage, $offset);

		$data['links'] = $this->pagination->create_links();
		$data['pro']=$this->pro_info->check_all();

		foreach ($data['pro'] as $key) {
			//项目进度
			$data['process'][$key['pro_id']]=$this->pro_info->pro_process($key['pro_id']);
			//捐助人数
			$data['don_num'][$key['pro_id']]=$this->pro_info->don_num($key['pro_id']);
			//总金额
			$data['pro_all'][$key['pro_id']]=$this->pro_info->check_pro_all($key['pro_id']);
		}
		$this->load->view('donateList.html', $data, FALSE);
	}
	public function test()
	{
		echo "string";
	}
}

/* End of file pro.php */
/* Location: ./application/controllers/pro.php */