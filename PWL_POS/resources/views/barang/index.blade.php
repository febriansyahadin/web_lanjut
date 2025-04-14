@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <button onclick="modalAction('{{ url('/barang/import') }}')" class="btn btn-info">Import Barang</button>
            <a href="{{ url('/barang/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Barang</a>
            <a href="{{ url('/barang/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export Barang PDF</a>
             <button onclick="modalAction('{{ url('/barang/create_ajax') }}')" class="btn btn-success">Tambah Data (Ajax)</button>
           
        </div>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        
        <div class="row">
            <div class="col-md-12">
                <div class="form-group row">
                    <label class="col-1 control-label col-form-label">Filter:</label>
                    <div class="col-3">
                        <select class="form-control" id="kategori_id" name="kategori_id">
                            <option value="">- Semua -</option>
                            @foreach($kategori as $item)
                                <option value="{{ $item->kategori_id }}">{{ $item->kategori_nama }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Kategori Barang</small>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped table-hover table-sm" id="table_barang">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Harga Jual</th>
                    <th>Harga Beli</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection
@push('css')
@endpush
@push('js')
<script>
    function modalAction(url = ''){
        $('#myModal').load(url, function(){
            $('#myModal').modal('show');
        });
    }

    $(function () {
        var table = $('#table_barang').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('barang/list') }}",
                data: function (d) {
                    d.kategori_id = $('#kategori_id').val();
                }
            },
            columns: [
                {data: 'barang_id', name: 'barang_id', className: "text-center", width: "5%"},
                {data: 'barang_kode', name: 'barang_kode', className: "text-center", width: "15%"},
                {data: 'barang_nama', name: 'barang_nama',width: "20%"},
                {data: 'harga_jual', name: 'harga_jual', className: "text-right", width: "15%"},
                {data: 'harga_beli', name: 'harga_beli', className: "text-right", width: "15%"},
                {data: "kategori.kategori_nama", className: "text-center", width: "10%", orderable: true,searchable: false},
                {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
            ]
        });
        $('#table_barang_filter input').on('keyup', function(e) {
            if (e.keyCode == 13) { // enter key
                table.draw();
            }
        });

        $('#kategori_id').on('change', function() {
            table.draw(); 
        });
    });

    $('#myModal').on('show.bs.modal', function () {
        console.log('Modal is about to be shown');
    });
</script>
@endpush