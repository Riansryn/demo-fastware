@extends('layout')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<main id="main" class="main">

    <div class="pagetitle">
        <h1>Form Elements</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item">Forms</li>
                <li class="breadcrumb-item active">Edit Perbaikan</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Form Edit Perbaikan {{$mesin->nama_mesin}}</h5>
                            <form id="PerbaikanForm" action="{{ route('detailpreventives.updatePerbaikan', $mesin->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <!-- Input perbaikan -->
                                <div id="input-container">
                                    <!-- Input awal perbaikan -->
                                    @foreach ($perbaikans as $key => $perbaikan)
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <input type="checkbox" id="perbaikan_{{ $key }}" name="checked[]" value="{{ $key }}" @if($checkedPerbaikans[$key]===1 ) checked @endif disabled>
                                            </span>
                                            <input type="text" class="form-control" name="perbaikan[]" value="{{ $perbaikan }}">
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</main>

@endsection
