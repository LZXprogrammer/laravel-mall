<?php

namespace App\Http\Controllers\Api;

use App\Models\Consumer;
use App\Models\ConsumerAccount;
use App\Models\LoginLog;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /**
     * 登陆接口
     *
     * @param   mobile      string      用户手机号
     * @param   password    string      用户密码
     * @return array()
     */
    public function index(Request $request)
    {
        //获取参数
        $mobile = $request->post('mobile');
        $password = $request->post('password');

        $ui = $this->userExist($mobile);
        //验证登陆用户是否存在
        if(empty($ui)) {
            returnJsonMsg('0', '登陆用户不存在', '');
        }
        //验证登陆密码是否正确
        $verify_password = md5(md5($password).$ui->rand);
        if($verify_password !== $ui->password) {
            returnJsonMsg('0', '登陆密码不正确', '');
        }

        //更新用户登陆信息
        session()->put('uid', $ui->id);
        session()->save();
        $user = Consumer::where('mobile', $mobile)->update(['last_login_time' => time()]);

        //增加用户登陆日志
        $log = new LoginLog();
        $log->c_id = $ui->id;
        $log->login_time = time();
        $log->login_ip = $request->getClientIp();
        $bool = $log->save();
        if(!$user || !$bool) {
            returnJsonMsg('0', '登陆失败', '');
        }
        returnJsonMsg('1', '登陆成功', '');
    }

    /**
     * 注册接口
     *
     * @param   mobile      string      用户手机号
     * @param   password    string      用户密码
     * @param   code        string      用户验证码
     * @param   code        string      用户验证码
     * @return  array()
     */
    public function register(Request $request)
    {
        //获取参数
        $mobile = $request->post('mobile');
        $password = $request->post('password');
        $code = $request->post('code');
        $promote = $request->post('promote');

        //进行逻辑判断
        $ui = $this->userExist($mobile);
        //验证登陆用户是否存在
        if(!empty($ui)) {
            returnJsonMsg('0', '登陆用户已存在', '');
        }
        //判断用户短信
        $sms = Message::where(['mobile' => $mobile, 'code' => $code, 'is_use' => '0'])->first();
        if(empty($sms)) {
            returnJsonMsg('0', '用户输入短信验证码不存在或不正确', '');
        }
        if($sms->overdue_time < time()) {
            returnJsonMsg('0', '用户短信验证码已过期', '');
        }

        $level_a = 0;
        $level_b = 0;
        $level_c = 0;
        //查询父级用户
        if($promote) {
            $p_user = Consumer::where('promote', $promote)->first();
            if($p_user) {
                $level_c = $p_user->id;
                $level_b = $p_user->level_c;
                $level_a = $p_user->level_b;
            }
        }

        //开启事务
        DB::beginTransaction();
        //生成随机数
        $rand = randFloat(6);
        //注册用户信息
        $u_inset = [
            'mobile'=>$mobile,
            'password'=>md5(md5($password).$rand),
            'rand'=>$rand,
            'level_a'=>$level_a,
            'level_b'=>$level_b,
            'level_c'=>$level_c,
            'create_time'=>time(),
            'promote'=>getPromote(8),
            'last_login_time'=>time()
        ];
        $consumer = Consumer::create($u_inset);
        //获取插入用户ID
        $c_id = $consumer->id;

        //用户银行卡
        $u_account = [
            'c_id' => $c_id,
            'total' => '0.00',
            'available' => '0.00',
            'freeze' => '0.00',
            'withdraw' => '0.00',
            'market' => '0.00',
            'market_a' => '0.00',
            'market_b' => '0.00',
            'market_c' => '0.00'
        ];
        $account = ConsumerAccount::create($u_account);

        //更改信息状态
        $u_sms = Message::where(['mobile' => $mobile, 'code' => $code])->update(['is_use' => '1']);

        if(!$consumer || !$account || !$u_sms) {
            //回滚数据
            DB::rollBack();
            returnJsonMsg('0', '注册失败', '');
        }

        $info['uid'] = $c_id;
        //提交数据
        DB::commit();
        returnJsonMsg('1', '注册成功', $info);
    }

    /**
     * 忘记密码
     *
     * @param   mobile              string      用户手机号
     * @param   code                string      短信验证码
     * @param   newPassword         string      新密码
     * @return  array()
     */
    public function forgotPassword(Request $request)
    {
        //获取参数
        $mobile = $request->post('mobile');
        $password = $request->post('new_pwd');
        $code = $request->post('code');

        $ui = $this->userExist($mobile);
        //验证登陆用户是否存在
        if(empty($ui)) {
            returnJsonMsg('0', '用户不存在', '');
        }
        //判断用户短信
        $sms = Message::where(['mobile' => $mobile, 'code' => $code, 'is_use' => '0'])->first();
        if(empty($sms)) {
            returnJsonMsg('0', '用户输入短信验证码不存在或不正确', '');
        }
        if($sms->overdue_time < time()) {
            returnJsonMsg('0', '用户短信验证码已过期', '');
        }

        //开启事务
        DB::beginTransaction();
        //生成新密码
        $new_password = md5(md5($password).$ui->rand);
        //生成随机数
        $user = Consumer::where('mobile', $mobile)->update(['password' => $new_password]);

        //更改信息状态
        $u_sms = Message::where(['mobile' => $mobile, 'code' => $code])->update(['is_use' => '1']);

        if(!$user || !$u_sms) {
            //回滚数据
            DB::rollBack();
            returnJsonMsg('0', '用户修改密码失败', '');
        }
        //提交数据
        DB::commit();
        returnJsonMsg('1', '用户修改密码成功', '');
    }


    /**
     * 修改密码
     *
     * @param  old_pwd      string      旧密码
     * @param  new_pwd      string      新密码
     * @param  repeat_pwd      string      确认密码
     * @param  mobile      string      用户手机号
     * @param  code      string      短信验证码
     * @return array()
     */
    public function updatePassword(Request $request)
    {
        //获取参数
        $old_pwd = $request->post('old_pwd');
        $new_pwd = $request->post('new_pwd');
        $mobile = $request->post('mobile');
        $code = $request->post('code');

        $ui = $this->userExist($mobile);
        //验证登陆用户是否存在
        if(empty($ui)) {
            returnJsonMsg('0', '用户不存在', '');
        }
        //判断用户短信
        $sms = Message::where(['mobile' => $mobile, 'code' => $code, 'is_use' => '0'])->first();
        if(empty($sms)) {
            returnJsonMsg('0', '用户输入短信验证码不存在或不正确', '');
        }
        if($sms->overdue_time < time()) {
            returnJsonMsg('0', '用户短信验证码已过期', '');
        }
        //判断旧密码是否正确
        if($ui->password != md5(md5($old_pwd).$ui->rand)) {
            returnJsonMsg('0', '用户输入旧密码不正确', '');
        }


        //开启事务
        DB::beginTransaction();
        //生成新密码
        $new_password = md5(md5($new_pwd).$ui->rand);
        //生成随机数
        $user = Consumer::where('mobile', $mobile)->update(['password' => $new_password]);

        if(!$user) {
            //回滚数据
            DB::rollBack();
            returnJsonMsg('0', '用户修改密码失败', '');
        }
        //提交数据
        DB::commit();
        returnJsonMsg('1', '用户修改密码成功', '');
    }

    //检查用户是否存在
    public function userExist($mobile) {
        return Consumer::where('mobile', $mobile)->first();
    }
}
