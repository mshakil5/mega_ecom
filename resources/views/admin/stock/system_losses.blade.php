@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">System Losses</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="systemLossTable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Warehouse</th>
                                        <th>Performed By</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($systemLosses as $key => $systemLoss)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($systemLoss->created_at)->format('d-m-Y') }}</td>
                                        <td>{{ ($systemLoss->product->product_code ?? '') . '-' . ($systemLoss->product->name ?? '') . '-' . ($systemLoss->size ?? '') . '-' . ($systemLoss->color ?? '') }}
                                          @if(optional($systemLoss->product)->is_zip)
                                            - Zip: {{ $systemLoss->zip == 1 ? 'Zip' : 'No' }}
                                          @endif
                                        </td>
                                        <td>{{ $systemLoss->quantity }}</td>
                                        <td>{{ $systemLoss->warehouse->name ?? '' }}</td>
                                        <td>{{ $systemLoss->user->name ?? '' }}</td>
                                        <td>{!! $systemLoss->reason !!}</td>
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

@section('script')
<script>
    $(function () {
        $('#systemLossTable').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"]
        }).buttons().container().appendTo('#systemLossTable_wrapper .col-md-6:eq(0)');
    });
</script>
@endsection
