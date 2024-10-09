@extends('layout')

@section('content')
    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Menu Pre Order</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboardHandling') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Menu Pengajuan PO</li>

                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tampilan Data Pengajuan PO</h5>
                            <!-- Table with stripped rows -->
                            <div class="table-responsive" style="height: 100%; overflow-y: auto;">
                                <!-- Menambahkan SweetAlert untuk notifikasi pengajuan cancel -->
                                @if (!empty($pengajuanCancel))
                                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            Swal.fire({
                                                title: 'Notifikasi!',
                                                text: 'Terdapat Pengajuan Cancel Item Pada No FPB: {{ implode(', ', $pengajuanCancel) }}',
                                                icon: 'warning',
                                                confirmButtonText: 'OK'
                                            });
                                        });
                                    </script>
                                @endif
                                <table class="datatable table">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="50px">NO</th>
                                            <th class="text-center" width="100px">NO FPB</th>
                                            <th class="text-center" width="100px">NO PO</th>
                                            <th class="text-center" width="100px">PIC</th>
                                            <th class="text-center" width="100px">Kategori</th>
                                            <th class="text-center" width="100px">Catatan</th>
                                            <th class="text-center" width="100px">Status</th>
                                            <th class="text-center" width="100px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $row)
                                            @if ($row->status_1 == 5 || $row->status_1 == 6 || $row->status_1 == 7 || $row->status_1 == 8 || $row->status_1 == 9 || $row->status_1 == 10 || $row->status_1 == 11 || $row->status_1 == 12)
                                                <!-- Tambahkan kondisi untuk menampilkan hanya jika status_1 == 2 -->
                                                <tr>
                                                    <td class="text-center py-3">{{ $loop->iteration }}</td>
                                                    <td class="text-center py-3">{{ $row->no_fpb }}</td>
                                                    <td class="text-center py-3">
                                                        @if (empty($row->no_po))
                                                            No PO belum dibuat
                                                        @else
                                                            {{ $row->no_po }}
                                                        @endif
                                                    </td>
                                                    <td class="text-center py-3">{{ $row->modified_at }}</td>
                                                    <td class="text-center py-3">{{ $row->kategori_po }}</td>
                                                    <td class="text-center py-3"><b>{{ $row->catatan }}</b></td>
                                                    <td class="text-center py-4"
                                                        style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        @if ($row->status_1 == 1)
                                                            <span class="badge bg-secondary align-items-center"
                                                                style="font-size: 18px;">Draf</span>
                                                        @elseif ($row->status_1 == 2)
                                                            <span class="badge bg-success align-items-center"
                                                                style="font-size: 18px;">Open</span>
                                                        @elseif ($row->status_1 == 3)
                                                            <span class="badge bg-success align-items-center"
                                                                style="font-size: 18px;">Open</span>
                                                        @elseif($row->status_1 == 4)
                                                            <span class="badge bg-success align-items-center"
                                                                style="font-size: 18px;">Open</span>
                                                        @elseif($row->status_1 == 5)
                                                            <span class="badge bg-success align-items-center"
                                                                style="font-size: 18px;">Open</span>
                                                        @elseif($row->status_1 == 6)
                                                            <span class="badge bg-success align-items-center"
                                                                style="font-size: 18px;">Open</span>
                                                        @elseif($row->status_1 == 7)
                                                            <span class="badge bg-success align-items-center"
                                                                style="font-size: 18px;">Open</span>
                                                        @elseif($row->status_1 == 8)
                                                            <span class="badge bg-success align-items-center"
                                                                style="font-size: 18px;">Open</span>
                                                        @elseif($row->status_1 == 9)
                                                            <span class="badge bg-info align-items-center"
                                                                style="font-size: 18px;">Finish</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center py-4">
                                                        <a href="{{ route('view.FormPo.proc', ['id' => $row->id]) }}"
                                                            class="btn btn-primary btn-sm" title="View Form">
                                                            <i class="fa-regular fa-folder-open"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.btn-kirim').forEach(button => {
                    button.addEventListener('click', function() {
                        // Mengambil no_fpb dari data attribute dan mengganti "/" dengan "-"
                        var no_fpb = this.getAttribute('data-no_fpb').replace(/\//g, '-');
                        console.log("FPB Number to send:", no_fpb); // Log FPB number yang diambil

                        Swal.fire({
                            title: 'Apakah anda ingin mengirim data?',
                            text: "Data yang telah dikirim tidak dapat dirubah!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, kirim!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Jika konfirmasi, lakukan AJAX POST request
                                $.ajax({
                                    url: "{{ route('kirim.fpb.finance', ':no_fpb') }}"
                                        .replace(':no_fpb', no_fpb),
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}', // CSRF Token Laravel
                                    },
                                    success: function(response) {
                                        console.log("Response from server:",
                                            response); // Log response dari server

                                        Swal.fire(
                                            'Terkirim!',
                                            'Data berhasil dikirim.',
                                            'success'
                                        ).then(() => {
                                            location
                                                .reload(); // Refresh halaman setelah sukses
                                        });
                                    },
                                    error: function(xhr) {
                                        console.log("Error occurred:", xhr
                                            .responseText
                                        ); // Log error jika terjadi kesalahan

                                        Swal.fire(
                                            'Gagal!',
                                            'Terjadi kesalahan saat mengirim data.',
                                            'error'
                                        );
                                    }
                                });
                            }
                        });
                    });
                });
            });
        </script>

    </main><!-- End #main -->
@endsection
