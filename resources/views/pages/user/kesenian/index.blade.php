@extends('layouts.main')

@section('title', 'Data Kesenian - Sanggar Seni Putra Karuhun')

@section('header-title', 'Data Kesenian')
    
@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Kesenian</a></div>
  <div class="breadcrumb-item active">Data Kesenian</div>
@endsection

@section('section-title', 'Kesenian')
    
@section('section-lead')
  Berikut ini adalah daftar seluruh kesenian.
@endsection

@section('content')

  @component('components.datatables')
    
    @slot('table_id', 'room-table')

    @slot('table_header')
      <tr>
        <th>#</th>
        <th>Foto</th>
        <th>Video</th>
        <th>Nama</th>
        <th>Paket</th>
        <th>Harga</th>
        <th>Anggota</th>
        <th>Deskripsi</th>
      </tr> 
    @endslot
      
  @endcomponent

@endsection

@push('after-script')

  <script>
  $(document).ready(function() {
    $('#room-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{{ route('kesenian-list.json') }}',
      order: [2, 'asc'],
      columns: [
      {
        name: 'DT_RowIndex',
        data: 'DT_RowIndex',
        orderable: false, 
        searchable: false
      },
      {
        name: 'foto',
        data: 'foto',
        orderable: false, 
        searchable: false,
        render: function ( data, type, row ) {
          if(data != null) {
            return `<div class="gallery gallery-fw">`
              + `<a href="{{ asset('storage/${data}') }}" data-toggle="lightbox">`
                + `<img src="{{ asset('storage/${data}') }}" class="img-fluid" style="min-width: 80px; height: auto;">`
              + `</a>`
            + '</div>';
          } else {
            return '-'
          }
        }
      },
      {
                name: 'video',
                data: 'video',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    if (data != null) {
                        return `<video width="320" height="240" controls>`
                            + `<source src="{{ asset('storage/${data}') }}" type="video/mp4">`
                            + `Your browser does not support the video tag.`
                        + `</video>`;
                    } else {
                        return '-';
                    }
                }
            },
      {
        name: 'nama',
        data: 'nama',
      },
      {
        name: 'paket',
        data: 'paket',
      },
      {
        name: 'anggota',
        data: 'anggota',
      },
      {
        name: 'harga',
        data: 'harga',
      },
      {
        name: 'deskripsi',
        data: 'deskripsi',
      },
    ],
    });

    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });
  });

</script>

@include('includes.lightbox')

@endpush