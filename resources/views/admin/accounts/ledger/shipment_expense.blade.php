@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="page-header"><a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a></div>
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            @if ($accountName)
                                <h4>{{ $accountName }} Ledger</h4>
                            @else
                                <h4>Account Name Not Found</h4>
                            @endif
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        <div class="text-center mb-4 company-name-container">
                            @php
                            $company = \App\Models\CompanyDetails::select('company_name')->first();
                            @endphp
                            <h2>{{ $company->company_name }}</h2>

                            @if ($accountName)
                                <h4>{{ $accountName }} Ledger</h4>
                            @else
                                <h4>Account Name Not Found</h4>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table id="dataTransactionsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Payment Type</th>
                                        <th>Transaction Type</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $balance = $totalBalance;
                                    @endphp

                                    @foreach($transactions as $index => $transaction)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') }}</td>
                                            <td>{{ $transaction->description }}</td>
                                            <td>{{ $transaction->paymentType }}</td>
                                            <td>{{ $transaction->transaction_type }}</td>
                                            @if(in_array($transaction->transaction_type, ['Current', 'Prepaid', 'Due Adjust']))
                                            <td>{{ $transaction->expense_cost }}</td>
                                            <td></td>
                                            <td>{{ $balance }}</td>
                                            @php
                                                $balance = $balance - $transaction->expense_cost;
                                            @endphp
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection