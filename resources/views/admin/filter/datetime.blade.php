<link rel="stylesheet"
      href="/vendor/laravel-admin/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">
<div class="form-group">
    <div class="form-group">
        <label>{{ $label }}</label>
        <i class="fa fa-calendar"></i>
        <input type="text" class="form-control"
               name="{{$name}}"
               value="{{ request($name, $value) }} " id="{{$name}}"
        >
    </div>
</div>
<script src="/vendor/laravel-admin/moment/min/moment-with-locales.min.js"></script>
<script src="/vendor/laravel-admin/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script>
    $("#{{ $name }}").parent().datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        locale: 'zh-CN',
        allowInputToggle: true
    });
</script>