<option value="">--(Select Sub Head)--</option>
@foreach ($subcetegoris as $each)
    <option value="{{ $each->id }}">{{ $each->name }}</option>
@endforeach
