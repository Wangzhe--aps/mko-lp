<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {
	/**
	 * 首页控制器，用于加载首页视图
	 */
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * 首页
	 */
	public function index()
	{
		$data['title'] = "众善网";
		$this->load->model('pro_info_model','pro_info');
		$this->load->model('href_model','href');
		$data['pro']=$this->pro_info->check_all();

		foreach ($data['pro'] as $key) {
			//项目进度
			$data['process'][$key['pro_id']]=$this->pro_info->pro_process($key['pro_id']);
			//捐助人数
			$data['don_num'][$key['pro_id']]=$this->pro_info->don_num($key['pro_id']);
			//总金额
			$data['pro_all'][$key['pro_id']]=$this->pro_info->check_pro_all($key['pro_id']);
		}
		$data['href']=$this->href->check_href();
		$this->load->view('home.html', $data, FALSE);
	}
		/**
	 * 帮助中心
	 */
	public function help()
	{
		$data['title'] = "了解众善";
		$this->load->view('help.html', $data, FALSE);
	}
	/**
	 * 用户注册页面加载
	 */
	public function s_signup()
	{	
		$data['title'] = "注册";
		$this->load->view('signup.html',$data, FALSE);

	}
	/**
	 * 用户注册
	 */
	public function p_signup()
	{
		//验证注册
		//写入数据库
		//跳转(已登录)
		if(!isset($_SESSION)){
			session_start();
		}

		$this->load->model('user_info_model','user_info');
		
		$user_name=$this->input->post('user_name');
		$user_password=$this->input->post('user_password');
		//$user_passwordag=$this->input->post('passwordag');
		$user_email = $this->input->post('user_email');

		$data=$this->user_info->check_user($user_name);

		if($data){
			error('用户名已经存在');
		}
	
		//获取页面数据，添加到数据库
		$data=array(
			'user_name'=>$user_name,
			'user_password'=>md5($this->input->post('user_password')),
			'user_email' =>$user_email
			);
		$this->user_info->add_user($data);
		//查询数据
		$data_session=$this->user_info->check_user($user_name);
		$user_id=$data_session[0]['user_id'];

		$session_userdata=array(
			'user_id'=>$user_id,
			'user_name'=>$user_name,
			'sign_time'=>time(),
			'user_email'=>$user_email
			);
		$this->session->set_userdata($session_userdata);
		redirect('welcome/index');
	}
	/**
	 * 展示忘记密码页面
	 */
	public function s_forget(){
		$data['title'] = "忘记密码";
		$this->load->view('forget.html',$data, FALSE);	
	}
	/**
	 * 修改密码页面
	 */
	public function p_forget(){

		$user_name=$this->input->post('user_name');
		$user_email=$this->input->post('user_email');
		$this->load->model('user_info_model','user_info');

		//发送邮件
		$config['protocol']="smtp";
    	$config['smtp_host']="smtp.126.com";
   		$config['smtp_user']="zs_email_server@126.com";
    	$config['smtp_pass']="zhongshan2014";
   		$config['crlf']="\n";   
   		$config['newline']="\n";
		$config['smtp_port'] = 25; 
		$config['charset'] = 'utf-8'; 
		$config['wordwrap'] = TRUE;
		$config['mailtype'] = 'html'; 
		$config['validate'] = true; 
		$config['priority'] = 1; 

   		$this->load->library('email'); 
   		$this->email->initialize($config);
		$this->email->from('zs_email_server@126.com','众善科技');
		$this->email->to($user_email);
		$this->email->subject('众善网密码找回');
		$message="<p>" .$user_name. "，你好：</p>
			<p>点击下面的链接修改密码：</p>
			{unwrap}".site_url('welcome/s_change_forget/'.$user_name.'/'.date('Y-m-d',time()))."{/unwrap}
			<p>(如果链接无法点击，请将它拷贝到浏览器的地址栏中。)</p>
			<p>众善科技</p>
			<p>" . date('Y-m-d',time()) . "</p>";
		$this->email->message($message); 

		$this->email->send();
		success('welcome/index','已发送成功');

	}
	public function s_change_forget(){
		$data['title'] = "更改密码";
		$data['user_name']=$this->uri->segment(3);
		$this->load->view('set_newpassword.html', $data, FALSE);	
	}
	public function p_change_forget(){
		$this->load->model('user_info_model','user_info');
		$user_name=$this->input->post('user_name');
		$user_password=$this->input->post('user_password');
		$user_passwordag=$this->input->post('user_passwordag');

		$data_return=$this->user_info->check_user($user_name);

		if(!$data_return){
			error('用户名不存在');
		}
		$data=array(
			'user_password'=>md5($user_passwordag),
			);
		$this->user_info->update_user_password($user_name,$data);
		if(!isset($_SESSION)){
		 	session_start();
		}
		$data_session=array(
		 	'user_name'=>$user_name,
		 	'user_id'=>$data_return[0]['user_id'],
		 	'sign_time'=>time(),
		 	'user_password'=>$user_passwordag
		 	);
		 $this->session->set_userdata($data_session);
		redirect('welcome/index');
	}
	/**
	 * 登录页面视图加载
	 */
	public function s_signin(){
		$data['title'] = "登录";
		$this->load->view('login.html',$data, FALSE);
	}
	/**
	 * 用户登录
	 */
		public function p_signin(){
		$user_name=$this->input->post('user_name');
		$user_password=$this->input->post('user_password');
		$this->load->model('user_info_model','user_info');
		$data=$this->user_info->check_user($user_name);

		if(!$data||$data[0]['user_password']!=md5($user_password)){
			error('用户名或密码错误');
		}

		if(!isset($_SESSION)){
			session_start();
		}
		$session_userdata=array(
			'user_name'=>$user_name,
			'user_id'=>$data[0]['user_id'],
			'signtime'=>time()
			);
		$this->session->set_userdata($session_userdata);
	
		redirect('welcome/index');
	}
	/**
	 * 项目预览
	 */
	public function thumb(){
		$user_name=$this->uri->segment(4);
		
		if($user_name){
			if(!isset($_SESSION)){
			session_start();
		}
		
		$this->load->model('user_info_model','user_info');
		$user=$this->user_info->check_user_name($user_name);
		$user_id=$user[0]['user_id'];

		$session_userdata=array(
			'user_id'=>$user_id,
			'user_name'=>$user_name
			);
		
		$this->session->set_userdata($session_userdata);
		}
		$this->load->model('pro_info_model','pro_info');
		$data['title'] = "项目详情";
		$pro_id=$this->uri->segment(3);
		$data['pro_id']=$pro_id;
		$data['pro']=$this->pro_info->check_pro($pro_id);
		$data['pro_all']=$this->pro_info->check_pro_all($pro_id);
		$data['don_num']=$this->pro_info->don_num($pro_id);
		$data['pro_user']=$this->pro_info->pro_user($pro_id);
		$this->load->view('project.html',$data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
