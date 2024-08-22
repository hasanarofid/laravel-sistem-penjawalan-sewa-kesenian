@extends('layouts.main')

@section('title', 'My Booking List - Sanggar Seni Putra Karuhun')

@section('header-title', 'My Booking List')
    
@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Transaksi</a></div>
  <div class="breadcrumb-item active">My Booking List</div>
@endsection

@section('section-title', 'My Booking List')
    
@section('section-lead')
Berikut ini adalah daftar seluruh booking dari setiap user. 
@endsection

@section('content')
<div class="table-responsive">
<table class="table table-striped display nowrap w-100" data-scroll-y="400">
  <thead>
    <tr>
      <th>No</th>
      <th>User</th>
      <th>Foto</th>
      <th>Video</th>
      <th>Kesenian</th>
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
        <td>{{ $item->user->name }}</td>
        <td>
            @php
                $item->kesenian->foto = json_decode($item->kesenian->foto, true);
                // dd($item->kesenian->foto);
            @endphp
            @if(is_array($item->kesenian->foto) && !empty($item->kesenian->foto))
                <div id="carousel-{{ $item->id }}" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        @foreach($item->kesenian->foto as $index => $foto)
                            <li data-target="#carousel-{{ $item->id }}" data-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}"></li>
                        @endforeach
                    </ol>
                    <div class="carousel-inner">
                        @foreach($item->kesenian->foto as $index => $foto)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <a href="{{ asset('storage/uploads/'.$foto) }}" data-toggle="lightbox">
                                    <img src="{{ asset('storage/uploads/'.$foto) }}" class="d-block" style="height: 200px; width:200px">
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev" href="#carousel-{{ $item->id }}" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carousel-{{ $item->id }}" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            @else
                <p>No photos available</p>
            @endif
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
        <td>{{ $item->date }}</td>
        <td>{{ $item->alamat }}</td>
        <td>{{ $item->status }}</td>
        <td>
            @if (!empty($item->bukti_pembayaran))
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
            @if ($item->status != 'DISETUJUI' && $item->status != 'DITOLAK')
            <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#paymentProofModal" data-id="{{ $item->id }}">Konfirmasi</a>
        @else
            <button class="btn btn-secondary" disabled>Sudah Konfirmasi</button>
        @endif
        
        </td>
    </tr>
@endforeach

  </tbody>
</table>
</div>

@endsection


<div class="modal fade" id="paymentProofModal" tabindex="-1" role="dialog" aria-labelledby="paymentProofModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="POST" action="{{ route('submit.konfirmasi') }}" enctype="multipart/form-data">
          @csrf
          <div class="modal-header">
              <h5 class="modal-title" id="paymentProofModalLabel">Kirim Bukti Pembayaran</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
              <input type="hidden" name="item_id" id="item-id">
              <div class="form-group">
                  <label for="proof">Status</label>
                  <select name="status" id="status" class="form-control" required>
                    <option value="">.:Pilih Status:.</option>
                    <option value="DISETUJUI">DISETUJUI</option>
                    <option value="DITOLAK">DITOLAK</option>
                  </select>
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
