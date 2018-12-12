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
        <td>买家ID：</td>
        <td>{{ $order['c_id'] }}</td>
        <td>买家账号：</td>
        <td>{{ $order['mobile'] }}</td>
        <td>买家用户名：</td>
        <td>{{ $order['nick_name'] }}</td>
        <td>买家真实名：</td>
        <td>{{ $order['real_name'] }}</td>
      </tr>
      <tr>
        <td>下单时间：</td>
        <td>{{ date('Y-m-d H:i:s', $order['create_time']) }}</td>
        <td>支付时间：</td>
        <td>{{ date('Y-m-d H:i:s', $order['paid_time']) }}</td>
        <td>支付渠道单号：</td>
        <td>12312312312312312312312312312312</td>
        <td>支付方式：</td>
        <td>wechat</td>
      </tr>
      <tr>
        <td><b>收货地址</b></td>
        <td colspan="3">{{ $order['address']['address'] }}</td>
        <td>收货人</td>
        <td>{{ $order['address']['name'] }}</td>
        <td>收货人电话</td>
        <td>{{ $order['address']['phone'] }}</td>
      </tr>
      <tr>
        <td rowspan="{{ count($order['goods']) + 1 }}">商品列表</td>
        
        <td>商品名称</td>
        <td>商品图片</td>
        <td>通道</td>
        <td>单价</td>
        <td>数量</td>  
        <td>小计</td>
      </tr>
        @foreach($order['goods'] as $good)
        <tr>
            <td>{{ $good['name'] }}</td>
            <td>12</td>
            <td>12</td>
            <td>{{ $good['price'] }}</td>
            <td>{{ $good['amount'] }}</td>
            <td>{{ $good['price'] * $good['amount'] }}</td>
        </tr>
        @endforeach
      <tr>
        <td>订单金额：</td>
        <td colspan="4">￥ {{ $order['total_amount'] }}</td>
      </tr>
      </tbody>
    </table>
  </div>
</div>