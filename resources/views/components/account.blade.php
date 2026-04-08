@php
    $num = isset($countNum) ? $countNum + 1 : 0;
    $selected = isset($selectVal) ? $selectVal : 0;
    $skip = $skip ?? [];
@endphp


@foreach ($setAccounts as $account)

    @if (in_array($account->id, $skip))
    @else
        @if ($account->subAccount->isNotEmpty())
            <x-account :setAccounts="$account->subAccount" :skip="$skip" :selectVal="$selected" :countNum="$num" />
        @else
            @php
                if(isset($account->parent) && $account->parent->unique_identifier == 8){
                    $bank = "true";
                }
            @endphp
            <option {{ $selected == $account->id ? 'selected' : '' }} is_bank="{{isset($bank) ? $bank : "false" }}" value="{{ $account->id }}">
                {{ $account->account_name }} {{ $account->account_code }}  {{ $account->bank_name }}
            </option>
        @endif
    @endif
@endforeach
