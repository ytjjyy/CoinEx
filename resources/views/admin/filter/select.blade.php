<div class="form-group">
    <div class="form-group">
        <div class="form-group">
            <label>{{ $lable }}</label>
            <select class="form-control " name="{{$name}}" style="width: 100%;" tabindex="-1" aria-hidden="true">
                <option value="">请选择</option>
                @foreach($options as $select => $option)
                    <option value="{{$select}}" {{ (string)$select === request($name, $value) ?'selected':'' }}>{{$option}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
