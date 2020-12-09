<tbody>
<tr>
    <th></th>
    <th> ID</th>
    <th> 用户</th>
    <th> 币种名称</th>
    <th> 目标地址</th>
    <th> 转入数量</th>
    <th> 转出数量</th>
    <th> 来源地址</th>
    <th> 类型</th>
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
                @if(isset($item['account'])) {{ $item['account'] }}@endif
            </td>
            <td>
                @if(isset($item['coinName'])) {{ $item['coinName'] }}@endif
            </td>
            <td>
                @if(isset($item['goalAddress'])) {{ $item['goalAddress'] }}@endif
            </td>
            <td>
                @if(isset($item['rollInNumber'])) {{ $item['rollInNumber'] }}@endif
            </td>
            <td>
                @if(isset($item['rollOutNumber'])) {{ $item['rollOutNumber'] }}@endif
            </td>
            <td>
                @if(isset($item['sourceAddress'])) {{ $item['sourceAddress'] }}@endif
            </td>
            <td>
                @if(isset($item['type']) && $item['type'] == 1) 转入 @else 转出 @endif;
             </td>
        </tr>
    @endforeach
@endif
</tbody>