@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/supplier/import') }}')" class="btn btn-info">Import Supplier</button>
                <a href="{{ url('/supplier/export_excel') }}" class="btn btn-primary">
                    <i class="fa fa-file-excel"></i> Export Supplier Excel </a>    
                <a href="{{ url('/supplier/export_pdf') }}" class="btn btn-warning">
                    <i class="fa fa-file-pdf"></i> Export Supplier PDF </a>  
                <button onclick="modalAction('{{ url('/supplier/create_ajax') }}')" class="btn btn-success">Tambah Data (Ajax)</button>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Opsional: Filter Supplier --}}
            {{-- <div class="form-group">
                <label for="supplier_id">Filter Supplier</label>
                <select id="supplier_id" class="form-control">
                    <option value="">-- Semua --</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->supplier_nama }}</option>
                    @endforeach
                </select>
            </div> --}}

            <table class="table table-bordered table-striped table-hover table-sm" id="table_supplier">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Kontak</th>
                        <th>Email</th>
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
    function modalAction(url = '') {
        $('#myModal').load(url, function () {
            $('#myModal').modal('show');
        });
    }

    $(document).ready(function() {
        var dataSupplier = $('#table_supplier').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: "{{ url('supplier/list') }}",
                type: "POST",
                dataType: "json",
                data: function(d) {
                    d.supplier_id = $('#supplier_id').val(); // jika filter digunakan
                    d._token = "{{ csrf_token() }}";
                }
            },
            columns: [
                { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
                { data: "supplier_nama", orderable: true, searchable: true },
                { data: "supplier_alamat", orderable: true, searchable: true },
                { data: "supplier_kontak", orderable: true, searchable: true },
                { data: "supplier_email", orderable: false, searchable: true },
                { data: "aksi", orderable: false, searchable: false }
            ]
        });

        // Reload data jika select digunakan
        $('#supplier_id').change(function() {
            dataSupplier.ajax.reload();
        });
    });
</script>
@endpush
