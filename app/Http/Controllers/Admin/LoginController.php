<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoginModel;
use App\Models\RegisterModel;
use phpqrcode;


class LoginController extends Controller
{
    public function login(){
        return view('/login/login');
    }
    public function login_do(Request $request){
        unset($request->_token);
        $password=md5($request->password);
        $where=['password'=>$password,'name'=>$request->name];
        $res=LoginModel::where($where)->first();
        if ($res) {
            $info = [
                'id' => $res['id'],
                'name' => $res['name'],
            ];
            request()->session()->put('info',$info);
            echo "<script>alert('登陆成功');location='/admin/index'</script>";
        }else{
            echo "<script>alert('账号或密码错误');location='/login/login'</script>";exit;
        }

    }
    public function register(){
        return view('login/register');
    }
    public function register_do(Request $request){
        $data = $request->all();
//        dd($data);
        unset($data['_token']);
        if ($request->password == $request->password1){
            unset($data['password1']);
        }else{
            echo "<script>alert('密码不一致');location='/login/register'</script>";
        }
//        dd($data);
        $data['password']=md5($data['password']);
//        dd($data);
        $res=RegisterModel::insert($data);
        if ($res){
            echo "<script>alert('注册成功');location='/login/login'</script>";
        }else{
            echo "<script>alert('注册失败');location='/login/register'</script>";

        }
    }
    public function wechat(){
            $code = $_GET['code'] ;
            $id = "wx70b42c1d1a4ee5b4";
            $secret = "5c52d652cce1d40cd1a438374def34bf";
            $tokenurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$id&secret=$secret&code=$code&grant_type=authorization_code";
            $res = file_get_contents ( $tokenurl);
            $token = json_decode( $res, true) ['access_token'];
            $openid = json_decode( $res, true) ['openid'];
            $userurl = "https://api.weixin.qq.com/sns/userinfo?access_token=$token&openid=$openid&Lang=zh_CN";
            $userinfo = file_get_contents($userurl);
            $user = json_decode ( $userinfo, true);
            print_r($user);
            echo "<img src=".$user['headimgurl']." /> ";


        $uid = $_GET['uid'];
        $id = "wx70b42c1d1a4ee5b4" ;
        $url = urlencode("http://zhiboba.aulei521.com/login.php");

        header("location:$url");
    }
    public function wechatout(){

        $uid = uniqid();

        $url = "http://lizhimin.lizhijun.fun/oauth.php?uid=".$uid;
        $obj = new \QRcode();

        $png=$obj->png($url,'/1.png');
                return view('login/png');
//        return view('login/png',['png'=>$png]);
    }
}
