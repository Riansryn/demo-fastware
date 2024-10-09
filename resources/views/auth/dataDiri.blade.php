@extends('layout')

@section('content')
    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Profile</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboardHandling') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Data Diri</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section profile">
            <div class="row">
                <div class="col-xl-4">

                    <div class="card">
                        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

                            <img src="assets/img/user.png" alt="Profile" class="rounded-circle">
                            <h2>{{ Auth::user()->name }}</h2>
                            <h3>{{ Auth::user()->roles->role }}</h3>
                        </div>
                    </div>

                </div>

                <div class="col-xl-8">

                    <div class="card">
                        <div class="card-body pt-3">
                            <!-- Bordered Tabs -->
                            <ul class="nav nav-tabs nav-tabs-bordered">

                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-toggle="tab"
                                        data-bs-target="#profile-overview">Data Diri</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Data
                                        Diri</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab"
                                        data-bs-target="#profile-change-password">Ubah Password</button>
                                </li>

                            </ul>
                            <div class="tab-content pt-2">
                                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                    <h5 class="card-title">Data Diri</h5>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label ">Nama Lengkap</div>
                                        <div class="col-lg-9 col-md-8">{{ $user->name }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Departemen</div>
                                        <div class="col-lg-9 col-md-8">{{ $role->role }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">NPK</div>
                                        <div class="col-lg-9 col-md-8">{{ $user->npk }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">No. Telp</div>
                                        <div class="col-lg-9 col-md-8">{{ $user->telp }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Email</div>
                                        <div class="col-lg-9 col-md-8">{{ $user->email }}</div>
                                    </div>
                                </div>
                                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
                                    <!-- Profile Edit Form -->
                                    <form id="ubahDataDiriForm" action="{{ route('ubahDataDiri') }}" method="post">
                                        @csrf
                                        <div class="row mb-3">
                                            <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Nama
                                                lengkap</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="name" type="text" class="form-control" id="name"
                                                    value="{{ $user->name }}">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="about"
                                                class="col-md-4 col-lg-3 col-form-label">Departemen</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="name" type="text" class="form-control" id="name"
                                                    value="{{ $role->role }}" disabled>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="company" class="col-md-4 col-lg-3 col-form-label">NPK</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="npk" type="text" class="form-control" id="company"
                                                    value="{{ $user->npk }}">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="Job" class="col-md-4 col-lg-3 col-form-label">No. Telp</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="telp" type="text" class="form-control" id="telp"
                                                    value="{{ $user->telp }}">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="Country" class="col-md-4 col-lg-3 col-form-label">Email</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="email" type="text" class="form-control" id="email"
                                                    value="{{ $user->email }}">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary"
                                                onclick="submitDataDiri()">Save</button>
                                        </div>
                                    </form><!-- End Profile Edit Form -->
                                </div>
                                <div class="tab-pane fade pt-3" id="profile-change-password">
                                    <!-- Change Password Form -->
                                    <form action="{{ route('ubahPassword') }}" method="post">
                                        @csrf
                                        <div class="row mb-3">
                                            <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Password
                                                Anda Saat Ini</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="currentPassword" type="password" class="form-control"
                                                    id="currentPassword" required>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">Password
                                                Baru</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="newPassword" type="password" class="form-control"
                                                    id="newPassword" required>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="newPassword_confirmation"
                                                class="col-md-4 col-lg-3 col-form-label">Konfirmasi Password Baru</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="newPassword_confirmation" type="password"
                                                    class="form-control" id="newPassword_confirmation" required>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary"
                                                onclick="return validatePasswordChange();">Ubah Password</button>
                                        </div>
                                    </form><!-- End Change Password Form -->

                                </div>
                            </div><!-- End Bordered Tabs -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <script>
            function validatePasswordChange() {
                var currentPassword = document.getElementById('currentPassword').value;
                var newPassword = document.getElementById('newPassword').value;
                var confirmPassword = document.getElementById('newPassword_confirmation').value;
                var currentPasswordStored = "{{ Auth::user()->pass }}"; // Password awal yang disimpan di database

                if (!currentPassword || !newPassword || !confirmPassword) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Mohon isi semua field dengan benar!'
                    });
                    return false;
                }

                if (currentPassword !== currentPasswordStored) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Password anda saat ini tidak sesuai!'
                    });
                    return false;
                }

                if (newPassword.length < 8) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Password baru harus memiliki minimal 8 karakter!'
                    });
                    return false;
                }

                if (newPassword === currentPassword) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Password baru harus berbeda dengan password Anda saat ini!'
                    });
                    return false;
                }

                if (newPassword !== confirmPassword) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Konfirmasi password baru tidak sesuai dengan password baru!'
                    });
                    return false;
                }

                // Jika semua validasi berhasil
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: 'Password berhasil diubah.',
                    showConfirmButton: false // Hilangkan tombol OK
                });
                return true;
            }


            function submitDataDiri() {
                // Submit form menggunakan JavaScript
                document.getElementById('ubahDataDiriForm').submit();

                // Tampilkan SweetAlert setelah form disubmit
                Swal.fire({
                    title: 'Success!',
                    text: 'Data berhasil disimpan.',
                    icon: 'success',
                    showConfirmButton: false
                });
            }
        </script>
    </main><!-- End #main -->
@endsection
