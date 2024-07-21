@extends('layout')

@section('content')

    <main id="main" class="main">

        <style>
            .searchable-dropdown {
                position: relative;
            }

            .searchable-dropdown input {
                width: 100%;
                box-sizing: border-box;
            }

            .dropdown-items {
                display: none;
                position: absolute;
                background-color: white;
                border: 1px solid #ddd;
                max-height: 200px;
                overflow-y: auto;
                z-index: 1000;
            }

            .dropdown-items div {
                padding: 8px;
                cursor: pointer;
            }

            .dropdown-items div:hover {
                background-color: #f1f1f1;
            }
        </style>


        <div class="pagetitle">
            <h1>Halaman Inquiry</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Menu Inquiry Sales</li>
                </ol>
            </nav>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tampilan Data Inquiry Sales</h5>
                    <button class="btn btn-primary btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#inquiryModal">Form
                        Inquiry</button>

                    <!-- Table with stripped rows -->
                    @if ($inquiries->isEmpty())
                        <p>Tidak ada inquiry yang ditemukan.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped" id="inquiryTable">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Kode Inq.</th>
                                        {{-- <th scope="col">Supplier</th> --}}
                                        <th scope="col">Order From</th>
                                        <th scope="col">Create By</th>
                                        <th scope="col">Note</th>
                                        <th scope="col">File</th>
                                        <th scope="col">is Active</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inquiries as $inquiry)
                                        <tr>
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <td>{{ $inquiry->kode_inquiry }}</td>
                                            <td>{{ $inquiry->customer ? $inquiry->customer->name_customer : 'N/A' }}</td>
                                            <td>{{ $inquiry->create_by }}</td>
                                            <td>{{ $inquiry->note }}</td>
                                            <td>
                                                @if ($inquiry->attach_file)
                                                    <a href="{{ asset('assets/files/' . $inquiry->attach_file) }}"
                                                        target="_blank">
                                                        <i class="fas fa-file-alt"></i>
                                                    </a>
                                                @else
                                                    <i class="fas fa-times"></i> No File
                                                @endif
                                            </td>
                                            <td>
                                                @if ($inquiry->status == 0)
                                                    <button type="button" class="btn btn-danger" title="Data tidak aktif">
                                                        <i class="bi bi-exclamation-octagon"></i>
                                                    </button>
                                                @endif
                                            </td>

                                            <td>
                                                @if (
                                                    $inquiry->status != 0 &&
                                                        $inquiry->status != 1 &&
                                                        $inquiry->status != 3 &&
                                                        $inquiry->status != 4 &&
                                                        $inquiry->status != 5 &&
                                                        $inquiry->status != 6 &&
                                                        $inquiry->status != 7)
                                                    <a class="btn btn-primary mt-1" title="Edit">
                                                        <i class="bi bi-pencil-fill"
                                                            onclick="openEditInquiryModal({{ $inquiry->id }})"></i>
                                                    </a>
                                                    <a class="btn btn-danger mt-1" title="Delete">
                                                        <i class="bi bi-trash-fill"
                                                            onclick="deleteInquiry({{ $inquiry->id }})"></i>
                                                    </a>
                                                    <a class="btn btn-info mt-1"
                                                        href="{{ route('formulirInquiry', ['id' => $inquiry->id]) }}"
                                                        title="Formulir Inquiry">
                                                        <i class="bi bi-file-earmark-arrow-up-fill"></i>
                                                    </a>
                                                @endif

                                                <a class="btn btn-warning mt-1" title="View Form"
                                                    href="{{ route('historyFormSS', $inquiry->id) }}">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <!-- End Table with stripped rows -->

                <!-- Modal Add-->
                <div class="modal fade" id="inquiryModal" tabindex="-1" aria-labelledby="inquiryModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="inquiryModalLabel">Form Inquiry</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('storeinquiry') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="jenis_inquiry" class="form-label">Jenis Inquiry</label>
                                        <select class="form-select" id="jenis_inquiry" name="jenis_inquiry" required>
                                            <option value="RO">RO</option>
                                            <option value="SPOR">SPOR</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="id_customer" class="form-label">Order from</label>
                                        <div class="searchable-dropdown">
                                            <input type="text" id="search_customer" placeholder="Select Customer">
                                            <div class="dropdown-items" id="customer_list">
                                                @foreach ($customers as $customer)
                                                    <div data-value="{{ $customer->id }}">{{ $customer->name_customer }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <input type="hidden" id="id_customer" name="id_customer" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Modal -->
                <div class="modal fade" id="editInquiryModal" tabindex="-1" aria-labelledby="editInquiryModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editInquiryModalLabel">Edit Inquiry</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editInquiryForm"
                                    action="{{ route('updateinquiry', ['id' => $inquiries->first()->id ?? 0]) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" id="editInquiryId" name="inquiry_id">
                                    <div class="mb-3">
                                        <label for="editjenis_inquiry" class="form-label">Jenis Inquiry</label>
                                        <select class="form-select" id="editjenis_inquiry" name="jenis_inquiry" required>
                                            <option value="RO">RO</option>
                                            <option value="SPOR">SPOR</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 edit-searchable-dropdown">
                                        <label for="search_edit_customer" class="form-label">Order from</label>
                                        <input type="text" class="form-control" id="search_edit_customer"
                                            placeholder="Search customer...">
                                        <div id="edit_customer_list" class="dropdown-menu show"
                                            style="width: 100%; display: none; max-height: 200px; overflow-y: auto;">
                                            @foreach ($customers as $customer)
                                                <div class="dropdown-item" data-value="{{ $customer->id }}">
                                                    {{ $customer->name_customer }}</div>
                                            @endforeach
                                        </div>
                                        <input type="hidden" id="edit_id_customer" name="id_customer">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- End Edit Inquiry Modal -->
            </div>
            </div>
        </section>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        {{-- excel --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

        <!-- SimpleDataTables JS -->
        <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('search_customer');
                const customerList = document.getElementById('customer_list');
                const hiddenInput = document.getElementById('id_customer');

                searchInput.addEventListener('input', function() {
                    const filter = searchInput.value.toLowerCase();
                    const items = customerList.getElementsByTagName('div');

                    for (let i = 0; i < items.length; i++) {
                        const txtValue = items[i].textContent || items[i].innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            items[i].style.display = '';
                        } else {
                            items[i].style.display = 'none';
                        }
                    }
                });

                customerList.addEventListener('click', function(e) {
                    if (e.target && e.target.matches('div[data-value]')) {
                        const selectedValue = e.target.getAttribute('data-value');
                        const selectedText = e.target.textContent;
                        searchInput.value = selectedText;
                        hiddenInput.value = selectedValue;
                        customerList.style.display = 'none';
                    }
                });

                searchInput.addEventListener('focus', function() {
                    customerList.style.display = 'block';
                });

                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.searchable-dropdown')) {
                        customerList.style.display = 'none';
                    }
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                const searchEditInput = document.getElementById('search_edit_customer');
                const editDropdown = document.getElementById('edit_customer_list');
                const hiddenEditInput = document.getElementById('edit_id_customer');
                const editItems = editDropdown.querySelectorAll('.dropdown-item');

                searchEditInput.addEventListener('input', function() {
                    const filter = searchEditInput.value.toLowerCase();
                    let hasVisibleItems = false;

                    editItems.forEach(function(item) {
                        const text = item.textContent.toLowerCase();
                        if (text.includes(filter)) {
                            item.style.display = 'block';
                            hasVisibleItems = true;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    editDropdown.style.display = hasVisibleItems ? 'block' : 'none';
                });

                editItems.forEach(function(item) {
                    item.addEventListener('click', function() {
                        searchEditInput.value = item.textContent;
                        hiddenEditInput.value = item.getAttribute('data-value');
                        editDropdown.style.display = 'none';
                    });
                });

                document.addEventListener('click', function(event) {
                    if (!event.target.closest('.edit-searchable-dropdown')) {
                        editDropdown.style.display = 'none';
                    }
                });
            });

            function openEditInquiryModal(id) {
                console.log('Opening modal for inquiry ID: ' + id);
                $.ajax({
                    url: '{{ route('editInquiry', ['id' => ':id']) }}'.replace(':id', id),
                    type: 'GET',
                    success: function(response) {
                        console.log('Response:', response);

                        // Populate the form with the received data
                        $('#editjenis_inquiry').val(response.jenis_inquiry);
                        $('#search_edit_customer').val(response
                            .customer_name); // Assuming the response contains customer_name
                        $('#edit_id_customer').val(response.id_customer); // Set the hidden input value
                        $('#editInquiryId').val(response.id);

                        // Populate the customer dropdown
                        const editDropdown = $('#edit_customer_list');
                        editDropdown.empty(); // Clear existing options
                        response.customers.forEach(customer => {
                            const item = $('<div>').addClass('dropdown-item').attr('data-value', customer
                                .id).text(customer.name_customer);
                            item.on('click', function() {
                                $('#search_edit_customer').val(customer.name_customer);
                                $('#edit_id_customer').val(customer.id);
                                editDropdown.hide();
                            });
                            editDropdown.append(item);
                        });

                        // Set the current customer name in the search input
                        $('#search_edit_customer').val(response.customer_name);

                        // Show the modal
                        $('#editInquiryModal').modal('show');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                const dataTable = new simpleDatatables.DataTable("#inquiryTable", {
                    searchable: true, // Aktifkan fitur pencarian
                    perPage: 10, // Jumlah entri data per halaman
                    perPageSelect: [5, 10, 20, 50], // Opsi jumlah entri data per halaman
                    dataProps: {
                        // Fungsi untuk menghasilkan format yang diinginkan
                        "Urutan": (value, data) => {
                            // Mendapatkan indeks baris data saat ini
                            const index = data.tableData.id;

                            // Mendapatkan nilai dari kolom "RO" atau "SPOR"
                            const spoOrRo = data[index][0].startsWith("RO") ? "RO" : "SPOR";

                            // Mendapatkan nilai dari kolom "Bulan"
                            const month = data[index][1];

                            // Mendapatkan nilai dari kolom "Tahun"
                            const year = data[index][2];

                            // Menghasilkan urutan sesuai format yang diinginkan
                            const order = (index + 1).toString().padStart(3, '0');
                            return `${spoOrRo}/${month}/${year}/${order}`;
                        }
                    }
                });
            });

            //delete
            function deleteInquiry(id) {
                Swal.fire({
                    title: "Apakah Anda yakin?",
                    text: "Anda akan menghapus inquiry ini!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('deleteinquiry', '') }}/' + id,
                            type: 'DELETE',
                            data: {
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    'Inquiry telah berhasil dihapus!',
                                    'success'
                                ).then((result) => {
                                    // Jika pengguna menekan tombol 'OK', refresh halaman
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                })
                                // Anda bisa menambahkan kode untuk menghapus baris tabel atau memperbarui tampilan di sini
                            }
                        });
                    }
                })
            }

            // report
            document.getElementById('exportReportBtn').addEventListener('click', function() {
                // Get the table element
                var table = document.getElementById('inquiryTable');

                // Create a workbook and add a worksheet
                var wb = XLSX.utils.table_to_book(table, {
                    sheet: "Inquiry Report"
                });
                var ws = wb.Sheets["Inquiry Report"];

                // Filter out unwanted columns (Note, File, is Active, Actions)
                // Define the columns we want to keep (1-based index: No, Kode Inq., Type Inq., Type, Size, Supplier, Qty, Order From, Create By, To Approve, To Validate)
                var columnsToKeep = [1, 2, 3, 4, 5, 6, 7];

                // Get the range of the worksheet
                var range = XLSX.utils.decode_range(ws['!ref']);

                // Create a new worksheet to store the filtered data
                var newWsData = [];

                for (var R = range.s.r; R <= range.e.r; ++R) {
                    var newRow = [];
                    for (var C = range.s.c; C <= range.e.c; ++C) {
                        if (columnsToKeep.includes(C + 1)) {
                            var cellAddress = {
                                c: C,
                                r: R
                            };
                            var cellRef = XLSX.utils.encode_cell(cellAddress);
                            newRow.push(ws[cellRef] ? ws[cellRef].v : null);
                        }
                    }
                    newWsData.push(newRow);
                }

                // Create a new worksheet with the filtered data
                var newWs = XLSX.utils.aoa_to_sheet(newWsData);

                // Apply auto filter to the header row
                newWs['!autofilter'] = {
                    ref: `A1:K${newWsData.length}`
                };

                // Adjust column widths
                var colWidths = [{
                        wpx: 40
                    }, // No
                    {
                        wpx: 100
                    }, // Kode Inq.

                    {
                        wpx: 120
                    }, // Supplier

                    {
                        wpx: 100
                    }, // Order From
                    {
                        wpx: 100
                    }, // Create By
                    {
                        wpx: 100
                    }, // To Approve
                    {
                        wpx: 100
                    } // To Validate
                ];
                newWs['!cols'] = colWidths;

                // Replace the old worksheet with the new one
                wb.Sheets["Inquiry Report"] = newWs;

                // Write the workbook to a file
                XLSX.writeFile(wb, 'Inquiry_Report.xlsx');
            });
        </script>

        <script>
            var inputCount = 1; // Untuk menghitung jumlah input yang ada

            function addInput() {
                inputCount++;
                var html = `<div class="mb-3">
                         <label for="supplier" class="form-label">Type</label>
                        <input type="text" class="form-control type-input" name="type[]" required>
                    </div>
                    <div class="mb-3">
                          <label for="supplier" class="form-label">Size</label>
                        <input type="text" class="form-control size-input" name="size[]" required>
                    </div>`;
                $('#inputContainer').append(html);
            }
        </script>


    </main><!-- End #main -->
@endsection
