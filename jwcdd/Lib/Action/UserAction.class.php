<?php
// User文件的Action类
class UserAction extends Action {

	//所有用户信息展示
    public function user(){
		checkLogin();
		$table=M("Users");
		//取所有用户信息
		$data=$table->select();
		for($i=0;$i<count($data);$i++)
		{
			if ($data[$i]['role']==1)
				$data[$i]['role']= '系统管理员';
			if ($data[$i]['role']==2)
				$data[$i]['role']= '督导';
			if ($data[$i]['role']==3)
				$data[$i]['role']= '学校领导';
			if ($data[$i]['role']==4)
				$data[$i]['role']= '教学院长';
			if ($data[$i]['role']==5)
				$data[$i]['role']= '教师';
		}									
		//发送到页面
		$this->userList=$data;
		$this->display();
    }
	//添加新用户
	public function addUser(){
		checkLogin();
		$user = M("Users");
		$dd = M(Dd);
		$data['teaid'] = $this->_post('teaid');
		$data['name'] = $this->_post('name');
		$data['title'] = $this->_post('title');
		$data['college'] = $this->_post('college');
		$data['idcard'] = $this->_post('idcard');
		$data['phone'] = $this->_post('phone');
		$data['mobi'] = $this->_post('mobi');
		$data['email'] = $this->_post('email');
		$user->data($data)->add();
//<<<<<<< HEAD
//		$data['mobi'] = $this->_post('mobi');
//		$data['mobi'] = $this->_post('mobi');
		//怎么根据新用户的id添加到督导表里？

		$this->redirect("User/user");
	}

    //个人信息查看
    public function user_profile(){
        $users = M('users');
        $userid = session('userId');
        $userid = 1;
        $con['uid'] = $userid;
        $data = $users->where($con)->field('teaid,name,college,title,idcard,phone,mobi,email')->select();
        $this->data = $data[0];
		$this->display();
    }

    //修改个人信息
    public function user_modify(){
        /*$users = M('users');
        if(!empty($_POST) && $this->isPost()){
            $data = array();
            $data['teaid'] = $this->_post('teaid');
            $data['name'] = $this->_post('name');
            $data['title'] = $this->_post('title');
            $data['college'] = $this->_post('college');
            $data['idcard'] = $this->_post('idcard');
            $data['mobi'] = $this->_post('mobi');
            $data['phone'] = $this->_post('phone');
            $data['email'] = $this->_post('email');
            $data['password'] = $this->_post('password');
            $con['uid'] = $this->_post('uid');
            foreach ($data as $key => $value) {
                if (empty($value)) {
                    unset($data[$key]);
                }
            }
            $users->where($con)->save($data);
            $this->saveOperation($con['uid'],'用户修改用户信息');
            $this->redirect('User/user_profile');
        }else{
            $data = array();
            $userid = session('userId');
            $userid = 1;
            $con['uid'] = $userid;
            $data = $users->where($con)->field('uid,teaid,name,college,title,idcard,phone,mobi,email')->select();

            $this->data = $data[0];
            
        }*/
		$this->display();
    }

	//删除用户信息
    public function user_delete($uid=-1){
        checkLogin();
        $deldd = M("Users");
        $con['uid'] = $uid;
        $deldd->where($con)->delete();
		$script = "<script>alert('Success!');location.href='http://localhost/jwcdd/index.php/User/user.html';</script>";
		echo $script;
		$userid = session('userId');
		saveOperation($userid,'删除了Users表中id为'.$uid.'的字段');
       // $this->redirect("User/user");
    }

    //显示要修改用户信息
    public function user_info($uid=-1,$flag){
        $user = M("Users");
        //查询要修改的用户
        $con['uid'] = $uid;
        $euser = $user->field('password', true)->where($con)->select();
        $this->euser = $euser[0]; 
        $this->flag = $flag; 
        //dump($flag);
        $this->display();
    }

    //修改用户信息
    public function editUser($flag=-1){
        $user = M("Users");
        //$userid = session('userid');
        $userid = 1;
        $data['role'] = $this->_post('role');
        $data['teaid'] = $this->_post('teaid');
        $data['name'] = $this->_post('name');
        $data['title'] = $this->_post('title');
        $data['college'] = $this->_post('college');
        $data['idcard'] = $this->_post('idcard');
        $data['mobi'] = $this->_post('mobi');
        $data['phone'] = $this->_post('phone');
        $data['email'] = $this->_post('email');
		
        $con['uid'] = $this->_post('uid');
        if($user->data($data)->where($con)->save()) {
            $this->saveOperation($userid,'用户修改用户信息 [uid='.$con['uid'].']');
            }else{
                $this->error('修改用户信息失败!');
        } 
        if ($flag==0) {
            $this->redirect("User/user");
        }
        elseif ($flag==1) {
            $this->redirect("User/user_dd");
        }
        else $this->redirect("User/user_info");    
    }

