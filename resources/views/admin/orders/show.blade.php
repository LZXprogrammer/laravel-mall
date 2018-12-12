<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title">订单流水号：{{ $order['no'] }}</h3>
    <div class="box-tools">
      <div class="btn-group pull-right" style="margin-right: 10px">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-default"><i class="fa fa-list"></i> 列表</a>
      </div>
    </div>
  </div>
  <div class="box-body">
    <table class="table table-bordered">
      <tbody>
      <tr>
        <th>买家ID：</th>
        <td>{{ $order['c_id'] }}</td>
        <th>买家账号：</th>
        <td>{{ $order['mobile'] }}</td>
        <th>买家用户名：</th>
        <td>{{ $order['nick_name'] }}</td>
        <th>买家真实名：</th>
        <td>{{ $order['real_name'] }}</td>
      </tr>
      <tr>
        <th>下单时间：</th>
        <td>{{ date('Y-m-d H:i:s', $order['create_time']) }}</td>
        <th>支付时间：</th>
        <td>{{ date('Y-m-d H:i:s', $order['paid_time']) }}</td>
        <th>支付渠道单号：</th>
        <td>{{ $order['payment_no'] }}</td>
        <th>支付方式：</th>
        <td>{{ $order['payment_method'] }}</td>
      </tr>
      <tr>
        <th><b>收货地址</b></th>
        <td colspan="3">{{ $order['address']['address'] }}</td>
        <th>收货人</th>
        <td>{{ $order['address']['name'] }}</td>
        <th>收货人电话</th>
        <td>{{ $order['address']['phone'] }}</td>
      </tr>
      <tr>
        <th rowspan="{{ count($order['goods']) + 1 }}">商品列表</th>
        <th>商品名称</th>
        <th>商品图片</th>
        <th>商品类别</th>
        <th>交易渠道</th>
        <th>单价</th>
        <th>数量</th>  
        <th>小计</th>
      </tr>
        @foreach($order['goods'] as $good)
        <tr>
            <td>{{ $good['name'] }}</td>
            <td><img src="{{ $good['show_pic'] }}" width="30px;" height="30px;" /></td>
            <td>
                @if($good['category'] == 1)
                    企业pos机
                @else
                    个人pos机
                @endif
            </td>
            <td>{{ $good['trad_channel'] }}</td>
            <td>￥ {{ $good['price'] }}</td>
            <td>{{ $good['amount'] }}</td>
            <td>￥ {{ $good['price'] * $good['amount'] }}</td>
        </tr>
        @endforeach
      <tr>
        <th>订单金额：</th>
        <td style="color:red;">￥ {{ $order['total_amount'] }}</td>
        <th>订单备注：</th>
        <td colspan="4"> {{ $order['remark'] }}</td>
      </tr>
      </tbody>
    </table>
  </div>
</div>


<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title">代理人信息</h3>
    
  </div>
  <div class="box-body">
    <table class="table table-bordered">
      <tbody>   
      <tr>
        <th>代理人等级</th>
        <th>代理人ID</th>
        <th>代理人用户名</th>
        <th>代理人账号</th>
        <th>代理人所得金额</th>
      </tr>
      @foreach($agents as $key => $agent)
      <tr>
        <td>@if($key == 'primary')
                一级代理
            @elseif($key == 'second')
                二级代理
            @elseif($key == 'three')
                三级代理
            @endif
        </td>
        <td>{{ $agent['id'] }}</td>
        <td>{{ $agent['nick_name'] }}</td>
        <td>{{ $agent['mobile'] }}</td>
        <td>￥ {{ $agent['agency_amount'] }}</td>
      </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>

