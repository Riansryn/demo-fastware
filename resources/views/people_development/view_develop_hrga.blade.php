@extends('layout')

@section('content')
    <main id="main" class="main">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <style>
            .container {
                margin-top: 20px;
                /* Remove or adjust margin as needed */
            }

            .card {
                background-color: #f8f9fa;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                width: 110%;
                box-sizing: border-box;
            }

            .form-group {
                margin-bottom: 10px;
            }

            label {
                font-weight: bold;
            }

            .styled-table {
                width: 200%;
                border-collapse: collapse;
                margin: 25px 0;
                font-size: 14px;
                text-align: center;
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
            <h1>Halaman Form View Data Training HRGA</h1>
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
                    <!-- Tabel di dalam card -->
                    <div class="table-container" style="overflow-x:auto;">
                        <table id="table" class="styled-table" style="width:100%;">
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
                                    <th scope="col">Nama Program</th>
                                    <th scope="col">Date Actual</th>
                                    <th scope="col">Biaya Actual</th>
                                    <th scope="col">Lembaga</th>
                                    <th scope="col">Keterangan</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->section }}</td>
                                        <td>{{ $item->id_job_position }}</td>
                                        <td>{{ $item->user->name ?? 'N/A' }}</td>
                                        <td>{{ $item->program_training }}</td>
                                        <td>{{ $item->kategori_competency }}</td>
                                        <td>{{ $item->competency }}</td>
                                        <td>{{ $item->due_date }}</td>
                                        <td>{{ 'Rp ' . number_format($item->biaya, 0, ',', '.') }}</td>
                                        <td>{{ $item->lembaga }}</td>
                                        <td>{{ $item->keterangan_tujuan }}</td>
                                        <td>{{ $item->program_training_plan }}</td>
                                        <td>{{ $item->due_date_plan }}</td>
                                        <td>{{ 'Rp ' . number_format($item->biaya_plan, 0, ',', '.') }}</td>
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
                                    $totalBiayaPlan = $data->sum('biaya_plan');
                                    $selisihBiaya = $totalBudget - $totalBiayaPlan;
                                @endphp
                                <tr>
                                    <th colspan="9" style="text-align:right;">Total Budget: Rp
                                        {{ number_format($totalBudget, 0, ',', '.') }}</th>

                                    <th colspan="5" style="text-align:right;">Total Biaya Actual: Rp
                                        {{ number_format($totalBiayaPlan, 0, ',', '.') }}</th>
                                    <th colspan="2"></th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th colspan="13" style="text-align:right;">Selisih Biaya: Rp
                                        {{ number_format($selisihBiaya, 0, ',', '.') }}</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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
                    <div style="margin-top: 3%">
                        <a href="{{ route('indexPD2') }}" class="btn btn-secondary">Close</a>
                        <button onclick="exportTableToPDF()" class="btn btn-primary"><i
                                class="bi bi-printer-fill"></i>Export PDF</button>
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

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script>
            function exportTableToPDF() {
                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF({
                    orientation: 'landscape',
                    unit: 'mm',
                    format: 'a4' // Menggunakan ukuran kertas A4
                });

                const table = document.getElementById('table');
                const rows = table.querySelectorAll('tbody tr');

                const pageWidth = doc.internal.pageSize.getWidth();
                const pageHeight = doc.internal.pageSize.getHeight();

                let startY = 20;
                const startX = 10;
                const cellPadding = 2;
                const cellHeight = 8; // Tinggi sel yang lebih kecil agar lebih banyak data muat
                const headerHeight = 10;
                const rowHeight = cellHeight + cellPadding;
                const fontSize = 7; // Ukuran font yang lebih kecil

                // Header tabel
                const headers = [
                    "NO", "Section", "Job Position", "Nama Karyawan", "Program Training",
                    "Kategori Competency", "Competency", "Due Date", "Budget", "Lembaga", "Keterangan Tujuan"
                ];

                // Lebar kolom diatur ulang agar lebih sesuai dengan ukuran A4
                const colWidths = [10, 25, 30, 30, 30, 30, 30, 20, 20, 25, 35];

                doc.setFontSize(10);
                doc.setFont("helvetica", "bold");
                doc.text('Training Competency Report', pageWidth / 2, 10, {
                    align: 'center'
                });

                doc.setFontSize(fontSize);
                let currentX = startX;
                headers.forEach((header, index) => {
                    doc.setFont("helvetica", "bold");
                    doc.rect(currentX, startY, colWidths[index], headerHeight); // Membuat header cell
                    doc.text(header, currentX + cellPadding, startY + headerHeight / 2 + 2); // Adjust text position
                    currentX += colWidths[index];
                });

                startY += headerHeight;

                let totalBudget = 0; // Inisialisasi total budget

                rows.forEach((row, rowIndex) => {
                    const cells = row.querySelectorAll('td');
                    currentX = startX;

                    const selectedCells = [
                        cells[0], cells[1], cells[2], cells[3], cells[4],
                        cells[5], cells[6], cells[7], cells[8], cells[9], cells[10]
                    ];

                    selectedCells.forEach((cell, cellIndex) => {
                        const cellText = cell?.innerText || '';
                        doc.setFont("helvetica", "normal");

                        const truncatedText = cellText.length > 20 ? cellText.substring(0, 18) + '...' :
                            cellText;

                        doc.rect(currentX, startY + rowHeight * rowIndex, colWidths[cellIndex],
                        cellHeight); // Membuat body cell
                        doc.text(truncatedText, currentX + cellPadding, startY + rowHeight * rowIndex +
                            cellHeight / 2 + 2);
                        currentX += colWidths[cellIndex];

                        // Menghitung total budget dari kolom Budget (index ke-8)
                        if (cellIndex === 8) {
                            const budgetValue = parseFloat(cellText.replace(/[^\d]/g,
                            '')); // Mengambil nilai numerik
                            if (!isNaN(budgetValue)) {
                                totalBudget += budgetValue;
                            }
                        }
                    });

                    // Jika melebihi halaman, tambahkan halaman baru
                    if (startY + rowHeight * (rowIndex + 1) > pageHeight - 50) { // Batas bawah halaman
                        doc.addPage('landscape', 'a4');
                        startY = 20;
                        currentX = startX;

                        // Gambarkan ulang header di halaman baru
                        headers.forEach((header, index) => {
                            doc.setFont("helvetica", "bold");
                            doc.rect(currentX, startY, colWidths[index], headerHeight); // Membuat header cell
                            doc.text(header, currentX + cellPadding, startY + headerHeight / 2 + 2);
                            currentX += colWidths[index];
                        });
                        startY += headerHeight;
                    }
                });

                // Tambahkan total budget di bawah tabel
                const totalBudgetText = `Total Budget: Rp ${totalBudget.toLocaleString('id-ID')}`;
                startY += rowHeight;
                doc.setFont("helvetica", "bold");

                const totalBudgetX = pageWidth - 100;
                doc.text(totalBudgetText, totalBudgetX, pageHeight - 77);

                // Tambahkan section tanda tangan
                const currentDate = new Date();
                const monthNames = [
                    "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                ];
                const formattedDate =
                    `${currentDate.getDate()} ${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;

                const signatureX = pageWidth - 140;
                const signatureY = pageHeight - 66;

                doc.setFontSize(10);
                doc.setFont("helvetica", "normal");
                doc.text(`Cikarang, ${formattedDate}`, pageWidth - 70, signatureY);

                doc.text('Dibuat', signatureX, signatureY + 10);
                doc.text('Diperiksa', signatureX + 50, signatureY + 10);
                doc.text('Disetujui', signatureX + 100, signatureY + 10);

                doc.save('table-export-a4.pdf');
            }
        </script>

    </main><!-- End #main -->
@endsection
