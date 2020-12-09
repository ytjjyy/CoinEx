<style>
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content-header">

</section>
<section class="content">

    <div class="balance">
            <span class="balance_span">
                <span style="display: inline-block;vertical-align: middle">USDT区块链上的BTC总额为：{{ $balance['totalBalance'] }}</span>
            </span>
        <span class="balance_span">
                <span style="display: inline-block;vertical-align: middle">USDT区块链上主账号的BTC个数为：{{ $balance['baseAddressBalance'] }}</span>
            </span>
        <span class="balance_span">
                <span style="display: inline-block;vertical-align: middle">USDT区块链上主账号的USDT个数为：{{ $balance['baseAddressUsdtBalance'] }}</span>
            </span>
    </div>


    <form action="/admin/coin/collectUsdt" method="post" class="form-horizontal" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="box-body">

            <div class="fields-group">

                <div class="form-group  ">

                    <label for="registerReward" class="col-sm-2 control-label">在USDT区块链节点上将BTC转账到主账号的个数</label>

                    <div class="col-sm-8">
                        <div class="input-group">

                            <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

                            <input type="text" id="amount" name="amount"
                                   value=""
                                   class="form-control amount" placeholder="输入">
                        </div>
                    </div>

                </div>

                <!-- /.box-body -->
                <div class="box-footer">

                    <div class="col-md-2">

                    </div>
                    <div class="col-md-8">

                        <div class="btn-group pull-right">
                            <input type="submit" class="btn btn-info pull-right"
                                   value="提交"/>

                        </div>


                    </div>

                </div>

            {{--<input type="hidden" name="_method" value="PUT" class="_method">--}}

            {{--<input type="hidden" name="_previous_" value="http://cmd.blaine.com/admin/tradeMarket"--}}
            {{--class="_previous_">--}}

            <!-- /.box-footer -->
            </div>
        </div>
    </form>

</section>