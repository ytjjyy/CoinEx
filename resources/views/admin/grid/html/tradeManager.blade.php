<tbody>
<tr>
    <th></th>
    <th> ID</th>
    <th> 用户名</th>
    <th> 真实姓名</th>
    <th> 手机号</th>
    <th> 交易对</th>
    <th> 交易类型</th>
    <th> 单价</th>
    <th> 委托数量</th>
    <th> 成交数量</th>
    <th> 成交价格</th>
    <th> 交易状态</th>
    <th> 委托时间</th>
    <th> 操作</th>
</tr>
@if(!empty($data))
    @foreach($data as $item)
        <tr>
            <td>
                <div class="icheckbox_minimal-blue" aria - checked="false" aria - disabled="false"
                     style="position: relative;">
                    <input type="checkbox" class="grid-row-checkbox" data - id="@if(isset($item['id'])) {{ $item['id'] }}@endif"
                           style="position: absolute; opacity: 0;">
                    <ins class="iCheck-helper"
                         style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                </div>
            </td>
            <td @if($item['isRed'] == 1) style="color: red" @endif>
                @if(isset($item['id'])) {{ $item['id'] }}@endif
            </td>
            <td @if($item['isRed'] == 1) style="color: red" @endif>
                @if(isset($item['userName'])) {{ $item['userName'] }}@endif
            </td>
            <td @if($item['isRed'] == 1) style="color: red" @endif>
                {{ $item['realName'] }}
            </td>
            <td @if($item['isRed'] == 1) style="color: red" @endif>
                {{ $item['mobile'] }}
            </td>
            <td @if($item['isRed'] == 1) style="color: red" @endif>
                {{ $item['coinName'] . '/' . $item['settlementCurrency'] }}
            </td>
            <td @if($item['isRed'] == 1) style="color: red" @endif>
                @if($item['type'] == "BUY") 买单 @else 卖单 @endif
            </td>
            <td @if($item['isRed'] == 1) style="color: red" @endif>
                {{ $item['price'] }}
            </td>
            <td @if($item['isRed'] == 1) style="color: red" @endif>
                {{ $item['amount'] }}
            </td>
            <td @if($item['isRed'] == 1) style="color: red" @endif>
                {{ $item['dealAmount'] }}
            </td>
            <td @if($item['isRed'] == 1) style="color: red" @endif>
                {{ $item['dealPrice'] }}
            </td>
            <td @if($item['isRed'] == 1) style="color: red" @endif>
                @if($item['status'] == 'OPEN') 未完全成交 @elseif($item['status'] == 'DEAL')
                    完全成交 @elseif($item['status'] == 'CANCELED') 已经撤单 @else 未完成 @endif
            </td>
            <td @if($item['isRed'] == 1) style="color: red" @endif>
                {{ $item['createdAt'] }}
            </td>
            <td>
                                <span><a class="btn btn-xs btn-bitbucket detail" data-id="{{ $item['id'] }}">
    撮合明细
              </a>&nbsp;&nbsp;</span>
            </td>
        </tr>
    @endforeach
@endif
<script>
    $('.detail').unbind('click').click(function () {
        let id=$(this).data('id');
        window.location.href='/admin/trade/detail/'+id;
    });
</script>
</tbody>