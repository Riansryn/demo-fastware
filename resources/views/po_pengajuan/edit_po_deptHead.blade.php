@extends('layout')

@section('content')
    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Halaman Edit Data</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboardHandling') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('index.PO') }}">Menu Pengajuan PO</a></li>
                    <li class="breadcrumb-item active">Halaman Edit Data</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Form Edit Data</h5>
                        <form id="formEditPO" action="{{ route('update.PoPengajuan.dept', $pengajuanPo->id) }}"
                            method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-lg-12">
                                    <!-- Dropdown untuk Kategori PO -->
                                    <div class="row">
                                        <p>No FPB: {{ $pengajuanPo->no_fpb }}</p>
                                        <div class="col-lg-2">
                                            <label for="kategori_po" class="form-label">Kategori PO:<span
                                                    style="color: red;">*</span></label>
                                        </div>
                                        <div class="col-lg-3">
                                            <select id="kategori_po" name="kategori_po" class="form-select"
                                                style="width: 100%;" required>
                                                <option value="">------------- Pilih Kategori PO ------------</option>
                                                <option value="Consumable"
                                                    {{ $pengajuanPo->kategori_po == 'Consumable' ? 'selected' : '' }}>
                                                    Consumable (Bandsaw, insert, OTH)</option>
                                                <option value="Subcon"
                                                    {{ $pengajuanPo->kategori_po == 'Subcon' ? 'selected' : '' }}>Subcon
                                                </option>
                                                <option value="Spareparts"
                                                    {{ $pengajuanPo->kategori_po == 'Spareparts' ? 'selected' : '' }}>
                                                    Spareparts</option>
                                                <option value="Indirect Material"
                                                    {{ $pengajuanPo->kategori_po == 'Indirect Material' ? 'selected' : '' }}>
                                                    Indirect Material (Nitrogen, Ferro Alloy, OTH)</option>
                                                <option value="IT"
                                                    {{ $pengajuanPo->kategori_po == 'IT' ? 'selected' : '' }}>IT (Printer,
                                                    SSD, Laptop, OTH)</option>
                                                <option value="GA"
                                                    {{ $pengajuanPo->kategori_po == 'GA' ? 'selected' : '' }}>GA (Renovasi,
                                                    ATK, Meja, Kursi, OTH)</option>
                                            </select>
                                        </div>
                                        <!-- Add Row Button -->
                                        <button type="button" class="btn btn-info mb-3" style="width: 5%"
                                            onclick="addRow()">+</button>
                                    </div>
                                    <br>

                                    <!-- Fields Container -->
                                    <div id="fieldsContainer">
                                        <!-- Start looping through the pengajuanPoList -->
                                        @foreach ($pengajuanPoList as $index => $pengajuanPoItem)
                                            <!-- Item Number -->
                                            <p style="font-size: 20px;"> <b>Item {{ $index + 1 }}</b></p>
                                            <!-- Fields for regular data (fieldsContainer) -->
                                            <div class="row field-group">
                                                <input type="hidden" name="id[]" value="{{ $pengajuanPoItem->id }}">
                                                <div class="col-md-2">
                                                    <label for="nama_barang" class="form-label">Nama Barang:<span
                                                            style="color: red;">*</span></label>
                                                    <input type="text" class="form-control" name="nama_barang[]"
                                                        value="{{ $pengajuanPoItem->nama_barang }}" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="spesifikasi" class="form-label">Spesifikasi:<span
                                                            style="color: red;">*</span></label>
                                                    <input type="text" class="form-control" name="spesifikasi[]"
                                                        value="{{ $pengajuanPoItem->spesifikasi }}" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="pcs" class="form-label">PCS:<span
                                                            style="color: red;">*</span></label>
                                                    <input type="number" class="form-control pcs-input" name="pcs[]"
                                                        value="{{ $pengajuanPoItem->pcs }}" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="price_list" class="form-label">Harga Satuan:<span
                                                            style="color: red;">*</span></label>
                                                    <input type="number" class="form-control price-input"
                                                        name="price_list[]" value="{{ $pengajuanPoItem->price_list }}"
                                                        required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="total_harga" class="form-label">Total Harga:<span
                                                            style="color: red;">*</span></label>
                                                    <input type="number" class="form-control total-input"
                                                        name="total_harga[]" value="{{ $pengajuanPoItem->total_harga }}"
                                                        disabled>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="file" class="form-label">Upload File/Drawing:</label>
                                                    <input class="form-control" type="file" name="file[]"
                                                        accept="*/*">
                                                </div>
                                                <!-- Hapus Baris Button -->
                                                <div class="col-md-4 d-flex align-items-end" style="margin-top: 2%">
                                                    <button type="button" class="btn btn-danger mb-3 remove-row-btn"
                                                        data-id="{{ $pengajuanPoItem->id }}">
                                                        (-)
                                                        Hapus Baris
                                                    </button>
                                                </div>
                                            </div>
                                            <br>

                                            <!-- Subcon Fields, only display if kategori_po is 'Subcon' for this item -->
                                            @if ($pengajuanPoItem->kategori_po == 'Subcon')
                                                <div id="SubconFields">
                                                    <div class="row field-group">
                                                        <div class="col-md-2">
                                                            <label for="target_cost" class="form-label">Target
                                                                Cost:</label>
                                                            <input type="number" class="form-control"
                                                                name="target_cost[]"
                                                                value="{{ $pengajuanPoItem->target_cost }}">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for="lead_time" class="form-label">Lead Time:</label>
                                                            <input type="datetime-local" class="form-control"
                                                                name="lead_time[]"
                                                                value="{{ $pengajuanPoItem->lead_time ? date('Y-m-d\TH:i', strtotime($pengajuanPoItem->lead_time)) : '' }}">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for="rekomendasi"
                                                                class="form-label">Rekomendasi:</label>
                                                            <input type="text" class="form-control"
                                                                name="rekomendasi[]"
                                                                value="{{ $pengajuanPoItem->rekomendasi }}">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for="nama_customer" class="form-label">Nama
                                                                Customer:</label>
                                                            <input type="text" class="form-control"
                                                                name="nama_customer[]"
                                                                value="{{ $pengajuanPoItem->nama_customer }}">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for="nama_project" class="form-label">Nama
                                                                Project:</label>
                                                            <input type="text" class="form-control"
                                                                name="nama_project[]"
                                                                value="{{ $pengajuanPoItem->nama_project }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <hr style="margin-top: 3%">
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row mb-3" style="margin-top: 2%">
                                <div class="col-sm-12 d-flex justify-content-end">
                                    <button id="updateButton" type="submit" class="btn btn-primary mb-4 me-3">
                                        <i class="fas fa-save"></i> Update
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
            <!-- Image Modal -->

        </section>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
        <script>
            // Fungsi untuk memeriksa dan menampilkan/subconFields
            function checkSubcon() {
                const kategori_po = document.getElementById('kategori_po').value;
                const SubconFields = document.getElementById('SubconFields');

                if (kategori_po === 'Subcon') {
                    SubconFields.style.display = 'block'; // Tampilkan jika kategori adalah Subcon
                } else {
                    SubconFields.style.display = 'none'; // Sembunyikan jika kategori bukan Subcon
                }
            }

            // Menambahkan event listener untuk dropdown dan memanggil checkSubcon saat halaman dimuat
            document.addEventListener("DOMContentLoaded", function() {
                // Panggil fungsi saat halaman dimuat
                checkSubcon();

                // Tambahkan event listener pada dropdown kategori
                document.getElementById('kategori_po').addEventListener('change', checkSubcon);
            });

            //deleterow
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.remove-row-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        var id = this.getAttribute('data-id'); // Ambil ID dari button
                        Swal.fire({
                            title: 'Yakin ingin menghapus?',
                            text: "Data ini akan dihapus permanen!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, hapus!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Lakukan AJAX untuk menghapus data di server
                                $.ajax({
                                    url: "{{ route('delete.PoPengajuan', ':id') }}"
                                        .replace(':id', id),
                                    type: 'DELETE',
                                    data: {
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        // Menghapus baris dari view
                                        document.getElementById('field-group-' + id)
                                            .remove();
                                        Swal.fire(
                                            'Terhapus!',
                                            'Data telah berhasil dihapus.',
                                            'success'
                                        );
                                        window.location.reload();
                                    },
                                    error: function(xhr) {
                                        Swal.fire(
                                            'Gagal!',
                                            'Terjadi kesalahan saat menghapus data.',
                                            'error'
                                        );
                                    }
                                });
                            }
                        });
                    });
                });
            });

            // Function to format number without decimal if it's a whole number
            function formatNumber(num) {
                return Number.isInteger(num) ? num.toString() : num.toFixed(2);
            }

            // Function to calculate total price per row
            function calculateTotalHarga(row) {
                const pcsField = row.querySelector('.pcs-input');
                const priceField = row.querySelector('.price-input');
                const totalHargaField = row.querySelector('.total-input');

                if (pcsField && priceField && totalHargaField) {
                    const pcs = parseFloat(pcsField.value) || 0;
                    const price = parseFloat(priceField.value) || 0;
                    const total = pcs * price;
                    totalHargaField.value = formatNumber(total);
                } else {
                    console.error('One or more fields not found in the row', row);
                }
            }

            // Function to add event listeners to a row
            function addListenersToRow(row) {
                const pcsField = row.querySelector('.pcs-input');
                const priceField = row.querySelector('.price-input');

                if (pcsField && priceField) {
                    pcsField.addEventListener('input', function() {
                        calculateTotalHarga(row);
                    });

                    priceField.addEventListener('input', function() {
                        calculateTotalHarga(row);
                    });
                } else {
                    console.error('PCS or Price field not found in the row', row);
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Add event listeners to existing rows
                const rows = document.querySelectorAll('.field-group');
                if (rows.length > 0) {
                    rows.forEach(row => {
                        addListenersToRow(row);
                        calculateTotalHarga(row); // Calculate initial total
                    });
                } else {
                    console.error('No rows with class "field-group" found');
                }
            });

            let itemCount = {{ count($pengajuanPoList) }}; // Inisialisasi dengan jumlah item awal

            function addRow() {
                const dynamicFieldsContainer = document.getElementById('fieldsContainer');

                // Meningkatkan count setiap kali addRow dipanggil
                itemCount += 1;

                // Buat elemen <p> untuk penomoran
                const itemLabel = document.createElement('p');
                itemLabel.style.fontSize = '16px'; // Set ukuran font menjadi 16px

                // Membuat elemen <strong> untuk teks tebal
                const boldText = document.createElement('strong');
                boldText.textContent = `Item ${itemCount}`; // Set teks "Item x"

                // Menambahkan <strong> ke <p>
                itemLabel.appendChild(boldText);

                // Tambahkan elemen <p> ke container
                dynamicFieldsContainer.appendChild(itemLabel);

                // Create the new row with standard fields
                const newRow = document.createElement('div');
                newRow.className = 'row field-group';
                newRow.innerHTML = `
                    <input type="hidden" name="id[]" value="">
                    <div class="col-md-2">
                        <label for="nama_barang" class="form-label">Nama Barang:<span style="color: red;">*</span></label>
                        <input type="text" class="form-control" name="nama_barang[]" required>
                    </div>
                    <div class="col-md-2">
                        <label for="spesifikasi" class="form-label">Spesifikasi:<span style="color: red;">*</span></label>
                        <input type="text" class="form-control" name="spesifikasi[]" required>
                    </div>
                    <div class="col-md-2">
                        <label for="pcs" class="form-label">PCS:<span style="color: red;">*</span></label>
                        <input type="number" class="form-control pcs-input" name="pcs[]" required>
                    </div>
                    <div class="col-md-2">
                        <label for="price_list" class="form-label">Harga Satuan:<span style="color: red;">*</span></label>
                        <input type="number" class="form-control price-input" name="price_list[]" required>
                    </div>
                    <div class="col-md-2">
                        <label for="total_harga" class="form-label">Total Harga:<span style="color: red;">*</span></label>
                        <input type="number" class="form-control total-input" name="total_harga[]" disabled required>
                    </div>
                    <div class="col-md-2">
                        <label for="file" class="form-label">Upload File/Drawing:<span style="color: red;">*</span></label>
                        <input class="form-control" type="file" name="file[]" accept="*/*" required>
                    </div>
                `;

                dynamicFieldsContainer.appendChild(newRow);
                addListenersToRow(newRow); // Tambahkan listeners jika diperlukan

                // Check if kategori_po is Subcon
                const kategoriPoValue = document.getElementById('kategori_po').value;
                if (kategoriPoValue === 'Subcon') {
                    // Add Subcon-specific fields if kategori_po is Subcon
                    const newSubconRow = document.createElement('div');
                    newSubconRow.className = 'row field-group Subcon-fields';
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
                `;
                    dynamicFieldsContainer.appendChild(newSubconRow);
                } else {
                    // Add an <hr> for separation if kategori_po is not Subcon
                    const hrElement = document.createElement('hr');
                    hrElement.style.marginTop = '3%';
                    dynamicFieldsContainer.appendChild(hrElement);
                }
            }


            document.addEventListener('DOMContentLoaded', function() {
                var imageModal = document.getElementById('imageModal')
                imageModal.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget
                    var imgSrc = button.getAttribute('data-bs-img-src')
                    var modalImg = imageModal.querySelector('.modal-body img')
                    modalImg.src = imgSrc
                })
            })
        </script>

    </main><!-- End #main -->
@endsection
