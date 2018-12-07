<?php

namespace App\Http\Controllers\Api;

use App\Models\Consumer;
use App\Models\HarvestAddress;
use App\Models\ConsumerBank;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\realNameAuthNotification AS realNameAuth;
use App\Notifications\BankVerificationNotification AS bankAuth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $_realNameAuth;
    protected $_bankAuth;

    public function __construct(realNameAuth $realNameAuth, bankAuth $bankAuth){
        $this->_realNameAuth = $realNameAuth;
        $this->_bankAuth = $bankAuth;
    }
    /**
     * 个人中心主页
     *
     * @return array|\Illuminate\Http\Response
     */
    public function index()
    {
        $user = Consumer::where('id', session('uid'))
            ->select(['mobile', 'avatar', 'nick_name', 'real_name', 'id_number', 'is_active', 'create_time', 'real_time', 'active_time', 'promote'])
            ->first();
        //拼装参数
        $info['mobiles'] = $user->mobile;
        $info['avatars'] = $user->avatar;
        $info['nick_name'] = $user->nick_name;
        $info['real_name'] = $user->real_name;
        $info['id_numbers'] = $user->id_number;
        $info['is_actives'] = $user->is_active;
        $info['create_times'] = $user->create_time;
        $info['real_times'] = $user->real_time;
        $info['active_times'] = $user->active_time;
        $info['promotes'] = $user->promote;

        return ['code' => 1, 'message' => '请求成功', 'data' => $info];
    }

    /**
     * 收获地址列表
     *
     * @return array()
     */
    public function addressList()
    {
        $info = HarvestAddress::where('c_id', session('uid'))->where('is_del', '1')->with(['province', 'city', 'area'])->get();

        $list = array();
        //拼装参数
        if(!empty($info)) {
            foreach ($info as $k => $v) {
                $res['id'] = $v['id'];
                $res['name'] = $v['name'];
                $res['phone'] = $v['phone'];
                $res['province_id'] = $v['province_id'];
                $res['city_id'] = $v['city_id'];
                $res['area_id'] = $v['area_id'];
                $res['address'] = $v['province']['name'].' '.$v['province']['name'].' '.$v['province']['name'].' '.$v['address'];
                //$res['is_default'] = $v['is_default'];
                $list[] = $res;
            }
        }

        return ['code' => 1, 'message' => '请求成功', 'data' => $list];
    }

    /**
     * 新增/编辑收获地址
     *
     * @return array()
     */
    public function editAddress(Request $request) {
        $id = $request->post('id');
        $province_id = $request->post('province_id');
        $city_id = $request->post('city_id');
        $area_id = $request->post('area_id');
        $address = $request->post('address');
        $name = $request->post('name');
        $phone = $request->post('phone');
        $is_default = $request->post('is_default');

        if($is_default == '1') {
            $update = HarvestAddress::where('is_default', '1')->where('c_id', session('uid'))->update(['is_default'=>'0']);
            if(!$update) {
                return ['code' => 0, 'message' => '请求失败', 'data' => ''];
            }
        }
        //判断是创建还是修改
        if(empty($id)) {
            $address = ['c_id'=>session('uid'),'province_id'=>$province_id,'city_id'=>$city_id,'area_id'=>$area_id,'address'=>$address,'name'=>$name,'phone'=>$phone,'is_default'=>$is_default];
            $res = HarvestAddress::create($address);
        }else{
            $address = ['province_id'=>$province_id,'city_id'=>$city_id,'area_id'=>$area_id,'address'=>$address,'name'=>$name,'phone'=>$phone,'is_default'=>$is_default];
            $res = HarvestAddress::where('id', $id)->update($address);
        }
        //判断是否成功
        if(!$res) {
            return ['code' => 0, 'message' => '请求失败', 'data' => ''];
        }
        return ['code' => 1, 'message' => '请求成功', 'data' => ''];
    }

    /**
     * 删除收获地址
     *
     * @return array()
     */
    public function delAddress(Request $request) {
        $id = $request->post('id');

        //修改数据状态
        $address = ['is_del' => '0'];
        $res = HarvestAddress::where('id', $id)->where('c_id', session('uid'))->update($address);

        //判断是否成功
        if(!$res) {
            return ['code' => 0, 'message' => '删除失败', 'data' => ''];
        }
        return ['code' => 1, 'message' => '删除成功', 'data' => ''];
    }

    /**
     * 实名认证
     *
     * @param   uid     string        用户ID
     * @param   real_name     string        真实姓名
     * @param   idCard     string        身份证号
     * @return array()
     */
    public function realNameAuth(Request $request)
    {
        //获取参数
        $real_name = $request->post('real_name');
        $id_card = $request->post('idCard');

        //开启事务
        DB::beginTransaction();

        $user = Consumer::where('id', session('uid'))->first();
        if(!empty($user['real_name']) && !empty($user['id_number']) && !empty($user['real_time'])) {
            return ['code' => 0, 'message' => '用户已实名', 'data' => ''];
        }

        $update = [
            'real_name' => $real_name,
            'id_number' => $id_card,
            'real_time' => time()
        ];
        $res = Consumer::where('id', session('uid'))->update($update);

        if(!$res) {
            DB::rollBack();
            return ['code' => 0, 'message' => '实名失败', 'data' => ''];
        }

        if(Config::get('systems.environment') == 'production') {
            $res = $this->_realNameAuth->idCard($real_name, $id_card);
            if ($res != '1') {
                DB::rollBack();
                return ['code' => 0, 'message' => $res, 'data' => ''];
            }
        }
        //提交数据
        DB::commit();
        return ['code' => 1, 'message' => '实名成功', 'data' => ''];
    }

    /**
     * 银行卡列表
     *
     * @param   uid     string      用户
     * @return array()
     */
    public function bankList()
    {
        $info = ConsumerBank::where('c_id', session('uid'))->with('bank')->where('is_del', '1')->get();
        $list = array();
        //拼装参数
        if(!empty($info)) {
            foreach ($info as $k => $v) {
                $res['id'] = $v['id'];
                $res['abbreviation'] = $v['bank']['abbreviation'];
                $res['bank_name'] = $v['bank']['name'];
                $res['bank_logo'] = $v['bank']['logo'];
                $res['bank_card'] = $v['bank_card'];
                $res['is_default'] = $v['is_default'];
                $list[] = $res;
            }
        }

        return ['code' => 1, 'message' => '请求成功', 'data' => $list];
    }

    /**
     * 编辑银行卡
     *
     * @param   id      string      银行卡ID
     * @param   bank_card      string      银行卡号
     * @param   mobile      string      电话号
     * @param   is_default      string      是否默认
     * @param   uid      string      用户ID
     * @return array()
     */
    public function editBank(Request $request)
    {
        //获取参数
        $id = $request->post('id');
        $bank_card = $request->post('bank_card');
        $reserved_mobile = $request->post('mobile');
        $is_default = $request->post('is_default');

        //若设置默认，则取消以前的默认
        if($is_default == '1') {
            ConsumerBank::where('is_default', '1')->where('c_id', session('uid'))->update(['is_default'=>'0']);
        }

        $bankInfo =  $this->_bankAuth->CheckBank($bank_card);
        if($bankInfo['code'] != '1') {
            return ['code' => 0, 'message' => $bankInfo['msg'], 'data' => ''];
        }
        if(empty($id)) {
            $bank = [
                'c_id'=>session('uid'),
                'bank_name'=>$bankInfo['msg'],
                'bank_card'=>$bank_card,
                'reserved_mobile'=>$reserved_mobile,
                'create_time'=>time(),
                'is_del'=>'1',
                'is_default'=>$is_default
            ];
            $res = ConsumerBank::create($bank);
        }else{
            $info = ConsumerBank::where('id', $id)->where('is_del', '1')->first();
            if(empty($info)) {
                return ['code' => 0, 'message' => '用户所修改银行卡不存在', 'data' => ''];
            }
            $bank = [
                'bank_name'=>'',
                'bank_card'=>$bank_card,
                'reserved_mobile'=>$reserved_mobile,
                'create_time'=>time(),
                'is_del'=>'1',
                'is_default'=>$is_default
            ];
            $res = ConsumerBank::where('id', $id)->update($bank);
        }
        //判断是否成功
        if(!$res) {
            return ['code' => 0, 'message' => '请求失败', 'data' => ''];
        }
        return ['code' => 1, 'message' => '请求成功', 'data' => ''];
    }

    /**
     * 删除银行卡
     *
     * @param    id      string      银行卡ID
     * @param    uid     string      用户ID
     * @return array()
     */
    public function delBank(Request $request)
    {
        //获取参数
        $id = $request->post('id');

        //修改数据状态
        $address = ['is_del' => '0'];
        $res = ConsumerBank::where('id', $id)->where('c_id', session('uid'))->update($address);

        //判断是否成功
        if(!$res) {
            return ['code' => 0, 'message' => '删除失败', 'data' => ''];
        }
        return ['code' => 1, 'message' => '删除成功', 'data' => ''];
    }
}
