<tbody>
<tr>
    <th></th>
    <th> ID</th>
    <th> 买入次数</th>
    <th> 卖出次数</th>
    <th> 用户手机号</th>
    <th> 邮箱</th>
    <th> 币种名称</th>
    <th> 结算币种</th>
    <th> 刷新时间</th>
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
            <td>
                @if(isset($item['id'])) {{ $item['id'] }}@endif
            </td>
            <td>
                {{ $item['buyCount'] }}
            </td>
            <td>
                {{ $item['sellCount'] }}
            </td>
            <td>
                {{ $item['mobile'] }}
            </td>
            <td>
                {{ $item['email']}}
            </td>
            <td>
                {{ $item['coinName'] }}
            </td>
            <td>
                {{ $item['settlementCurrency'] }}
            </td>
            <td>
                {{ $item['freshDate'] }}
            </td>
        </tr>
    @endforeach
@endif
</tbody>