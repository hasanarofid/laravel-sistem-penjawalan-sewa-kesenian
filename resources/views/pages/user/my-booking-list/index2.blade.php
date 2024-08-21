@extends('layouts.main')

@section('title', 'My Booking List - Sanggar Seni Putra Karuhun')

@section('header-title', 'My Booking List')
    
@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Transaksi</a></div>
  <div class="breadcrumb-item active">My Booking List</div>
@endsection

@section('section-title', 'My Booking List')
    
@section('section-lead')
  Berikut ini adalah daftar seluruh booking yang pernah kamu buat.
@endsection

@section('content')

      <a href="{{ route('my-booking-list.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i>&nbsp;Booking</a>
  
<table class="table table-bordered">
  <thead>
    <tr>
      <th>No</th>
      <th>Kode Transaksi</th>
      <th>Foto</th>
      <th>Video</th>
      <th>Kesenian</th>
      <th>Harga Paket</th>
      <th>Tanggal</th>
      <th>Alamat</th>
      <th>Status</th> 
      <th>Bukti Pembayaran</th> 
      <th>Action</th> 
    </tr>
  </thead>
  <tbody>
    @php
        $no = 1;
    @endphp
    @foreach ($model as $item)
    <tr>
        <td>{{ $no++ }}</td>
        <td>
            <img src="{!! QrCode::size(200)->generate($item->kode_transaksi) !!}">
        </td>
        <td>
            <div class="gallery gallery-fw">
                <a href="{{ asset('storage/'.$item->kesenian->foto) }}" data-toggle="lightbox">
                    <img src="{{ asset('storage/'.$item->kesenian->foto) }}" class="img-fluid" style="min-width: 100px; height: 100px;">
                </a>
            </div>
        </td>

        <td>
            <div class="gallery gallery-fw">
                <video width="320" height="240" controls>`
                    <source src="{{ asset('storage/'.$item->kesenian->video) }}" type="video/mp4">`
                    Your browser does not support the video tag.`
                </video>
            </div>
        </td>
        <td>{{ $item->kesenian->nama }}</td>
        <td>{{ number_format($item->kesenian->harga) }}</td>
        <td>{{ $item->date }}</td>
        <td>{{ $item->alamat }}</td>
        <td>{{ $item->status }}</td>
        <td>
            @if ($item->status === 'DIBAYAR')
                <div class="gallery gallery-fw">
                    <a href="{{ asset('storage/'.$item->bukti_pembayaran) }}" data-toggle="lightbox">
                        <img src="{{ asset('storage/'.$item->bukti_pembayaran) }}" class="img-fluid" style="min-width: 100px; height: 100px;">
                    </a>
                </div>
            @else
                -
            @endif
        </td>
        <td>
            @if ($item->status !== 'DIBAYAR')
                <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#paymentProofModal" data-id="{{ $item->id }}">Kirim Bukti</a>
            @else
                <button class="btn btn-secondary" disabled>Kirim Bukti</button>
            @endif
        </td>
    </tr>
@endforeach

  </tbody>
</table>


@endsection


<div class="modal fade" id="paymentProofModal" tabindex="-1" role="dialog" aria-labelledby="paymentProofModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="POST" action="{{ route('submit.payment.proof') }}" enctype="multipart/form-data">
          @csrf
          <div class="modal-header">
              <h5 class="modal-title" id="paymentProofModalLabel">Kirim Bukti Pembayaran</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
            <div class="row mb-3 p-3" style="background-color: rgb(38, 0, 255); color: aliceblue; border-radius: 5px;">
                <div class="col">
                    <h5 class="text-center">NO REKENING KAMI</h5>
                    <p class="text-center mb-0">1310017243298</p>
                    <p class="text-center">Mandiri</p>
                    <p class="text-center font-weight-bold">Fanisa Nur Fauzi</p>
                </div>
            </div>
              <input type="hidden" name="item_id" id="item-id">
              <div class="form-group">
                  <label for="proof">Bukti Pembayaran</label>
                  <input type="file" class="form-control" id="proof" name="proof" required>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
              <button type="submit" class="btn btn-primary">Kirim</button>
          </div>
      </form>
      
      </div>
  </div>
</div>

@push('after-script')
<script src="//cdn.datatables.net/plug-ins/1.10.22/dataRender/ellipsis.js"></script>

<script>
  $('#paymentProofModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var itemId = button.data('id');
        var modal = $(this);
        modal.find('.modal-body #item-id').val(itemId);
    });
</script>


@endpush
