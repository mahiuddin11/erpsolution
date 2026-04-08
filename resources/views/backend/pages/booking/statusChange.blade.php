<div class="card card-outline card-info">
    <div class="card-body">
        <input type="hidden" value="{{$booking->id}}" name="booking_id" />
        <div class="form-group row">
            <label class="col-sm-4">Booking ID</label>
            <div class="col-sm-8">
                <input type="text" value="{{$booking->voucher_id}}" placeholder="" readonly class="form-control">
                @error('reason')
                <span class="error text-red text-bold"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4">Status </label>
            <div class="col-sm-8">
                <select onchange="getStatusVariation(this.value)" name="status" class="form-control select2">
                    <option value="" selected disabled>--Select Status -- </option>
                    @foreach($status as $key => $value)
                    <option @if($booking->delivery_status == $value->id) selected @endif
                        value="{{ $value->id}}">{{ $value->status}}
                    </option>
                    @endforeach
                </select>
                @error('status')
                <span class="error text-red text-bold"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="form-group row driver" style="display: none;">
            <label class="col-sm-4 assaintitle">Assain Driver</label>
            <div class="col-sm-8">
                <select name="driver_id" class="form-control select2">
                    <option value="" selected disabled>-- Select Driver -- </option>
                    @foreach($driver as $key => $value)
                    <option value="{{ $value->id}}">{{ $value->username}} [{{$value->registration_number}}]</option>
                    @endforeach
                </select>
                @error('driver_id')
                <span class="error text-red text-bold"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="form-group row reason" style="display: none;">
            <label class="col-sm-4">Reason</label>
            <div class="col-sm-8">
                <textarea type="text" name="reason" placeholder="please type someting!" class="form-control"></textarea>
                @error('reason')
                <span class="error text-red text-bold"> {{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="form-group row">
            <button type="submit" class=" btn btn-danger"><i class="fa fa-save"></i> Save</button>
        </div>
    </div>
</div>
</form>

<script>
function getStatusVariation(statusId) {
    if (statusId == 3 || statusId == 2) {
        $('.driver').show();
        $('.reason').hide();
        if (statusId == 2) {
            $('.assaintitle').text('Pickup Driver');
        }

    } else if (statusId == 10 || statusId == 7 || statusId == 8 || statusId == 9) {
        $('.reason').show();
        $('.driver').hide();
    } else if (statusId == 4) {
        $('.reason').show();
        $('.driver').show();
    } else {
        $('.reason').hide();
        $('.driver').hide();
    }

}
</script>