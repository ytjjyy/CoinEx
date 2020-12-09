<div class="form-group">
    <div class="form-group">
        <lable>{{$lable}}</lable>
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-pencil"></i>
            </div>
            <input type="{{ $type }}" class="form-control " placeholder="{{$placeholder}}" name="{{$name}}"
                   value="{{ request($name, $value) }}">
        </div>
    </div>
</div>
