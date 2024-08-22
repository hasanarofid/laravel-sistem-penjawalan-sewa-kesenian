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
    render: function (data, type, row) {
        if (data && Array.isArray(data)) {
            var galleryHtml = `<div id="carousel-${row.id}" class="carousel slide" data-ride="carousel">`;

            // Indicators
            galleryHtml += '<ol class="carousel-indicators">';
            data.forEach(function (foto, index) {
                galleryHtml += `<li data-target="#carousel-${row.id}" data-slide-to="${index}" class="${index === 0 ? 'active' : ''}"></li>`;
            });
            galleryHtml += '</ol>';

            // Slides
            galleryHtml += '<div class="carousel-inner">';
            data.forEach(function (foto, index) {
                galleryHtml += `<div class="carousel-item ${index === 0 ? 'active' : ''}">`
                    + `<a href="{{ asset('storage/uploads/${foto}') }}" data-toggle="lightbox">`
                    + `<img src="{{ asset('storage/uploads/${foto}') }}" class="d-block " style="height: 200px;width:200px">`
                    + `</a>`
                    + `</div>`;
            });
            galleryHtml += '</div>';

            // Controls
            galleryHtml += `<a class="carousel-control-prev" href="#carousel-${row.id}" role="button" data-slide="prev">`
                + `<span class="carousel-control-prev-icon" aria-hidden="true"></span>`
                + `<span class="sr-only">Previous</span>`
                + `</a>`
                + `<a class="carousel-control-next" href="#carousel-${row.id}" role="button" data-slide="next">`
                + `<span class="carousel-control-next-icon" aria-hidden="true"></span>`
                + `<span class="sr-only">Next</span>`
                + `</a>`;

            galleryHtml += '</div>'; // Close carousel div

            return galleryHtml;
        } else {
            return '-';
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