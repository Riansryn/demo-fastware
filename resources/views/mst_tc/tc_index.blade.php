@extends('layout')

@section('content')
    <main id="main" class="main">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="pagetitle">
            <h1>Halaman Pengajuan Data Competency</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Menu List Competency</li>
                </ol>
            </nav>
        </div>
        <section class="section">
            <div class="container">
                <a href="{{ route('tcCreate') }}" class="btn btn-primary">Tambah Data</a>
                <h4 style="margin-top: 3%"> <b>Table Data Technical Competency</b></h4>
                <table class="datatable table">
                    <thead>
                        <tr>
                            <th scope="col">NO</th>
                            <th scope="col">Job Position</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($technicalData as $index => $data)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $data->jobPosition->job_position }}</td>
                                <td>
                                    <a href="{{ route('mst_tc.edit', $data->id) }}" class="btn btn-warning">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <h4 style="margin-top: 3%"> <b>Table Data Soft Skills</b></h4>
                <table class="datatable table">
                    <thead>
                        <tr>
                            <th scope="col">NO</th>
                            <th scope="col">Job Position</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($softSkillsData as $index => $data)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $data->jobPosition->job_position }}</td>
                                <td>
                                    <a href="{{ route('mst_sk.editSoftSKills', $data->id) }}" class="btn btn-warning">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <h4 style="margin-top: 3%"> <b>Table Data Additional</b></h4>
                <table class="datatable table">
                    <thead>
                        <tr>
                            <th scope="col">NO</th>
                            <th scope="col">Job Position</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($additionalData as $index => $data)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $data->jobPosition->job_position }}</td>
                                <td>
                                    <a href="{{ route('mst_ad.editAdditionals', $data->id) }}" class="btn btn-warning">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <!-- jQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- SimpleDataTables JS -->
        <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>

    </main><!-- End #main -->
@endsection
