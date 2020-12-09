<style>
    .status {
        width: 147px;
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
    <h1>
        服务器状态
        <small>服务器状态</small>
    </h1>
</section>
<section class="content">
    @if(!empty($service))
        @foreach($service as $value)
        <div class="status">
            <span class="status_span">
                <i class="@if($value['serviceStatus']) ping @else circle @endif"></i>
                <span style="display: inline-block;vertical-align: middle">{{ $value['serviceName'] }} </span>
            </span>
        </div>
        @endforeach
    @endif
</section>
<script>
    var mintue = "{{ $minute }}";
    if(mintue > 0){
        setInterval(function () {
            let pathname = window.location.pathname;
            if (pathname == '/admin/serviceStatus') {
                window.location.reload(true);
            }
        }, mintue*60000);
    }
</script>