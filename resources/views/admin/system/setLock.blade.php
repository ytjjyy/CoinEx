<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content-header">
    <h1>
        锁用户资产
        <small>锁用户资产</small>
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
                            {{--<a class="btn btn-sm btn-default form-history-back"><i class="fa fa-arrow-left"></i>&nbsp;返回</a>--}}
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form action="/admin/lockCoin" method="post"
                      class="form-horizontal" enctype="multipart/form-data" pjax-container>
                    {{ csrf_field() }}
                    <div class="box-body">

                        <div class="fields-group">


                            <div class="form-group  ">
                                <label for="cronId" class="col-sm-2 control-label">释放策略</label>
                                <div class="col-sm-8">
                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

                                        {{--<input type="number" id="tradeReward" name="tradeReward"--}}
                                        {{--value="{{ $config['tradeReward'] }}"--}}
                                        {{--class="form-control tradeReward" placeholder="输入">--}}
                                        <select class="form-control" name="cronId">
                                            <option value="">请选择锁仓</option>
                                            @if(!empty($corn))
                                                @foreach($corn as $item)
                                                    <option value="{{ $item['id'] }}">{{ $item['cronName'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group  ">
                                <label for="tradeReward" class="col-sm-2 control-label">用户类型</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="radio" value="1" name="type" checked> 用户名
                                        <input type="radio" value="2" name="type">用户组
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
                                            @if(!empty(getCoin()))
                                                @foreach(getCoin() as $value)
                                                    <option value="{{ $value }}">{{ $value }}</option>
                                            @endforeach
                                        @endif
                                        <!--<option value="DET">DET</option>-->
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group  ">
                                <label for="lockRate" class="col-sm-2 control-label">锁仓比例(0.1代表10%)</label>
                                <div class="col-sm-8">
                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

                                        <input type="text" id="lockRate" name="lockRate"
                                               value=""
                                               class="form-control lockRate" placeholder="锁仓比例(0.1代表10%)">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group  ">
                                <label for="releaseRate" class="col-sm-2 control-label">释放比例比例(0.1代表10%)</label>
                                <div class="col-sm-8">
                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

                                        <input type="text" id="releaseRate" name="releaseRate"
                                               value=""
                                               class="form-control releaseRate" placeholder="释放比例比例(0.1代表10%)">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group  ">
                                <label for="groupType" class="col-sm-2 control-label">用户组</label>
                                <div class="col-sm-8">
                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

                                        {{--<input type="number" id="tradeReward" name="tradeReward"--}}
                                        {{--value="{{ $config['tradeReward'] }}"--}}
                                        {{--class="form-control tradeReward" placeholder="输入">--}}
                                        <select class="form-control" name="groupType">
                                            <option value="">用户组</option>
                                            @if(!empty(getGroup()))
                                                @foreach(getGroup() as $key=>$value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        @endif
                                        <!--<option value="DET">DET</option>-->
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group  ">
                                <label for="userNames" class="col-sm-2 control-label">请填写用户的用户名(用,号分割开)</label>
                                <div class="col-sm-8">
                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                        <textarea  class="form-control" name="userNames" placeholder="用户名,请用逗号分割开"></textarea>
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
