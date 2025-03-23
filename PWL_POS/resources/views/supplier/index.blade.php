    @extends('layouts.template')

    @section('content')
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ $page->title }}</h3>
                <div class="card-tools">
                    <a href="{{ url('supplier/create') }}" class="btn btn-sm btn-primary mt-1">Tambah</a>
                    <button onclick="modalAction('{{ url('/supplier/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Ajax</button>
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <table class="table table-bordered table-striped table-hover table-sm" id="table_supplier">
                    <thead>
                        <tr>
                            <th>ID </th>
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
    function modalAction(url = ''){ 
    $('#myModal').load(url,function(){ 
        $('#myModal').modal('show'); 
    }); 
}
    $(document).ready(function() {
        $('#table_supplier').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: "{{ url('supplier/list') }}",
                type: "POST",
                dataType: "json",
                data: {
                    _token: "{{ csrf_token() }}" // Hanya mengirim CSRF token
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
    });
        </script>
    @endpush
