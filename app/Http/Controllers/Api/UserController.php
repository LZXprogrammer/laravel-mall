<?php

namespace App\Http\Controllers\Api;

use App\Models\Consumer;
use App\Models\HarvestAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\realNameAuthNotification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $_realNameAuth;

    public function __construct(realNameAuthNotification $realNameAuthNotification){
        $this->_realNameAuth = $realNameAuthNotification;
    }
    /**
     * 个人中心主页
     *
     * @return \Illuminate\Http\Response
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
        returnJsonMsg('1', '请求成功', $info);
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

        returnJsonMsg('1', '请求成功', $list);
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
        //判断是创建还是修改
        if(empty($id)) {
            $address = ['c_id'=>session('uid'),'province_id'=>$province_id,'city_id'=>$city_id,'area_id'=>$area_id,'address'=>$address,'name'=>$name,'phone'=>$phone];
            $res = HarvestAddress::create($address);
        }else{
            $address = ['province_id'=>$province_id,'city_id'=>$city_id,'area_id'=>$area_id,'address'=>$address,'name'=>$name,'phone'=>$phone];
            $res = HarvestAddress::where('id', $id)->update($address);
        }
        //判断是否成功
        if(!$res) {
            returnJsonMsg('0', '请求失败', '');
        }
        returnJsonMsg('1', '请求成功', '');
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
        $res = HarvestAddress::where('id', $id)->update($address);

        //判断是否成功
        if(!$res) {
            returnJsonMsg('0', '请求失败', '');
        }
        returnJsonMsg('1', '请求成功', '');
    }

    /**
     * 实名认证
     *
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
            returnJsonMsg('0', '用户已实名', '');
        }

        $update = [
            'real_name' => $real_name,
            'id_number' => $id_card,
            'real_time' => time()
        ];
        $res = Consumer::where('id', session('uid'))->update($update);

        if(!$res) {
            DB::rollBack();
            returnJsonMsg('0', '实名失败', '');
        }

        if(Config::get('systems.environment') == 'production') {
            $res = $this->_realNameAuth->idCard($real_name, $id_card);
            if ($res != '1') {
                DB::rollBack();
                returnJsonMsg('0', $res, '');
            }
        }
        //提交数据
        DB::commit();
        returnJsonMsg('1', '实名成功', '');
    }
}
