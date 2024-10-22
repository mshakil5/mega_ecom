@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="mb-3">
            <a href="{{ route('allsupplier') }}" class="btn btn-secondary">
              <i class="fa fa-arrow-left"></i> Back
            </a>
          </div>

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">{{$supplier->name}} All Transactions</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

              <div class="text-center mb-4 company-name-container">
                  <h2>{{$supplier->name}}</h2>

                      <h4>Supplier transaction history</h4>
              </div>



              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sl</th>
                  <th>Date</th>
                  <th>Description</th>
                  <th>Payment Type</th>
                  <th>Document</th>
                  <th>Dr Amount</th>
                  <th>Cr Amount</th>
                  <th>Balance</th>
                  <th>Note</th>
                </tr>
                </thead>
                <tbody>

                  @php
                      $balance = $totalBalance;
                  @endphp


                  @foreach ($transactions as $key => $data)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                   <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                    <td>{{ $data->table_type }}</td>
                    <td>{{ $data->payment_type }}</td>
                    <td>
                        @if($data->document)
                            <a href="{{ asset($data->document) }}" target="_blank" class="btn btn-secondary">
                                View
                            </a>
                        @else
                            Not available
                        @endif
                    </td>

                    @if(in_array($data->payment_type, ['Credit']))
                      <td style="text-align: right">{{ number_format($data->at_amount, 2) }}</td>
                      <td></td>
                      <td style="text-align: right">{{ number_format($balance, 2) }}</td>
                      @php
                          $balance = $balance - $data->at_amount;
                      @endphp
                    @elseif(in_array($data->payment_type, ['Cash', 'Bank', 'Return']))
                      <td></td>
                      <td style="text-align: right">{{ number_format($data->at_amount, 2) }}</td>
                      <td style="text-align: right">{{ number_format($balance, 2) }}</td>
                      @php
                          $balance = $balance + $data->at_amount;
                      @endphp
                    @endif
                    <td>{!! $data->note !!}</td>
                  </tr>
                  @endforeach
                
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>

@endsection
@section('script')
<script>
    $(function () {
      $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

@endsection