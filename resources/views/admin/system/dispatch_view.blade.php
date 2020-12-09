<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content-header">
    <h1>
        拨币
        <small>拨币</small>
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
                <form action="/admin/dispatch/coin" method="post"
                      class="form-horizontal" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="box-body">

                        <div class="fields-group">


                            <div class="form-group  ">
                                <label for="tradeReward" class="col-sm-2 control-label">锁仓设置</label>
                                <div class="col-sm-8">
                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

                                        {{--<input type="number" id="tradeReward" name="tradeReward"--}}
                                               {{--value="{{ $config['tradeReward'] }}"--}}
                                               {{--class="form-control tradeReward" placeholder="输入">--}}
                                        <select class="form-control" name="dispatchId">
                                            <option value="">请选择锁仓</option>
                                            @if(!empty($dispatch_config))
                                                @foreach($dispatch_config as $item)
                                            <option value="{{ $item['id'] }}">{{ $item['lockName'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group  ">
                                <label for="tradeReward" class="col-sm-2 control-label">币种选择</label>
                                <div class="col-sm-8">
                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

                                        {{--<input type="number" id="tradeReward" name="tradeReward"--}}
                                        {{--value="{{ $config['tradeReward'] }}"--}}
                                        {{--class="form-control tradeReward" placeholder="输入">--}}
                                        <select class="form-control" name="coinName">
                                            <option value="">币种选择</option>
                                            @if(!empty($coinName))
                                                @foreach($coinName as $value)
                                                    <option value="{{ $value['name'] }}">{{ $value['name'] }}</option>
                                                @endforeach
                                            @endif
                                            <!--<option value="DET">DET</option>-->
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group  ">
                                <label for="tradeReward" class="col-sm-2 control-label">拨币数量</label>
                                <div class="col-sm-8">
                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

                                        <input type="text" id="amount" name="amount"
                                        value=""
                                        class="form-control amout" placeholder="输入拨币数量">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group  ">
                                <label for="tradeReward" class="col-sm-2 control-label">备注</label>
                                <div class="col-sm-8">
                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

                                        <input type="text" id="comment" name="comment"
                                               value=""
                                               class="form-control comment" placeholder="备注">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group  ">
                                <label for="tradeReward" class="col-sm-2 control-label">请填写用户的用户名(用,号分割开)</label>
                                <div class="col-sm-8">
                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                        <textarea  class="form-control" name="phone" placeholder="用户名,请用逗号分割开"></textarea>
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
