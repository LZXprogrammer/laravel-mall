<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title">订单流水号：</h3>
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
        <td></td>
        <td>买家账号：</td>
        <td></td>
        <td>买家用户名：</td>
        <td></td>
      </tr>
      <tr>
        <td>支付方式：</td>
        <td></td>
        <td>支付时间：</td>
        <td></td>
        <td>支付渠道单号：</td>
        <td></td>
      </tr>
      <tr>
        <td>收货地址</td>
        <td colspan="4"></td>
      </tr>
      <tr>
        <td rowspan="">商品列表</td>
        <td>商品名称</td>
        <td>单价</td>
        <td>数量</td>
        <td>小计</td>
      </tr>
      
      <tr>
        <td>订单金额：</td>
        <td colspan="4">￥</td>
      </tr>
      </tbody>
    </table>
  </div>
</div>