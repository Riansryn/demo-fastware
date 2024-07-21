@extends('layout')

@section('content')
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        .form-section {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .form-section .form-group {
            flex: 1 1 15%;
            /* Adjust this value to control the width of each item */
            margin-right: 2px;
            margin-bottom: 15px;
        }

        .form-section label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        .add-column-button {
            margin-top: 15px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Halaman Inquiry</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active"><a href="{{ route('createinquiry') }}">Menu Inquiry Sales</a></li>
                    <li class="breadcrumb-item active">Formulir Inquiry Sales</li>
                </ol>
            </nav>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tampilan Formulir Inquiry Sales</h5>
                    <div class="form-section">
                        <div class="form-group">
                            <label>Kode Inquiry:</label>
                            <div class="form-value">{{ $inquiry->kode_inquiry }}</div>
                        </div>
                        <div class="form-group">
                            <label>Order From:</label>
                            <div class="form-value">{{ $inquiry->customer ? $inquiry->customer->name_customer : 'N/A' }}</div>
                        </div>
                        <div class="form-group">
                            <label>Create By:</label>
                            <div class="form-value">{{ $inquiry->create_by }}</div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 30px;">No</th>
                                    <th style="width: 150px;">Nama Material</th>
                                    <th style="width: 50px;">Jenis</th>
                                    <th style="width: 50px;">Thickness</th>
                                    <th style="width: 50px;">Weight</th>
                                    <th style="width: 50px;">Inner Diameter</th>
                                    <th style="width: 50px;">Outer Diameter</th>
                                    <th style="width: 50px;">Length</th>
                                    <th style="width: 50px;">Pcs</th>
                                    <th style="width: 50px;">Qty</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                @forelse ($materials as $index => $material)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $material->type_materials ? $material->type_materials->type_name : 'N/A' }}</td> <!-- Menampilkan type_name -->
                                        <td>{{ $material['jenis'] }}</td>
                                        <td>{{ $material['thickness'] }}</td>
                                        <td>{{ $material['weight'] }}</td>
                                        <td>{{ $material['inner_diameter'] }}</td>
                                        <td>{{ $material['outer_diameter'] }}</td>
                                        <td>{{ $material['length'] }}</td>
                                        <td>{{ $material['pcs'] }}</td>
                                        <td>{{ $material['qty'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" style="text-align: center;">Data tidak ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <a href="{{ route('formulirInquiry', $inquiry->id) }}"
                        class="btn btn-secondary add-row-button">Kembali</a>
                    <a href="{{ route('createinquiry') }}" class="btn btn-primary delete-row-button">Finish</a>
                </div>
            </div>
        </section>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <!-- excel -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    </main><!-- End #main -->
@endsection
