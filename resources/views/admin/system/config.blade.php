<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content-header">
    <h1>
        参数设置
        <small>参数设置</small>
    </h1>
</section>
    <section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">编辑</h3>

                    <div class="box-tools">
                        <div class="btn-group pull-right" style="margin-right: 10px">
                            {{--<a href="http://cmd.blaine.com/admin/tradeMarket" class="btn btn-sm btn-default"><i--}}
                            {{--class="fa fa-list"></i>&nbsp;列表</a>--}}
                        </div>
                        <div class="btn-group pull-right" style="margin-right: 10px">
                            <a class="btn btn-sm btn-default form-history-back"><i class="fa fa-arrow-left"></i>&nbsp;返回</a>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form action="/admin/config/system" method="post"
                      class="form-horizontal" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="box-body">

                        <div class="fields-group">

                            <div class="form-group  ">

                                <label for="registerReward" class="col-sm-2 control-label">注册奖励</label>

                                <div class="col-sm-8">
                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

                                        <input type="number" id="registerReward" name="registerReward"
                                               value="{{ $config['registerReward'] }}"
                                               class="form-control registerReward" placeholder="输入">
                                    </div>
                                </div>

                            </div>
                            <span id="helpBlock" class="help-block" style="margin-left: 60px;">
                                 注：用户成功注册，即可获得注册奖励
                            </span>
                            <div class="form-group  ">

                                <label for="referrerReward" class="col-sm-2 control-label">推荐奖励</label>

                                <div class="col-sm-8">
                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

                                        <input type="number" id="referrerReward" name="referrerReward"
                                               value="{{ $config['referrerReward'] }}"
                                               class="form-control referrerReward" placeholder="输入">
                                    </div>
                                </div>
                            </div>
                            <span id="helpBlock" class="help-block" style="margin-left: 60px;">
                                  注：用户推荐用户成功注册，被推荐人实名认证审核通过。
                            </span>

                            <div class="form-group  ">
                                <label for="otcSellFreeze" class="col-sm-2 control-label">otc挂卖单需要冻结的DT个数</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                        <input type="text" id="otcSellFreeze" name="otcSellFreeze"
                                               value="{{ $config['otcSellFreeze'] }}"
                                               class="form-control otcSellFreeze" placeholder="输入">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group  ">
                                <label for="otcCancelPriFeeRate" class="col-sm-2 control-label">otc取消挂单权限需要扣除的手续费比例</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                        <input type="text" id="otcCancelPriFeeRate" name="otcCancelPriFeeRate"
                                               value="{{ $config['otcCancelPriFeeRate'] }}"
                                               class="form-control otcCancelPriFeeRate" placeholder="输入">
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
                                               value="保存"/>

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
            </div>
        </div>

    </div>

</section>
<script>
    setTimeout(function () {
        $('.alert-success').find('button').click();
    }, 2000);
</script>
