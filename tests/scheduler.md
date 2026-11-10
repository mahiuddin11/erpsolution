[web@hps2 test.wtbl.com.bd]$ grep -c "x-account" resources/views/backend/pages/settings/journal_voucher/create.blade.php1
[web@hps2 test.wtbl.com.bd]$ grep -n "foreach\|x-account\|<option" resources/views/components/account.blade.php
10:@foreach ($setAccounts as $account)
21:                <option {{ $selected == $account->id ? 'selected' : '' }} is_bank="{{ $bank }}"
27:            <x-account :setAccounts="$account->subAccount" :skip="$skip" :selectVal="$selected" :countNum="$num" :parentName="$account->account_name" />
35:            <option {{ $selected == $account->id ? 'selected' : '' }} is_bank="{{ $bank }}"
42:@endforeach
56:<!--@foreach ($setAccounts as $account)-->
60:<!--            <x-account :setAccounts="$account->subAccount" :skip="$skip" :selectVal="$selected" :countNum="$num" />-->
67:<!--            <option {{ $selected == $account->id ? 'selected' : '' }} is_bank="{{ isset($bank) ? $bank : 'false' }}"-->
73:<!--@endforeach-->
[web@hps2 test.wtbl.com.bd]$ ^C[web@hps2 test.wtbl.com.bd]$
