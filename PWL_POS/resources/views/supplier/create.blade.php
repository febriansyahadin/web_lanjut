@extends('layouts.template')

@section('content')
  <div class="card card-outline card-primary">
    <div class="card-header">
      <h3 class="card-title">{{ $page->title }}</h3>
      <div class="card-tools"></div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('supplier') }}" class="form-horizontal"> 
        @csrf
        
        <div class="form-group row">
          <label class="col-2 control-label col-form-label">Nama Supplier</label>
          <div class="col-10">
            <input type="text" class="form-control" id="supplier_nama" name="supplier_nama" value="{{ old('supplier_nama') }}" required>
            @error('supplier_nama')
              <small class="form-text text-danger">{{ $message }}</small>
            @enderror
          </div>
        </div>
        <div class="form-group row">
          <label class="col-2 control-label col-form-label">Alamat Supplier</label>
          <div class="col-10">
            <input type="text" class="form-control" id="supplier_alamat" name="supplier_alamat" value="{{ old('supplier_alamat') }}" required>
            @error('supplier_alamat')
              <small class="form-text text-danger">{{ $message }}</small>
            @enderror
          </div>
        </div>
        <div class="form-group row">
            <label class="col-2 control-label col-form-label">Kontak Supplier</label>
            <div class="col-10">
              <input type="text" class="form-control" id="supplier_kontak" name="supplier_kontak" value="{{ old('supplier_kontak') }}" required>
              @error('supplier_kontak')
                <small class="form-text text-danger">{{ $message }}</small>
              @enderror
            </div>
          </div>
          <div class="form-group row">
            <label class="col-2 control-label col-form-label">Email Supplier</label>
            <div class="col-10">
              <input type="text" class="form-control" id="supplier_email" name="supplier_email" value="{{ old('supplier_email') }}" required>
              @error('supplier_email')
                <small class="form-text text-danger">{{ $message }}</small>
              @enderror
            </div>
          </div>
        <div class="form-group row">
          <label class="col-2 control-label col-form-label"></label>
          <div class="col-10">
            <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
            <a class="btn btn-sm btn-default ml-1" href="{{ url('supplier') }}">Kembali</a>
          </div>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('css')
@endpush
@push('js')
@endpush
