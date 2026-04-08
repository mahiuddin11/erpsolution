<table class="table table-striped">
    <thead>
        <tr>
            <th>Transaction ID</th>
            <th>Account Name</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->invoice }}</td>
                <td>{{ $transaction->account_name }}</td>
                <td>{{ number_format($transaction->debit, 2) }}</td>
                <td>{{ number_format($transaction->credit, 2) }}</td>
                <td>{{ $transaction->created_at }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td >Total Amount:</td>
            <td >{{$amount}}</td>
        </tr>
    </tfoot>
</table>

