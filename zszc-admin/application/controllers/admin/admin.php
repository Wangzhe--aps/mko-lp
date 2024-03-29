<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 后台默认控制器
 */
class Admin extends MY_Controller{
	/**
	 * 默认方法
	 */
	public function index(){
		$this->load->view('admin/index.html');
	}


	/**
	 * 默认欢迎
	 */
	public function copy(){
		$this->load->view('admin/copy.html');
	}

	/**
	 * 修改密码
	 */
	public function change(){
		$this->load->view('admin/change_passwd.html');
	}

	/**
	 * 修改动作
	 */
	public function change_passwd(){
		$this->load->model('admin_user_model', 'admin');

		$user_name = $this->session->userdata('user_name');
		$admindata = $this->admin->check_admin_name($user_name);

		// p($admindata);die;
		$passwd = $this->input->post('passwd');
		if(md5($passwd) != $admindata[0]['password']) error('原始密码错误');

		$passwdF = $this->input->post('passwdF');
		$passwdS = $this->input->post('passwdS');

		if($passwdF != $passwdS) error('两次密码不相同');

		
		$user_id = $this->session->userdata('user_id');

		$data = array(
			'password'	=> md5($passwdF)
			);
		$this->admin->update_admin($user_id,$data);

		success('admin/admin/change', '修改成功');
	}














}