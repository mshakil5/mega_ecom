@extends('supplier.supplier')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Dashboard</h1>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

  <!-- content area -->
  <section class="content">
    <div class="container-fluid">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-warning">
            <div class="inner">

              @php
                $saleCount = \App\Models\Purchase::where('supplier_id', Auth::guard('supplier')->user()->id)->count();
              @endphp


              <h3>{{ $saleCount }}</h3>

              <p>Sales Count</p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
            <a href="{{ route('productPurchaseHistory.supplier') }}" class="small-box-footer">All Sales <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-success">
            <div class="inner">

              @php
                  $supplierId = Auth::guard('supplier')->user()->id;
                  $supplier = \App\Models\Supplier::findOrFail($supplierId);
                  $productIds = $supplier->supplierStocks()->pluck('product_id');
                  $orderCount = \App\Models\Order::whereHas('orderDetails', function ($query) use ($productIds) {
                      $query->whereIn('product_id', $productIds);
                  })->count();
              @endphp

              <h3>{{ $orderCount }}</h3>

              <p>Orders Count</p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
            <a href="{{ route('order.supplier') }}" class="small-box-footer">All Sales <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
    </div>
  </section>
@endsection

@section('script')

@endsection