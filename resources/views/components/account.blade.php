@php
    $num = isset($countNum) ? $countNum + 1 : 0;
    $selected = isset($selectVal) ? $selectVal : 0;
    $skip = $skip ?? [];
    $parentName = $parentName ?? '';
@endphp



@foreach ($setAccounts as $account)
    @if (in_array($account->id, $skip))
    @else
        @if ($account->subAccount->isNotEmpty())
            @if (!empty($account->parent_id))
                @php
                    $bank = 'false';
                    if (isset($account->parent) && $account->parent->unique_identifier == 8) {
                        $bank = 'true';
                    }
                @endphp
                <option {{ $selected == $account->id ? 'selected' : '' }} is_bank="{{ $bank }}"
                    value="{{ $account->id }}">
                    {{ $account->account_name }}
                    {{ $account->account_code }} {{ $account->bank_name }}
                </option>
            @endif
            <x-account :setAccounts="$account->subAccount" :skip="$skip" :selectVal="$selected" :countNum="$num" :parentName="$account->account_name" />
        @else
            @php
                $bank = 'false';
                if (isset($account->parent) && $account->parent->unique_identifier == 8) {
                    $bank = 'true';
                }
            @endphp
            <option {{ $selected == $account->id ? 'selected' : '' }} is_bank="{{ $bank }}"
                value="{{ $account->id }}">
                {{ $account->account_name }} {{ $account->account_code }}
                {{ $account->bank_name }}
            </option>
        @endif
    @endif
@endforeach
