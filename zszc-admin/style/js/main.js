  function login_check(){
    if(document.getElementById("user_name").value == ""){
    alert('用户名不能为空！');
    return false;
    }
	else if(document.getElementById("user_pwd").value == ""){
    alert('密码不能为空！');
    return false;
    }
    else return true;
}
  function register_check(){
    if(document.getElementById("user_name").value == ""){
    alert('用户名不能为空！');
    return false;
    }
	else if(document.getElementById("user_email").value == ""){
    alert('邮箱不能为空！');
    return false;
    }
	else if(document.getElementById("user_pwd").value == ""){
    alert('密码不能为空！');
    return false;
    }
	else if(document.getElementById("user_pwd").value != document.getElementById("pwd_repeat").value){
    alert('两次密码不一致！');
    return false;
    }
	else if(document.getElementById("realname").value == ""){
    alert('真实姓名不能为空！');
    return false;
    }
    else return true;
}
function item_introduce(){
    document.getElementById('item_introduce').style.display = 'block';
    document.getElementById('team').style.display = 'none';
    document.getElementById('item_show').style.display = 'none';
    document.getElementById('donation_detail').style.display = 'none';
}
function team(){
    document.getElementById('item_introduce').style.display = 'none';
    document.getElementById('team').style.display = 'block';
    document.getElementById('item_show').style.display = "none";
    document.getElementById('donation_detail').style.display = "none";
}
function item_show(){
    document.getElementById('item_introduce').style.display = 'none';
    document.getElementById('team').style.display = 'none';
    document.getElementById('item_show').style.display = "block";
    document.getElementById('donation_detail').style.display = "none";
}
function donation_detail(){
    document.getElementById('item_introduce').style.display = 'none';
    document.getElementById('team').style.display = 'none';
    document.getElementById('item_show').style.display = "none";
    document.getElementById('donation_detail').style.display = "block";
}