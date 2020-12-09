<meta name="csrf-token" content="{{ csrf_token() }}">

@if(!empty($errorMsg))
    <div class="box-body">
        <div class="fields-group">
            <div class="form-group  ">
                <label for="type" class="col-sm-10 control-label"><font color="red">{{ $errorMsg }}</font> </label>
            </div>
        </div>
    </div>
@endif

<section class="content-header">
    <h1>
        交易操盘，托管项目：{{ $fundraising['name'] }}
        <small>(ID:{{ $fundraising['id'] }})</small>
    </h1>
</section>
<section class="content">
    <form action="/admin/fundraising/create-trade" method="post"
          class="form-horizontal" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" name="fundRaisingId" value="{{ $fundraising['id'] }}">
        <div class="box-body">

            <div class="fields-group">

                <div class="form-group  ">
                    <label for="marketName" class="col-sm-2 control-label">交易市场</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                            <select class="form-control" name="marketName">
                                <option value="">请选择交易市场</option>
                                @if(!empty($markets))
                                    @foreach($markets as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group  ">
                    <label for="price" class="col-sm-2 control-label">价格</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                            <input type="text" id="price" name="price" value="" class="form-control price" placeholder="价格（最多4位小数）">
                        </div>
                    </div>
                </div>

                <div class="form-group  ">
                    <label for="amount" class="col-sm-2 control-label">数量</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                            <input type="text" id="amount" name="amount" value="" class="form-control amount" placeholder="数量（最多4位小数）">
                        </div>
                    </div>
                </div>

                <div class="form-group  ">
                    <label for="type" class="col-sm-2 control-label">交易类型</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                            <select class="form-control" name="type">
                                <option value="BUY">买入</option>
                                <option value="SELL">卖出</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <div class="col-md-2">
                    </div>
                    <div class="col-md-8">
                        <div class="btn-group pull-right">
                            <input type="submit" class="btn btn-info pull-right" value="提交"/>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>

    <div class="box-body">
        <div class="fields-group">
            <div class="form-group  ">
                <label for="type" class="col-sm-2 control-label"></label>
                <a class="col-sm-2" href="/admin/fundraising-operate?id={{ $fundraising['id'] }}">刷新页面</a>
                <a class="col-sm-2" href="/admin/fundraising-open-trade-list?fundRaisingId={{ $fundraising['id'] }}" target="_blank">当前挂单</a>
                <a class="col-sm-2" href="/admin/fundraising-trade-log-list?fundRaisingId={{ $fundraising['id'] }}" target="_blank">历史成交</a>
                <label for="type" class="col-sm-2 control-label"></label>
            </div>
        </div>
    </div>

@if(!empty($balances))
    <h2>
        当前资金列表：
    </h2>
        <div class="box-body">
            <div class="fields-group">
                <div class="form-group  ">
                    <table border="1" width="80%">
                        <tbody>
                        <tr><th>币种名称</th><th>可用余额</th><th>冻结金额</th><th>可用折合</th><th>冻结折合</th></tr>
                        @foreach($balances as $coinBalance)
                            @if(!empty($coinBalance['availableBalance']) || !empty($coinBalance['freezeBalance']))
                                <tr><td>{{ $coinBalance['coinName'] }}</td><td>{{ $coinBalance['availableBalance'] }}</td><td>{{ $coinBalance['freezeBalance'] }}</td>
                                    <td>{{ $coinBalance['availableConvert'] }}</td><td>{{ $coinBalance['freezeConvert'] }}</td></tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
@endif
</section>