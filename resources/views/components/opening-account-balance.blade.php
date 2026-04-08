@foreach ($accounts as $account)
    @if ($account->subAccount->isEmpty())
        <tr>
            <td>{{ $account->account_name }} {{ $account->account_code }}  {{ $account->bank_name }}</td>
            <td>
                <input type="number" step="0.01" name="accounts[{{ $account->id }}][debit]"
                    value="{{  $account->balance_type == "debit" ? $account->opening_balance : 0 }}" class="form-control debit">
            </td>
            <td>
                <input type="number" step="0.01"  name="accounts[{{ $account->id }}][credit]"
                    value="{{ $account->balance_type == "credit" ? $account->opening_balance : 0 }}" class="form-control credit">
            </td>
        </tr>
    @else
        <x-opening-account-balance :accounts="$account->subAccount" />
    @endif
@endforeach
