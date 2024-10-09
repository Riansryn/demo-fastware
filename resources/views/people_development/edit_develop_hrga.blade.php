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

            select.status-dropdown {
                color: white;
                /* Default text color */
            }
        </style>
        <div class="pagetitle">
            <h1>Halaman Tindak Lanjut Training</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Menu Tindak Lanjut Training</li>
                </ol>
            </nav>
        </div>
        <section class="section">
            <!-- Card untuk Departemen dan Nama -->
            <div class="card">
                <!-- Tabel untuk edit data -->
                <form id="trainingForm" method="POST" enctype="multipart/form-data" action="{{ route('updatePdPlan') }}">
                    @csrf
                    @method('PUT')
                    <div class="table-container" style="overflow-x:auto;">
                        <p style="color: red">*scroll kesamping dalam pengisian data</p>

                        <table class="styled-table" style="width:100%;">
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
                                    <th scope="col">Upload File</th>
                                    <th scope="col">View File</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                <!-- Baris data akan diisi di sini -->
                            </tbody>
                            <tfoot>
                                @php
                                    // Hitung total budget, total biaya actual, dan selisih biaya
                                    $totalBudget = $data->sum('biaya');
                                    $totalBiayaPlan = $data->sum('biaya_plan');
                                    $selisihBiaya = $totalBudget - $totalBiayaPlan;
                                @endphp
                                <tr>
                                    <th></th>
                                    <th colspan="8" style="text-align:right;">Total Budget: Rp
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
                    <div class="mt-3 mb-4">
                        <button type="button" id="addRowBtn" class="btn btn-success">Add Row</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{ route('indexPD2') }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>
                <div style="margin-top: 20px;">
                    <strong>Keterangan Status:</strong>
                    <ul
                        style="list-style-type: none; padding-left: 0; margin-top: 10px; display: flex; flex-direction: column; gap: 15px;">
                        <li style="display: flex; align-items: center; justify-content: space-between;">
                            <span style="display: flex; align-items: center;">
                                <span
                                    style="background-color: blue; color: white; padding: 5px 15px; border-radius: 5px; margin-right: 5px;">
                                    <b>Biru</b>
                                </span> - Mencari Vendor =
                                <span id="status-blue-percentage">0% (0 dari 0)</span>
                            </span>

                        </li>
                        <li style="display: flex; align-items: center; justify-content: space-between;">
                            <span style="display: flex; align-items: center;">
                                <span
                                    style="background-color: orange; color: white; padding: 5px 15px; border-radius: 5px; margin-right: 5px;">
                                    <b>Orange</b>
                                </span> - Proses Pendaftaran = <span id="status-orange-percentage">0% (0 dari 0)</span>
                            </span>

                        </li>
                        <li style="display: flex; align-items: center; justify-content: space-between;">
                            <span style="display: flex; align-items: center;">
                                <span
                                    style="background-color: yellow; color: black; padding: 5px 15px; border-radius: 5px; margin-right: 5px;">
                                    <b>Kuning</b>
                                </span> - On Progress = <span id="status-yellow-percentage">0% (0 dari 0)</span>
                            </span>
                        </li>
                        <li style="display: flex; align-items: center; justify-content: space-between;">
                            <span style="display: flex; align-items: center;">
                                <span
                                    style="background-color: green; color: white; padding: 5px 15px; border-radius: 5px; margin-right: 5px;">
                                    <b>Hijau</b>
                                </span> - Done = <span id="status-green-percentage">0% (0 dari 0)</span>
                            </span>

                        </li>
                        <li style="display: flex; align-items: center; justify-content: space-between;">
                            <span style="display: flex; align-items: center;">
                                <span
                                    style="background-color: rgb(154, 150, 150); color: rgb(251, 251, 251); padding: 5px 15px; border-radius: 5px; margin-right: 5px;">
                                    <b>Abu</b>
                                </span> - Pending = <span id="status-gray-percentage">0% (0 dari 0)</span>
                            </span>

                        </li>
                        <li style="display: flex; align-items: center; justify-content: space-between;">
                            <span style="display: flex; align-items: center;">
                                <span
                                    style="background-color: red; color: white; padding: 5px 15px; border-radius: 5px; margin-right: 5px;">
                                    <b>Merah</b>
                                </span> - Ditolak = <span id="status-red-percentage">0% (0 dari 0)</span>
                            </span>

                        </li>
                    </ul>
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

        <script>
            var existingData = @json($data);
            var jobPositions = @json($jobPositions);

            document.addEventListener('DOMContentLoaded', function() {
                var tableBody = document.getElementById('table-body');

                var statusPercentageDivs = {
                    'Mencari Vendor': document.getElementById('status-blue-percentage'),
                    'Proses Pendaftaran': document.getElementById('status-orange-percentage'),
                    'On Progress': document.getElementById('status-yellow-percentage'),
                    'Done': document.getElementById('status-green-percentage'),
                    'Pending': document.getElementById('status-gray-percentage'),
                    'Ditolak': document.getElementById('status-red-percentage')
                };

                function updateStatusPercentage() {
                    var totalRows = tableBody.querySelectorAll('tr').length;
                    if (totalRows === 0) {
                        Object.values(statusPercentageDivs).forEach(function(div) {
                            div.innerText = '0% (0 dari 0)';
                        });
                        return;
                    }

                    var statusCounts = {
                        'Mencari Vendor': 0,
                        'Proses Pendaftaran': 0,
                        'On Progress': 0,
                        'Done': 0,
                        'Pending': 0,
                        'Ditolak': 0
                    };

                    tableBody.querySelectorAll('tr').forEach(function(row) {
                        var statusDropdown = row.querySelector('.status-dropdown');
                        if (statusDropdown) {
                            var status = statusDropdown.value;
                            if (statusCounts[status] !== undefined) {
                                statusCounts[status]++;
                            }
                        }
                    });

                    // Calculate and update the percentage and count for each status
                    for (var status in statusCounts) {
                        var percentage = (statusCounts[status] / totalRows * 100).toFixed(2) + '%';
                        var countText = `${statusCounts[status]} dari ${totalRows}`;
                        if (statusPercentageDivs[status]) {
                            statusPercentageDivs[status].innerText = `${percentage} (${countText})`;
                        }
                    }
                }

                // Call this function whenever you need to update the percentages, e.g., after adding/removing rows
                updateStatusPercentage();

                existingData.forEach(function(item, index) {
                    var newRow = document.createElement('tr');
                    newRow.id = `row-${item.id}`; // Set a unique ID for each row

                    var userOptions = '<option value="">---- Pilih Karyawan ----</option>';
                    var competencyOptions = '<option value="">---- Pilih Competency ----</option>';

                    if (item.user) {
                        userOptions += `<option value="${item.user.id}" selected>${item.user.name}</option>`;
                    }

                    if (item.competency) {
                        competencyOptions +=
                            `<option value="${item.competency}" selected>${item.competency}</option>`;
                    }

                    updateStatusPercentage();

                    newRow.innerHTML = `
                        <td>${index + 1}</td>
                        <input type="hidden" id="modified_at" name="modified_at" value="${item.modified_at}" />
                        <input type="hidden" name="id[]" value="${item.id}" /> <!-- Hidden ID field -->
                        <td>
                            <select class="section-dropdown" name="section[]" disabled>
                                <option value="">---- Pilih Section ----</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section }}" ${item.section == '{{ $section }}' ? 'selected' : ''}>
                                        {{ $section }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="job-position-dropdown" name="id_job_position[]" disabled>
                                <option value="">---- Pilih Job Position ----</option>
                            </select>
                        </td>
                        <td>
                            <select class="user-dropdown" name="id_user[]" style="width: 250px" disabled>
                                ${userOptions}
                            </select>
                        </td>
                        <td><input type="text" id="program_training" name="program_training[]" value="${item.program_training}" placeholder="Program Training" disabled></td>
                        <td>
                            <select name="kategori_competency[]" class="competency-category-dropdown" disabled>
                                <option value="">---- Pilih Kategori Competency ----</option>
                                <option value="technical" ${item.kategori_competency == 'technical' ? 'selected' : ''}>Technical Competency</option>
                                <option value="nontechnical" ${item.kategori_competency == 'nontechnical' ? 'selected' : ''}>Non Technical</option>
                                <option value="additional" ${item.kategori_competency == 'additional' ? 'selected' : ''}>Additional</option>
                            </select>
                        </td>
                        <td>
                            <select name="competency[]" class="competency-dropdown" style="width: 300px" disabled>
                                ${competencyOptions}
                            </select>
                        </td>
                        <td><input type="date" id="due_date" name="due_date[]" value="${item.due_date}"></td>
                        <td><input type="text" id="biaya" name="biaya[]" value="${item.biaya || ''}" placeholder="Budget"></td>
                        <td><input type="text" id="lembaga" name="lembaga[]" value="${item.lembaga}" placeholder="Lembaga"></td>
                        <td><input type="text" id="keterangan_tujuan" name="keterangan_tujuan[]" value="${item.keterangan_tujuan}" placeholder="Keterangan Tujuan"></td>
                        <td><input type="text" id="program_training_plan" name="program_training_plan[]" value="${item.program_training_plan || ''}" placeholder="Nama Program Plan"></td>
                        <td><input type="date" id="due_date_plan" name="due_date_plan[]" value="${item.due_date_plan || ''}"></td>
                        <td><input type="text" id="biaya_plan" name="biaya_plan[]" value="${item.biaya_plan || ''}" placeholder="Biaya Plan"></td>
                        <td><input type="text" id="lembaga_plan" name="lembaga_plan[]" value="${item.lembaga_plan || ''}" placeholder="Lembaga Plan"></td>
                        <td><input type="text" id="keterangan_plan" name="keterangan_plan[]" value="${item.keterangan_plan || ''}" placeholder="Keterangan Plan"></td>
                        <td>
                            <select name="status_2[]" class="status-dropdown" onchange="updateDropdownColor(this); toggleFileUpload(this);" style="background-color: ${getStatusColor(item.status_2)};">
                                <option value=""> ----- Pilih Status -----</option>
                                <option value="Mencari Vendor" ${item.status_2 == 'Mencari Vendor' ? 'selected' : ''}>Mencari Vendor</option>
                                <option value="Proses Pendaftaran" ${item.status_2 == 'Proses Pendaftaran' ? 'selected' : ''}>Proses Pendaftaran</option>
                                <option value="On Progress" ${item.status_2 == 'On Progress' ? 'selected' : ''}>On progress</option>
                                <option value="Done" ${item.status_2 == 'Done' ? 'selected' : ''}>Done</option>
                                <option value="Pending" ${item.status_2 == 'Pending' ? 'selected' : ''}>Pending</option>
                                <option value="Ditolak" ${item.status_2 == 'Ditolak' ? 'selected' : ''}>Ditolak</option>
                            </select>
                        </td>
                        <td><input type="file" name="file[${item.id}]" accept=".pdf"></td>
                        <td>${item.file ? `<a href="#" class="btn btn-primary" onclick="downloadPdf(${item.id})"><i class="bi bi-filetype-pdf fs-6"></i></a>` : 'No file'}</td>
                    `;

                    tableBody.appendChild(newRow);

                    if (item.status_2 === 'Done') {
                        newRow.querySelector('input[type="file"]').removeAttribute('disabled');
                    }

                    var sectionDropdown = newRow.querySelector('.section-dropdown');
                    var jobPositionDropdown = newRow.querySelector('.job-position-dropdown');
                    var userDropdown = newRow.querySelector('.user-dropdown');
                    var competencyCategoryDropdown = newRow.querySelector('.competency-category-dropdown');
                    var competencyDropdown = newRow.querySelector('.competency-dropdown');

                    sectionDropdown.addEventListener('change', function() {
                        var selectedSection = this.value;
                        jobPositionDropdown.innerHTML =
                            '<option value="">---- Pilih Job Position ----</option>';
                        userDropdown.innerHTML = '<option value="">---- Pilih Karyawan ----</option>';

                        var addedUsers = [];

                        jobPositions.forEach(function(jobPosition) {
                            if (jobPosition.user && jobPosition.user.section ===
                                selectedSection) {
                                var jobOption = document.createElement('option');
                                jobOption.value = jobPosition.job_position;
                                jobOption.text = jobPosition.job_position;
                                jobPositionDropdown.appendChild(jobOption);

                                if (!addedUsers.includes(jobPosition.user.id)) {
                                    var userOption = document.createElement('option');
                                    userOption.value = jobPosition.user.id;
                                    userOption.text = jobPosition.user.name;
                                    userDropdown.appendChild(userOption);
                                    addedUsers.push(jobPosition.user.id);
                                }
                            }
                        });

                        if (item.id_job_position) {
                            jobPositionDropdown.value = item.id_job_position;
                        }

                        if (item.user) {
                            userDropdown.value = item.user.id;
                        }
                    });

                    // Memastikan data tersimpan ditampilkan ketika halaman dimuat
                    if (item.section) {
                        sectionDropdown.value = item.section;
                        sectionDropdown.dispatchEvent(new Event('change'));
                    }
                });

                document.getElementById('addRowBtn').addEventListener('click', function() {
                    var newRow = document.createElement('tr');

                    newRow.innerHTML = `
                            <td></td>
                            <input type="hidden" name="id[]" value="" />
                            <input type="hidden" name="modified_at[]" value="" />
                            <td>
                                <select class="section-dropdown" name="section[]">
                                    <option value="">---- Pilih Section ----</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section }}">{{ $section }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="job-position-dropdown" name="id_job_position[]">
                                    <option value="">---- Pilih Job Position ----</option>
                                </select>
                            </td>
                            <td>
                                <select class="user-dropdown" name="id_user[]" style="width: 250px">
                                    <option value="">---- Pilih Karyawan ----</option>
                                </select>
                            </td>
                            <td><input type="text" name="program_training[]" placeholder="Program Training" disabled></td>
                            <td>
                                <select name="kategori_competency[]" class="competency-category-dropdown" disabled>
                                    <option value="">---- Pilih Kategori Competency ----</option>
                                    <option value="technical">Technical Competency</option>
                                    <option value="nontechnical">Non Technical</option>
                                    <option value="additional">Additional</option>
                                </select>
                            </td>
                            <td>
                                <select name="competency[]" class="competency-dropdown" style="width: 300px" disabled>
                                    <!-- Option elements will be populated dynamically -->
                                </select>
                            </td>
                            <td><input type="date" name="due_date[]" placeholder="Due Date" disabled></td>
                            <td><input type="text" name="biaya[]" placeholder="Budget" disabled></td>
                            <td><input type="text" name="lembaga[]" placeholder="Lembaga" disabled></td>
                            <td><input type="text" name="keterangan_tujuan[]" placeholder="Keterangan Tujuan" disabled></td>
                            <td><input type="text" name="program_training_plan[]" placeholder="Nama Program Plan"></td>
                            <td><input type="date" name="due_date_plan[]"></td>
                            <td><input type="text" name="biaya_plan[]" placeholder="Biaya Plan"></td>
                            <td><input type="text" name="lembaga_plan[]" placeholder="Lembaga Plan"></td>
                            <td><input type="text" name="keterangan_plan[]" placeholder="Keterangan Plan"></td>
                            <td>
                                <select name="status_2[]" class="status-dropdown" onchange="updateDropdownColor(this); toggleFileUpload(this);">
                                    <option value=""> ----- Pilih Status -----</option>
                                    <option value="Mencari Vendor">Mencari Vendor</option>
                                    <option value="Proses Pendaftaran">Proses Pendaftaran</option>
                                    <option value="On Progress">On progress</option>
                                    <option value="Done">Done</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Ditolak">Ditolak</option>
                                </select>
                            </td>
                            <td><input type="file" name="file[]" accept=".pdf" disabled></td>
                            <td>No file</td>
                        `;

                    tableBody.appendChild(newRow);

                    var sectionDropdown = newRow.querySelector('.section-dropdown');
                    var jobPositionDropdown = newRow.querySelector('.job-position-dropdown');
                    var userDropdown = newRow.querySelector('.user-dropdown');

                    sectionDropdown.addEventListener('change', function() {
                        var selectedSection = this.value;
                        jobPositionDropdown.innerHTML =
                            '<option value="">---- Pilih Job Position ----</option>';
                        userDropdown.innerHTML = '<option value="">---- Pilih Karyawan ----</option>';

                        var addedUsers = [];

                        jobPositions.forEach(function(jobPosition) {
                            if (jobPosition.user.section === selectedSection) {
                                var jobOption = document.createElement('option');
                                jobOption.value = jobPosition.job_position;
                                jobOption.text = jobPosition.job_position;
                                jobPositionDropdown.appendChild(jobOption);

                                if (!addedUsers.includes(jobPosition.user.id)) {
                                    var userOption = document.createElement('option');
                                    userOption.value = jobPosition.user.id;
                                    userOption.text = jobPosition.user.name;
                                    userDropdown.appendChild(userOption);
                                    addedUsers.push(jobPosition.user.id);
                                }
                            }
                        });
                    });
                });
            });

            function downloadPdf(id) {
                var downloadPdfUrl = "{{ route('download.pdf', ['id' => ':id']) }}";
                var url = downloadPdfUrl.replace(':id', id);
                window.location.href = url; // Redirect to the download URL
            }

            $(document).ready(function() {
                $('#trainingForm').submit(function(event) {
                    event.preventDefault(); // Mencegah pengiriman form secara default

                    var formData = new FormData(this);

                    // Mengambil data dari setiap baris
                    $('#trainingForm tbody tr').each(function() {
                        var id = $(this).find('input[name="id[]"]')
                    .val(); // Mengecek ID untuk menentukan apakah data baru atau lama
                        var isNew = id === ""; // ID kosong berarti baris baru

                        // Mengambil file input tanpa tergantung pada atribut name
                        var fileInput = $(this).find('input[type="file"]');
                        var file = null;

                        if (fileInput.length > 0 && fileInput.prop('files') && fileInput.prop('files')
                            .length > 0) {
                            file = fileInput.prop('files')[0];
                        }

                        if (isNew) {
                            // Data baru
                            var newProgramTrainingPlan = $(this).find(
                                'input[name="program_training_plan[]"]').val();
                            console.log("New Program Training Plan:",
                            newProgramTrainingPlan); // Menampilkan nilai new_program_training_plan
                            formData.append('new_section[]', $(this).find('select[name="section[]"]')
                                .val());
                            formData.append('new_id_job_position[]', $(this).find(
                                'select[name="id_job_position[]"]').val());
                            formData.append('new_id_user[]', $(this).find('select[name="id_user[]"]')
                                .val());
                            formData.append('new_program_training_plan[]', newProgramTrainingPlan);
                            formData.append('new_due_date_plan[]', $(this).find(
                                'input[name="due_date_plan[]"]').val());
                            formData.append('new_biaya_plan[]', $(this).find(
                                'input[name="biaya_plan[]"]').val());
                            formData.append('new_lembaga_plan[]', $(this).find(
                                'input[name="lembaga_plan[]"]').val());
                            formData.append('new_keterangan_plan[]', $(this).find(
                                'input[name="keterangan_plan[]"]').val());
                            formData.append('new_status_2[]', $(this).find('select[name="status_2[]"]')
                                .val());
                            formData.append('new_file[]', file);
                        } else {
                            // Data lama
                            var existingProgramTrainingPlan = $(this).find(
                                'input[name="program_training_plan[]"]').val();
                            console.log("Existing Program Training Plan:",
                            existingProgramTrainingPlan); // Menampilkan nilai existing_program_training_plan
                            formData.append('existing_id[]', id);
                            formData.append('existing_section[]', $(this).find(
                                'select[name="section[]"]').val());
                            formData.append('existing_id_job_position[]', $(this).find(
                                'select[name="id_job_position[]"]').val());
                            formData.append('existing_id_user[]', $(this).find(
                                'select[name="id_user[]"]').val());
                            formData.append('existing_program_training[]', $(this).find(
                                'input[name="program_training[]"]').val());
                            formData.append('existing_due_date[]', $(this).find(
                                'input[name="due_date[]"]').val());
                            formData.append('existing_biaya[]', $(this).find('input[name="biaya[]"]')
                                .val());
                            formData.append('existing_lembaga[]', $(this).find(
                                'input[name="lembaga[]"]').val());
                            formData.append('existing_keterangan_tujuan[]', $(this).find(
                                'input[name="keterangan_tujuan[]"]').val());
                            formData.append('existing_program_training_plan[]',
                                existingProgramTrainingPlan);
                            formData.append('existing_due_date_plan[]', $(this).find(
                                'input[name="due_date_plan[]"]').val());
                            formData.append('existing_biaya_plan[]', $(this).find(
                                'input[name="biaya_plan[]"]').val());
                            formData.append('existing_lembaga_plan[]', $(this).find(
                                'input[name="lembaga_plan[]"]').val());
                            formData.append('existing_keterangan_plan[]', $(this).find(
                                'input[name="keterangan_plan[]"]').val());
                            formData.append('existing_status_2[]', $(this).find(
                                'select[name="status_2[]"]').val());
                            formData.append('existing_file[' + id + ']', file);
                        }
                    });

                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.success) {
                                alert('Data berhasil diperbarui!');
                                window.location.href = "{{ route('indexPD2') }}";
                            } else {
                                alert('Terjadi kesalahan: ' + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Terjadi kesalahan: ' + error);
                        }
                    });
                });
            });

            // Function to enable or disable file upload based on status_2 value
            function toggleFileUpload(statusDropdown) {
                var fileInput = statusDropdown.closest('tr').querySelector('input[type="file"]');
                if (statusDropdown.value === 'Done') {
                    fileInput.removeAttribute('disabled');
                } else {
                    fileInput.setAttribute('disabled', 'disabled');
                }
            }

            function updateRowNumbers() {
                var tableBody = document.getElementById('table-body');
                var rows = tableBody.getElementsByTagName('tr');
                for (var i = 0; i < rows.length; i++) {
                    rows[i].getElementsByTagName('td')[0].innerText = i + 1;
                }
            }
            // Function to determine the color based on status
            function getStatusColor(status) {
                switch (status) {
                    case 'Mencari Vendor':
                        return 'blue';
                    case 'Proses Pendaftaran':
                        return 'orange';
                    case 'On Progress':
                        return 'yellow';
                    case 'Done':
                        return 'green';
                    case 'Pending':
                        return 'gray';
                    case 'Ditolak':
                        return 'red';
                    default:
                        return 'white';
                }
            }

            // Function to determine text color for better visibility
            function getTextColor(backgroundColor) {
                const darkColors = ['blue', 'green', 'red', 'orange', 'gray'];
                return darkColors.includes(backgroundColor) ? 'white' : 'black';
            }

            // Function to update dropdown color based on selection
            function updateDropdownColor(dropdown) {
                const color = getStatusColor(dropdown.value);
                dropdown.style.backgroundColor = color;
                dropdown.style.color = getTextColor(color); // Update text color for visibility
            }

            // Apply the color when loading the page (if you have preset values)
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.status-dropdown').forEach(function(dropdown) {
                    updateDropdownColor(dropdown);
                });
            });

            // Fungsi untuk memformat angka sebagai mata uang
            function formatCurrency(amount) {
                return amount.toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                });
            }
        </script>

    </main><!-- End #main -->
@endsection
