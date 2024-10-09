@extends('layout')

@section('content')
    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Halaman Tambah Data</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboardHandling') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('index.PO') }}">Menu Pengajuan PO</a></li>
                    <li class="breadcrumb-item active">Halaman Tambah Data</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Form Tambah Data</h5>
                        <form id="formPengajuanPO" action="{{ route('store.po') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12">
                                    <!-- Dropdown untuk Kategori PO -->
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <label for="kategori_po" class="form-label">Kategori PO:<span
                                                    style="color: red;">*</span></label>
                                        </div>
                                        <div class="col-lg-3">
                                            <select id="kategori_po" name="kategori_po" class="form-select"
                                                style="width: 100%;" required onchange="checkSubcon()">
                                                <option value="">------------- Pilih Kategori PO ------------</option>
                                                <option value="Consumable">Consumable (Bandsaw, insert, OTH)</option>
                                                <option value="Subcon">Subcon</option>
                                                <option value="Spareparts">Spareparts</option>
                                                <option value="Indirect Material">Indirect Material (Nitrogen, Ferro Alloy,
                                                    OTH)</option>
                                                <option value="IT">IT (Printer, SSD, Laptop, OTH)</option>
                                                <option value="GA">GA (Renovasi, ATK, Meja, Kursi, OTH)</option>
                                            </select>
                                        </div>
                                        <!-- Add Row Button -->
                                        <button type="button" class="btn btn-info mb-3" style="width: 5%"
                                            onclick="addRow()">+</button>
                                    </div>
                                    <br>

                                    <!-- Dynamic Fields Container -->
                                    <div id="dynamicFieldsContainer">
                                        <!-- Initial Fields -->
                                        <p style="margin-bottom: 2%;"><b>Item 1</b></p>
                                        <div class="row field-group">
                                            <div class="col-md-2">
                                                <label for="nama_barang" class="form-label">Nama Barang:<span
                                                        style="color: red;">*</span></label>
                                                <input type="text" class="form-control" id="nama_barang"
                                                    name="nama_barang[]" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="spesifikasi" class="form-label">Spesifikasi:<span
                                                        style="color: red;">*</span></label>
                                                <input type="text" class="form-control" id="spesifikasi"
                                                    name="spesifikasi[]" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="pcs" class="form-label">PCS:<span
                                                        style="color: red;">*</span></label>
                                                <input type="number" class="form-control" id="pcs" name="pcs[]"
                                                    required>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="price_list" class="form-label">Harga Satuan:<span
                                                        style="color: red;">*</span></label>
                                                <input type="number" class="form-control" id="price_list"
                                                    name="price_list[]" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="total_harga" class="form-label">Total Harga:<span
                                                        style="color: red;">*</span></label>
                                                <input type="number" class="form-control" id="total_harga"
                                                    name="total_harga[]" disabled required>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="file" class="form-label">Upload File/Drawing:</label>
                                                <input class="form-control" type="file" id="file" name="file[]"
                                                    accept="*/*">
                                            </div>
                                        </div>
                                        <br>
                                        <!-- Input untuk Subcon (dihide by default) -->
                                        <div id="SubconFields" style="display:none;">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <label for="target_cost" class="form-label">Target Cost:</label>
                                                    <input type="number" class="form-control" id="target_cost"
                                                        name="target_cost[]">
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="lead_time" class="form-label">Lead Time:</label>
                                                    <input type="datetime-local" class="form-control" id="lead_time"
                                                        name="lead_time[]">
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="rekomendasi" class="form-label">Rekomendasi:</label>
                                                    <input type="text" class="form-control" id="rekomendasi"
                                                        name="rekomendasi[]">
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="nama_customer" class="form-label">Nama Customer:</label>
                                                    <input type="text" class="form-control" id="nama_customer"
                                                        name="nama_customer[]">
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="nama_project" class="form-label">Nama Project:</label>
                                                    <input type="text" class="form-control" id="nama_project"
                                                        name="nama_project[]">
                                                </div>
                                            </div>
                                            <br>
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row mb-3" style="margin-top: 2%">
                                <div class="col-sm-12 d-flex justify-content-end">
                                    <button id="saveButton" type="submit" class="btn btn-primary mb-4 me-3">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                    <a href="{{ route('index.PO') }}" class="btn btn-primary mb-4 me-2">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
        <script>
            function checkSubcon() {
                const kategori_po = document.getElementById('kategori_po').value;
                const SubconFields = document.getElementById('SubconFields');

                if (kategori_po === 'Subcon') {
                    SubconFields.style.display = 'block';
                } else {
                    SubconFields.style.display = 'none';
                }
            }

            // Fungsi untuk menghitung total harga per baris
            function calculateTotalHarga(row) {
                const pcsField = row.querySelector('input[name="pcs[]"]');
                const priceField = row.querySelector('input[name="price_list[]"]');
                const totalHargaField = row.querySelector('input[name="total_harga[]"]');

                const pcs = parseFloat(pcsField.value) || 0;
                const price = parseFloat(priceField.value) || 0;

                totalHargaField.value = pcs * price;
            }

            // Fungsi untuk menambahkan event listener pada input pcs dan price_list
            function addListenersToRow(row) {
                const pcsField = row.querySelector('input[name="pcs[]"]');
                const priceField = row.querySelector('input[name="price_list[]"]');

                pcsField.addEventListener('input', function() {
                    calculateTotalHarga(row);
                });

                priceField.addEventListener('input', function() {
                    calculateTotalHarga(row);
                });
            }

            // Menambahkan event listener pada row yang sudah ada
            document.querySelectorAll('.field-group').forEach(row => {
                addListenersToRow(row);
            });

            let itemCount = 1; // Variabel untuk menghitung jumlah item
            function addRow() {
                itemCount++; // Tambah jumlah item setiap kali baris baru ditambahkan
                const dynamicFieldsContainer = document.getElementById('dynamicFieldsContainer');
                const newRow = document.createElement('div');
                newRow.className = 'row field-group'; // Memastikan ada kelas row untuk grup
                newRow.innerHTML = `
                <p style="margin-bottom: 2%;"><b>Item ${itemCount}</b></p> <!-- Menampilkan nomor item dinamis -->
                    <div class="col-md-2">
                        <label for="nama_barang" class="form-label">Nama Barang:<span style="color: red;">*</span></label>
                        <input type="text" class="form-control" name="nama_barang[]" required>
                    </div>
                    <div class="col-md-2">
                        <label for="spesifikasi" class="form-label">Spesifikasi:<span style="color: red;">*</span></label>
                        <input type="text" class="form-control" name="spesifikasi[]" required>
                    </div>
                    <div class="col-md-2">
                        <label for="pcs" class="form-label">PCS:<span
                                style="color: red;">*</span></label>
                        <input type="number" class="form-control" id="pcs" name="pcs[]"
                            required>
                    </div>
                    <div class="col-md-2">
                        <label for="price_list" class="form-label">Harga Satuan:<span
                                style="color: red;">*</span></label>
                        <input type="number" class="form-control" id="price_list" name="price_list[]"
                            required>
                    </div>
                    <div class="col-md-2">
                        <label for="total_harga" class="form-label">Total Harga:<span
                                style="color: red;">*</span></label>
                        <input type="number" class="form-control" id="total_harga"
                            name="total_harga[]" disabled required>
                    </div>
                    <div class="col-md-2">
                        <label for="file" class="form-label">Upload File/Drawing:</label>
                        <input class="form-control" type="file" name="file[]" accept="*/*">
                    </div>
                    <hr style="margin-top:3%">
                `;
                dynamicFieldsContainer.appendChild(newRow);
                // Tambahkan listener untuk row baru
                addListenersToRow(newRow);


                // Menambahkan field Subcon jika kategori PO adalah "Subcon"
                const SubconFields = document.getElementById('SubconFields');
                if (document.getElementById('kategori_po').value === 'Subcon') {
                    const newSubconRow = document.createElement('div');
                    newSubconRow.className = 'row field-group';
                    newSubconRow.innerHTML = `
                    <div class="col-md-2">
                        <label for="target_cost" class="form-label">Target Cost:</label>
                        <input type="number" class="form-control" name="target_cost[]">
                    </div>
                    <div class="col-md-2">
                        <label for="lead_time" class="form-label">Lead Time:</label>
                        <input type="datetime-local" class="form-control" name="lead_time[]">
                    </div>
                    <div class="col-md-2">
                        <label for="rekomendasi" class="form-label">Rekomendasi:</label>
                        <input type="text" class="form-control" name="rekomendasi[]">
                    </div>
                    <div class="col-md-2">
                        <label for="nama_customer" class="form-label">Nama Customer:</label>
                        <input type="text" class="form-control" name="nama_customer[]">
                    </div>
                    <div class="col-md-2">
                        <label for="nama_project" class="form-label">Nama Project:</label>
                        <input type="text" class="form-control" name="nama_project[]">
                    </div>
                     <hr style="margin-top:3%">
                `;
                    dynamicFieldsContainer.appendChild(newSubconRow);
                }
            }
        </script>

    </main><!-- End #main -->
@endsection
