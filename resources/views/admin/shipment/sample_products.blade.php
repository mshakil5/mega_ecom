@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Sample Products</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="sampleProducts">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Shipment Details</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sampleProducts as $key => $sampleProduct)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $sampleProduct->shipment->shipping->shipping_id }}
                                         - {{ $sampleProduct->shipment->shipping->shipping_date }} - {{ $sampleProduct->shipment->shipping->shipping_name }}</td>
                                        <td>{{ $sampleProduct->product->product_code }} - {{ $sampleProduct->product->name }}</td>
                                        <td>{{ $sampleProduct->sample_quantity }}</td>
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
        $('#sampleProducts').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"]
        }).buttons().container().appendTo('#sampleProducts_wrapper .col-md-6:eq(0)');
    });
</script>
@endsection