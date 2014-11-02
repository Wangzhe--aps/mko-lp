<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	//修改密码
	public function p_change(){
		$this->load->model('user_info_model','user_info');
		$user_password=$this->input->post('oldpass');
		$user_passwordag=$this->input->post('newpass');

		$user_id=$this->session->userdata('user_id');
		$data_return=$this->user_info->check_user_id($user_id);
		$data=$this->user_info->check_user_id($user_id);	
		$data=array(
			'user_password'=>md5($user_passwordag),
			);
		$this->user_info->update_user($user_id,$data);

		if(!isset($_SESSION)){
			session_start();
		}
		$data_session=array(
			'user_password'=>$data[0]['user_password'],
			);
		$this->session->set_userdata($data_session);
		redirect('index/user/s_my');
	} 

	public function s_my(){

		$data['title'] = "用户中心";
		$this->load->model('user_info_model','user_info');
		$this->load->model('pro_info_model','pro_info');
		$this->load->model('donate_model','donate');

		$uid=$this->session->userdata('user_id');
		//累计捐助的项目个数
		$data['don_num']=$this->donate->check_don_num($uid);
		 //累计发起的项目个数
		$data['pro_num']=$this->user_info->my_pro($uid);
		//累计捐助总金额
		$data['don_all']=$this->user_info->check_don_all($uid);
		 //累计发起的项目
		$data['my_pro']=$this->user_info->check_my($uid);
		$my_pro=$data['my_pro'];
		 //累计捐助的项目
		$data['my_don']=$this->donate->check_my_don($uid);
		$my_don=$data['my_don'];
		 /**
		  * 项目进度
		  */
		foreach ($my_pro as $key) {
			$data['process'][$key['pro_id']]=$this->pro_info->pro_process($key['pro_id']);
		}

		$this->load->view('my.html',$data,FALSE);
	}
	public function quit(){
		$data['title'] = "登录";
		$this->session->sess_destroy();
		$this->load->view('login.html',$data,FALSE);
	}


}

/* End of file user.php */
/* Location: ./application/controllers/user.php */