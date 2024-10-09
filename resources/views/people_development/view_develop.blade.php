@extends('layout')

@section('content')
    <main id="main" class="main">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <style>
            .container {
                margin-top: 20px;
            }

            .card {
                background-color: #f8f9fa;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                width: 100%;
                /* Lebar card 100% */
                box-sizing: border-box;
                /* Memastikan padding tidak menambah lebar card */
            }


            .card-content {
                display: flex;
                flex-direction: column;
            }

            .form-group {
                margin-bottom: 10px;
            }

            label {
                font-weight: bold;
            }

            .styled-table {
                width: 100%;
                border-collapse: collapse;
                margin: 25px 0;
                font-size: 14px;
                text-align: left;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            }

            .styled-table thead tr {
                background-color: #1072a0;
                color: #ffffff;
                text-align: left;
            }

            .styled-table th,
            .styled-table td {
                padding: 12px 15px;
            }

            .styled-table tbody tr {
                border-bottom: 1px solid #dddddd;
            }

            .styled-table tbody tr:nth-of-type(even) {
                background-color: #f3f3f3;
            }

            .styled-table tbody tr:last-of-type {
                border-bottom: 2px solid #009879;
            }

            .styled-table tbody tr.active-row {
                font-weight: bold;
                color: #009879;
            }
        </style>
        <div class="pagetitle">
            <h1>Halaman View Form Training Dept. Head</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Menu View Form Training</li>
                </ol>
            </nav>
        </div>
        <section class="section">
            <div class="container">
                <!-- Card untuk Departemen dan Nama -->
                <div class="card">
                    <div class="card-content">
                        <div class="form-group">
                            <label for="name">PIC: <span
                                    id="name">{{ $data->first()->modified_at ?? 'N/A' }}</span></label>
                        </div>
                    </div>
                    <!-- Tabel di dalam card -->
                    <!-- Card container -->
                    <div class="card"
                        style="padding: 20px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); border-radius: 10px; background-color: #fff;">
                        <!-- Tabel di dalam card -->
                        <div class="table-container" style="overflow-x:auto; margin-bottom: 20px;">
                            <table class="styled-table" style="width:100%; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th scope="col" rowspan="2">NO</th>
                                        <th scope="col" rowspan="2">Section</th>
                                        <th scope="col" rowspan="2">Job Position</th>
                                        <th scope="col" rowspan="2">Nama Karyawan</th>
                                        <th scope="col" rowspan="2">Program Training</th>
                                        <th scope="col" rowspan="2">Kategori Competency</th>
                                        <th scope="col" rowspan="2">Competency</th>
                                        <th scope="col" rowspan="2">Due Date</th>
                                        <th scope="col" rowspan="2">Budget</th>
                                        <th scope="col" rowspan="2">Lembaga</th>
                                        <th scope="col" rowspan="2">Keterangan Tujuan</th>
                                    </tr>
                                    <tr style="background-color: #f0ad4e;">
                                        <th scope="col">Nama Program </th>
                                        <th scope="col">Date Actual</th>
                                        <th scope="col">Lembaga </th>
                                        <th scope="col">Keterangan </th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="table-body">
                                    @foreach ($data as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->section}}</td>
                                            <td>{{ $item->id_job_position }}</td>
                                            <td>{{ $item->user->name ?? 'N/A' }}</td>
                                            <td>{{ $item->program_training }}</td>
                                            <td>{{ $item->kategori_competency }}</td>
                                            <td>{{ $item->competency }}</td>
                                            <td>{{ $item->due_date }}</td>
                                            <td>{{ number_format($item->biaya, 0, ',', '.') }}</td>
                                            <td>{{ $item->lembaga }}</td>
                                            <td>{{ $item->keterangan_tujuan }}</td>
                                            <td>{{ $item->program_training_plan }}</td>
                                            <td>{{ $item->due_date_plan }}</td>
                                            <td>{{ $item->lembaga_plan }}</td>
                                            <td>{{ $item->keterangan_plan }}</td>
                                            <td>
                                                @php
                                                    $statusColor = '';
                                                    switch ($item->status_2) {
                                                        case 'Mencari Vendor':
                                                            $statusColor = 'background-color: blue; color: white;';
                                                            break;
                                                        case 'Proses Pendaftaran':
                                                            $statusColor = 'background-color: orange; color: white;';
                                                            break;
                                                        case 'On Progress':
                                                            $statusColor = 'background-color: yellow; color: black;';
                                                            break;
                                                        case 'Done':
                                                            $statusColor = 'background-color: green; color: white;';
                                                            break;
                                                        case 'Pending':
                                                            $statusColor = 'background-color: gray; color: white;';
                                                            break;
                                                        case 'Ditolak':
                                                            $statusColor = 'background-color: red; color: white;';
                                                            break;
                                                    }
                                                @endphp
                                                <span
                                                    style="display: inline-block; padding: 5px 10px; border-radius: 5px; {{ $statusColor }}">
                                                    {{ $item->status_2 }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    @php
                                        // Hitung total budget, total biaya actual, dan selisih biaya
                                        $totalBudget = $data->sum('biaya');
                                    @endphp
                                    <tr>
                                        <th colspan="9" style="text-align:right;">Total Budget: Rp
                                            {{ number_format($totalBudget, 0, ',', '.') }}</th>

                                        <th colspan="1"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- Legend section -->
                        <div style="margin-top: 20px;">
                            <strong>Keterangan Status:</strong>
                            <ul
                                style="list-style-type: none; padding-left: 0; margin-top: 10px; display: flex; gap: 15px; align-items: center;">
                                <li style="display: flex; align-items: center;">
                                    <span
                                        style="background-color: blue; color: white; padding: 5px 15px; border-radius: 5px; margin-right: 5px;"><b>Biru</b></span>
                                    - Mencari Vendor
                                </li>
                                <li style="display: flex; align-items: center;">
                                    <span
                                        style="background-color: orange; color: white; padding: 5px 15px; border-radius: 5px; margin-right: 5px;"><b>Orange</b></span>
                                    - Proses Pendaftaran
                                </li>
                                <li style="display: flex; align-items: center;">
                                    <span
                                        style="background-color: yellow; color: black; padding: 5px 15px; border-radius: 5px; margin-right: 5px;"><b>Kuning</b></span>
                                    - On Progress
                                </li>
                                <li style="display: flex; align-items: center;">
                                    <span
                                        style="background-color: green; color: white; padding: 5px 15px; border-radius: 5px; margin-right: 5px;"><b>Hijau</b></span>
                                    - Done
                                </li>
                                <li style="display: flex; align-items: center;">
                                    <span
                                        style="background-color: rgb(154, 150, 150); color: rgb(251, 251, 251); padding: 5px 15px; border-radius: 5px; margin-right: 5px;"><b>Abu</b></span>
                                    - Pending
                                </li>
                                <li style="display: flex; align-items: center;">
                                    <span
                                        style="background-color: red; color: white; padding: 5px 15px; border-radius: 5px; margin-right: 5px;"><b>Merah</b></span>
                                    - Ditolak
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div style="margin-top: 3%">
                        <a href="{{ route('indexPD') }}" class="btn btn-secondary">Close</a>
                    </div>
                </div>
            </div>
        </section>
        <!-- jQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        {{-- excel --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

        <!-- SimpleDataTables JS -->
        <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
        <script></script>
    </main><!-- End #main -->
@endsection
