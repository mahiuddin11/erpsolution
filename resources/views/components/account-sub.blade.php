@foreach ($subacounts as $account)
    <ul class="nested">
            <li>
                <i class="fas fa-folder-open folder-icone rotate"></i>
              <a href="{{route("settings.account.destroy",$account->id)}}" onclick="return confirm('Are you sure?')" class="btn btn-sm "> <i style="color: red" class="fas fa-trash folder-icone rotate"></i></a>
                <a class="text-dark" href="{{ route('settings.account.edit', $account->id) }}">
                    <span>{{ $account->head_code ? $account->head_code . ' -' : '' }}
                        {{ $account->account_name }} {{ $account->account_code }}  {{ $account->bank_name }}
                    </span>
                </a>
                @if ($account->subAccount->isNotEmpty())
                    <x-account-sub :subacounts="$account->subAccount" />
                @endif
            </li>
    </ul>
@endforeach
