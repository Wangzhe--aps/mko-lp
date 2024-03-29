<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Check extends CI_Controller {

	public function pass()
	{
		// 获取PID
		$this->load->helper('form');
		$pro_id=$this->input->post('pro_id');
		$this->load->model('pro_info_model','pro_info');
		$pro=$this->pro_info->check_pro($pro_id);
		$user=$this->pro_info->check_user($pro[0]['user_id']);
		$user_email=$user[0]['user_email'];
		$user_name=$user[0]['user_name'];
		//修改项目状态
		$this->load->model('pro_info_model','pro_info');

		$data=array(
			'pro_status'=>0,
			'pro_start'=>date('Y-m-d:',time())
			);
		$status=$this->pro_info->update_pro($pro_id,$data);
		// 判断是否修改成功
		if($status!=0){
		//根据PID查询项目信息
			$pro=$this->pro_info->check_pro_name($pro_id);
			$pro_title=$pro[0]['pro_title'];
			
			// 发邮件
			//配置邮箱信息
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
			//发送邮件
	   		$this->load->library('email'); 
	   		$this->email->initialize($config);
			$this->email->from('zs_email_server@126.com','众善科技');
			$this->email->to($user_email);
			$this->email->subject('众善科技公司项目审核邮件通知');
			$message="<p>" .$user_name. "，你好：</p>
				<p>您发起的项目： <strong>".$pro_title."</strong></p>
				<p>已经通过众善公司审核，现在项目状态为<strong>正在进行中</strong>。欲查看项目详情，请登录众善网查看：{unwrap}www.allheart.cn{unwrap}</p>
				<p>众善科技</p>
				<p>" . date('Y-m-d',time()) . "</p>";
			$this->email->message($message); 
			$this->email->send();
			success('admin/all_pro/index',"项目审核完毕");
		}else{
			error('状态修改失败');
		}
	}
	public function notpass()
	{
		//获取PID
		$this->load->helper('form');
		$this->load->model('pro_info_model','pro_info');
		$pro_id=$this->input->post('pro_id');
		
		$reason=$this->input->post('reason');
		//修改项目状态
		$data=array(
			'pro_status'=>1
			);
		$status=$this->pro_info->update_pro($pro_id,$data);
		// 判断是否修改成功
		if($status!=0){
			//根据PID查询项目信息
			$pro=$this->pro_info->check_pro_name($pro_id);
			$pro_title=$pro[0]['pro_title'];
			$this->load->model('Riser_info_model','riser_info');
			$riser=$this->riser_info->check_riser_pro_id($pro_id);
			$riser_email=$riser[0]['riser_email'];
			$riser_name=$riser[0]['riser_name'];
			// 发邮件
			//配置邮箱信息
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
			$this->email->to($riser_email);
			$this->email->subject('众善科技公司项目审核邮件通知');
			$message="<p>" .$riser_name. "，你好：</p>
				<p>您发起的项目".$pro_title."</p>
				<p>未通过众善公司审核，理由为：<strong>".$reason."</strong></p><p>欲查看项目详情，请登录众善网查看：{unwrap}www.allheart.cn{unwrap}</p>
				<p>众善科技</p>
				<p>" . date('Y-m-d',time()) . "</p>";
			$this->email->message($message); 
			$this->email->send();

			success('admin/all_pro/index',"项目审核完毕");
		}else{
			error('状态修改失败');
		}
	}
}

/* End of file check.php */
/* Location: ./application/controllers/admin/check.php */
