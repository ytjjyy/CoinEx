<style>
    .status {
        width: 800px;
        height: 119px;
        line-height: 119px;
        border: 1px solid #000000;
        text-align: center;
        float: left;
        margin-right: 10px;
    }

    .status .status_span i {
        margin-right: 5px;
        display: inline-block;
        vertical-align: middle
    }

    .circle {
        width: 20px;
        height: 20px;
        background-color: red;
        border-radius: 50%;
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%;
    }

    .ping {
        width: 20px;
        height: 20px;
        background-color: green;
        border-radius: 50%;
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%;
    }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content-header">

</section>
<section class="content">

    <div class="status">
            <span class="status_span">
                <i class="@if(isset($config['tradeBool']) && $config['tradeBool']) ping @else circle @endif"></i>
                <span style="display: inline-block;vertical-align: middle">挂单监控{{ $config['tradeListDetail'] }}</span>
            </span>
    </div>
    <div class="status">
            <span class="status_span">
                <i class="@if(isset($config['cashBool']) && $config['cashBool']) ping @else circle @endif"></i>
                <span style="display: inline-block;vertical-align: middle">持币监控</span>
            </span>
    </div>
    {{--<div class="status">--}}
            {{--<span class="status_span">--}}
                {{--<i class="@if(isset($config['positionsBool']) && $config['positionsBool']) ping @else circle @endif"></i>--}}
                {{--<span style="display: inline-block;vertical-align: middle">持仓监控</span>--}}
            {{--</span>--}}
    {{--</div>--}}
    <div class="status">
            <span class="status_span">
                <i class="@if(isset($config['hfTradeBool']) && $config['hfTradeBool']) ping @else circle @endif"></i>
                <span style="display: inline-block;vertical-align: middle">高频交易监控{{ $config['hfTradeListDetail'] }}</span>
            </span>
    </div>
    <div class="status">
            <span class="status_span">
                <i class="@if(isset($config['serviceBool']) && $config['serviceBool']) ping @else circle @endif"></i>
                <span style="display: inline-block;vertical-align: middle">服务器监控 </span>
            </span>
    </div>
    <div class="status">
            <span class="status_span">
                <i class="@if(isset($config['onlieNumber']) && $config['onlieNumber']) ping @else circle @endif"></i>
                <span style="display: inline-block;vertical-align: middle">在线人数:{{$config['onlieNumber']}} </span>
            </span>
    </div>

</section>