    //管理本学期督导
    public function user_dd(){
        checkLogin();
        //查询本学期可用督导信息
        $nowdd = M("Dd");
        //先固定学年学期取值，后面再设成变量传参
        $con1['year'] = 2014;
        $con1['term'] = '秋季';
        $con2['role'] = 2;
        $ddinfo = $nowdd->join('dd_Users on dd_Dd.uid = dd_Users.uid')->field('dd_Users.uid, teaid, name, title, college, idcard, mobi, phone, email, pos, group, did')->where($con1)->where($con2)->order('`group` asc, pos desc')->select();
        $this->ddinfo = $ddinfo;
        //查询本学期还可添加哪些督导
        $users = M("Users");         
        $dduid = i_array_column($ddinfo, 'uid');
        //dump($dduid);
        $con2['uid'] = array('not in', $dduid);
        $dd = $users->field('password,role',true)->where($con2)->order('uid asc')->select();
        $this->dd = $dd;
        //查询已有的组别
        $dgroup = M("Dgroup");
        $dgroup = $dgroup->select();
        $this->dgroup = $dgroup; 
        $this->display();
    }

    //增加督导组别
    public function user_addgroup(){
        checkLogin();
        //$userid = session('userid');
        $userid = 1;
        $addgroup = M("Dgroup");     
        $data['gname'] = $this->_post('gname');
        $nowgroup = $addgroup->field('gname')->select();
        $nowgroup = i_array_column($nowgroup, 'gname');
        //dump($nowgroup);
        if (!empty($data['gname']) && !in_array($data['gname'], $nowgroup)) {
            if($addgroup->data($data)->add()) {
            $this->saveOperation($userid,'用户增加督导组别 ['.$data['gname'].']');
            }else{
                $this->error('增加督导组别失败!');
            }      
        }              
        //页面重定向
        $this->redirect("User/user_dd");
    }

    //选择本学期督导
    public function user_seldd(){
        checkLogin();
        //$userid = session('userid');
        $userid = 1;
        $nowdd = M("Dd");
        $data['year'] = 2014;
        $data['term'] = '秋季';
        $uid = $this->_post('uid');
        $length = count($uid);
        for ($i=0; $i<$length; $i++){
            $data['uid'] = $uid[$i];
            //将选择的督导添加到dd表中
            if($nowdd->data($data)->add()) {
            $this->saveOperation($userid,'用户添加本学期督导 [督导uid'.$data['uid'].']');
            }else{
                $this->error('添加本学期督导失败!');
            } 
        }
        //页面重定向       
        $this->redirect("User/user_dd");
    }

    //修改督导组别
    public function user_mgroup($did=-1, $gname=-1){
        checkLogin();
        //$userid = session('userid');
        $userid = 1;
        $mgroup = M("Dd");
        $con['did'] = $did;
        $data['group'] = $gname;
        if($mgroup->data($data)->where($con)->save()) {
            $this->saveOperation($userid,'用户修改督导组别 [督导did'.$did.']');
        }else{
            $this->error('修改督导组别失败!');
        }    
        $this->redirect("User/user_dd");
    }

    //修改督导职务
    public function user_mpos($did=-1, $pos=-1){
        checkLogin();
        //$userid = session('userid');
        $userid = 1;
        $mpos = M("Dd");
        $con['did'] = $did;
        $data['pos'] = $pos;
        if ($mpos->data($data)->where($con)->save()) {
            $this->saveOperation($userid,'用户修改督导职务 [督导did'.$did.']');
        }else{
            $this->error('修改督导职务失败!');
        }
        $this->redirect("User/user_dd");        
    }

    //删除本学期督导
    public function user_deldd($did=-1){
        checkLogin();
        //$userid = session('userid');
        $userid = 1;
        $deldd = M("Dd");
        $con['did'] = $did;
        $deldd->where($con)->delete();
        $this->redirect("User/user_dd");
    }

    //记录用户操作
    private function saveOperation($uid,$operation){
        $logs = M('logs');
        $data['loguid'] = $uid;
        $data['logtime'] = date('Y-m-d H:i:s');
        $data['logip'] =  get_client_ip();
        $data['operation'] = $operation;
        $logs->data($data)->add();
    }  

	// 定义一个函数getIP()
	private function getIP(){
		$ip = '';
		if (getenv("HTTP_CLIENT_IP"))
			$ip = getenv("HTTP_CLIENT_IP");
		else if(getenv("HTTP_X_FORWARDED_FOR"))
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		else if(getenv("REMOTE_ADDR"))
			$ip = getenv("REMOTE_ADDR");
		else $ip = "Unknow";
		return $ip;
	}
}