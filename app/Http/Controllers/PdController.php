<?php

namespace App\Http\Controllers;

use App\Models\TcJobPosition;
use App\Models\TcPeopleDevelopment;
use App\Models\TrsPenilaianTc;
use App\Models\PoinKategori;
use App\Models\User;
use App\Models\Role;
use App\Models\BtnStatus;
use App\Models\DetailTcPenilaian;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache; // Import the Cache facade


class PdController extends Controller
{
    //

    public function indexPD()
    {
        // Ambil ID peran pengguna yang login
        $loggedInUserRoleId = auth()->user()->role_id;

        // Ambil nama pengguna yang login
        $loggedInUser = auth()->user()->name; // Ganti dengan cara yang sesuai untuk mengambil nama pengguna

        // Subquery untuk mendapatkan id maksimum untuk setiap job position yang dikelompokkan berdasarkan tahun_aktual
        $subQuery = TcPeopleDevelopment::select('tahun_aktual', DB::raw('MAX(id) as max_id'))
            ->groupBy('tahun_aktual');

        // Main query untuk mendapatkan data terbaru berdasarkan id maksimum dari subquery
        $query = TcPeopleDevelopment::joinSub($subQuery, 'sub', function ($join) {
            $join->on('mst_pd_pengajuans.id', '=', 'sub.max_id');
        });

        // Tambahkan filter jika role_id bukan 1, 14, atau 15
        if (!in_array($loggedInUserRoleId, [1, 14, 15])) {
            // Misalnya, filter berdasarkan nama pengguna jika role_id bukan salah satu dari yang ditentukan
            $query->where('modified_at', $loggedInUser);
        }

        // Ambil data
        $data = $query->get();

        // Ambil tahun_aktual dari data pertama (jika ada)
        $tahun_aktual = $data->first()->tahun_aktual ?? now()->year;

        // Mengirim data ke view
        return view('people_development.dept_develop_index', compact('data', 'tahun_aktual'));
    }

    public function indexPD2()
    {
        // Mendapatkan tahun saat ini
        $currentYear = Carbon::now()->year;

        // Membuat array tahun dari tahun saat ini hingga 2 tahun ke depan
        $years = [];
        for ($i = 0; $i <= 2; $i++) {
            $years[] = (string)($currentYear + $i);
        }

        // Subquery untuk mendapatkan id maksimum per tahun_aktual
        $subQuery = TcPeopleDevelopment::select('tahun_aktual', DB::raw('MAX(id) as max_id'))
            ->whereIn('status_1', [2, 3])
            ->whereIn('tahun_aktual', $years)
            ->groupBy('tahun_aktual');

        // Mengambil data dari TcPeopleDevelopment beserta relasi Role
        $data = TcPeopleDevelopment::with('role')
            ->joinSub($subQuery, 'sub', function ($join) {
                $join->on('mst_pd_pengajuans.id', '=', 'sub.max_id');
            })
            ->orderBy('mst_pd_pengajuans.tahun_aktual', 'asc') // Mengurutkan berdasarkan tahun_aktual secara ascending, pastikan menyebutkan nama tabel
            ->get();

        // Mengirim data ke view
        return view('people_development.hrga_develop_index', compact('data'));
    }

    public function historiDevelop()
    {
        // Get the role of the authenticated user
        $roleId = auth()->user()->role_id;

        // Initialize the sections based on the role_id
        $sections = [];

        // Role 11: Finance, Accounting, HRGA, IT
        if ($roleId == 11) {
            $sections = [
                'Finance, Accounting',
                'PDCA, HR, GA, Legal, Procurement, IT',
                'Fin, Acc, Proc, HRGA & IT',
                'HR, GA & Legal',
                'PDCA, Procurement, IT',
            ];
        }

        // Role 5: Production-related sections
        if ($roleId == 5) {
            $sections = [
                'PPC, Production CT',
                'Production HT',
                'Production MC & Machining Custom',
                'Technical Support QC & Maintenance',
            ];
        }

        // Role 2: Sales-related sections
        if ($roleId == 2) {
            $sections = [
                'Sales Region I, II, III, IV',
                'Sales Region I, II',
                'Sales Region III, IV',
                'Sales Region II',
                'Sales Region I'
            ];
        }

        // Role 7: Logistics
        if ($roleId == 7) {
            $sections = [
                'Logistics'
            ];
        }

        // Role 1, 14, 15: Can access all sections
        if (in_array($roleId, [1, 14, 15])) {
            $sections = array_merge(
                [
                    'Finance, Accounting',
                    'PDCA, HR, GA, Legal, Procurement, IT',
                    'Fin, Acc, Proc, HRGA & IT',
                    'HR, GA & Legal',
                    'PDCA, Procurement, IT',
                ],
                [
                    'PPC, Production CT',
                    'Production HT',
                    'Production MC & Machining Custom',
                    'Technical Support QC & Maintenance',
                ],
                [
                    'Sales Region I, II, III, IV',
                    'Sales Region I, II',
                    'Sales Region III, IV',
                    'Sales Region II',
                    'Sales Region I'
                ],
                [
                    'Logistics'
                ]
            );
        }

        // Query the TcPeopleDevelopment model based on sections
        $dataTcPeopleDevelopment = TcPeopleDevelopment::whereIn('section', $sections) // Filter by sections
            ->where('status_2', 'Done') // Add condition for status_2 to be 'Done'
            ->with('user') // Ensure the user relationship is loaded
            ->get();

        // Pass the data to the view using compact
        return view('people_development.histori_develop', compact('dataTcPeopleDevelopment'));
    }

    public function viewPD($modified_at)
    {
        // Mengambil data berdasarkan id_job_position
        $data = TcPeopleDevelopment::where('modified_at', $modified_at)
            ->with('role', 'user', 'jobPosition') // Mengambil relasi yang dibutuhkan
            ->get();

        // Mengirim data ke view
        return view('people_development.view_develop', compact('data'));
    }

    public function viewPD2()
    {
        // Mengambil semua data dari model TcPeopleDevelopment
        $data = TcPeopleDevelopment::with('role', 'user', 'jobPosition') // Mengambil relasi yang dibutuhkan
            ->get();

        // Mengirim data ke view
        return view('people_development.view_develop_hrga', compact('data'));
    }

    public function createPD()
    {
        // Get the logged-in user's role_id
        $roleId = auth()->user()->role_id;

        // Initialize the sections based on role_id
        $sections = [];

        // Role 11: Finance, Accounting, HRGA, IT
        if ($roleId == 11) {
            $sections = [
                'Finance, Accounting',
                'PDCA, HR, GA, Legal, Procurement, IT',
                'Fin, Acc, Proc, HRGA & IT',
                'HR, GA & Legal',
                'PDCA, Procurement, IT',
            ];
        }

        // Role 5: Production-related sections
        if ($roleId == 5) {
            $sections = [
                'PPC, Production CT',
                'Production HT',
                'Production MC & Machining Custom',
                'Technical Support QC & Maintenance',
            ];
        }

        // Role 2: Sales-related sections
        if ($roleId == 2) {
            $sections = [
                'Sales Region I, II, III, IV',
                'Sales Region I, II',
                'Sales Region III, IV',
                'Sales Region II',
                'Sales Region I'
            ];
        }

        // Role 2: Sales-related sections
        if ($roleId == 7) {
            $sections = [
                'Logistics'
            ];
        }

        // Role 1, 14, 15: Can access all sections
        if (in_array($roleId, [1, 14, 15])) {
            $sections = array_merge(
                [
                    'Finance, Accounting',
                    'PDCA, HR, GA, Legal, Procurement, IT',
                    'Fin, Acc, Proc, HRGA & IT',
                    'HR, GA & Legal',
                    'PDCA, Procurement, IT',
                ],
                [
                    'PPC, Production CT',
                    'Production HT',
                    'Production MC & Machining Custom',
                    'Technical Support QC & Maintenance',
                ],
                [
                    'Sales Region I, II, III, IV',
                    'Sales Region I, II',
                    'Sales Region III, IV',
                    'Sales Region II',
                    'Sales Region I'
                ],
                [
                    'Logistics'
                ]
            );
        }

        // Fetch job positions with relationships to users
        $jobPositions = TcJobPosition::with('user')->get();

        // Fetch penilaian data (as per your original logic)
        $technicalPenilaians = DB::table('trs_penilaian_tcs as p')
            ->join('mst_tcs as tc', 'p.id_tc', '=', 'tc.id')
            ->select(DB::raw("'Technical' as category"), 'tc.keterangan_tc as keterangan', 'tc.nilai as nilai_standard', 'p.nilai_tc as nilai_aktual', 'p.id_user', 'p.id_tc')
            ->whereNotNull('p.id_tc')
            ->get();

        $nonTechnicalPenilaians = DB::table('trs_penilaian_tcs as p')
            ->join('mst_soft_skills as sk', 'p.id_sk', '=', 'sk.id')
            ->select(DB::raw("'Non-Technical' as category"), 'sk.keterangan_sk as keterangan', 'sk.nilai as nilai_standard', 'p.nilai_sk as nilai_aktual', 'p.id_user', 'p.id_sk')
            ->whereNotNull('p.id_sk')
            ->get();

        $additionalPenilaians = DB::table('trs_penilaian_tcs as p')
            ->join('mst_additionals as ad', 'p.id_ad', '=', 'ad.id')
            ->select(DB::raw("'Additional' as category"), 'ad.keterangan_ad as keterangan', 'ad.nilai as nilai_standard', 'p.nilai_ad as nilai_aktual', 'p.id_user', 'p.id_ad')
            ->whereNotNull('p.id_ad')
            ->get();

        $penilaians = $technicalPenilaians->merge($nonTechnicalPenilaians)->merge($additionalPenilaians);

        // Pass sections and other data to the view
        return view('people_development.create_develop', compact('sections', 'jobPositions', 'penilaians'));
    }

    public function savePdPengajuan(Request $request)
    {
        $data = $request->all();
        $userName = Auth::user()->name; // Mengambil nama user yang sedang login


        // Cek apakah kunci 'id_user' ada dalam $data
        if (!isset($data['id_user']) || !is_array($data['id_user'])) {
            return redirect()->back()->with('error', 'Data id_user tidak ditemukan atau tidak valid.');
        }

        // Loop melalui setiap row dan simpan data
        foreach ($data['id_user'] as $key => $value) {

            $tahunAktual = date('Y') + 1; // Mendapatkan tahun depan
            TcPeopleDevelopment::create([
                'section' => $data['section'][$key] ?? null,
                'id_job_position' => $data['id_job_position'][$key] ?? null,
                'id_user' => $data['id_user'][$key] ?? null,
                'program_training' => $data['program_training'][$key] ?? null,
                'kategori_competency' => $data['kategori_competency'][$key] ?? null,
                'competency' => $data['competency'][$key] ?? null,
                'due_date' => $data['due_date'][$key] ?? null,
                'lembaga' => $data['lembaga'][$key] ?? null,
                'keterangan_tujuan' => $data['keterangan_tujuan'][$key] ?? null,
                'modified_at' => $userName, // Menambahkan modified_at dengan nama user yang login
                'status_1' => 1,
                'tahun_aktual' => $tahunAktual, // Simpan tahun depan di kolom tahun_aktual
                // Tambahkan field lain jika perlu
            ]);
        }

        return redirect()->route('indexPD')->with('success', 'Data berhasil diperbarui');
    }

    public function editPdPengajuan($modified_at)
    {
        // Get the logged-in user's role_id
        $roleId = auth()->user()->role_id;

        // Initialize the sections based on role_id
        $sections = [];

        // Role 11: Finance, Accounting, HRGA, IT
        if ($roleId == 11) {
            $sections = [
                'Finance, Accounting',
                'PDCA, HR, GA, Legal, Procurement, IT',
                'Fin, Acc, Proc, HRGA & IT',
                'HR, GA & Legal',
                'PDCA, Procurement, IT',
                'Procurement'
            ];
        }

        // Role 5: Production-related sections
        if ($roleId == 5) {
            $sections = [
                'PPC, Production CT',
                'Production HT',
                'Production MC & Machining Custom',
                'Technical Support QC & Maintenance',
            ];
        }

        // Role 2: Sales-related sections
        if ($roleId == 2) {
            $sections = [
                'Sales Region I, II, III, IV',
                'Sales Region I, II',
                'Sales Region III, IV',
                'Sales Region II',
                'Sales Region I'
            ];
        }

        // Role 7: Logistics-related sections
        if ($roleId == 7) {
            $sections = [
                'Logistics'
            ];
        }

        // Role 1, 14, 15: Can access all sections
        if (in_array($roleId, [1, 14, 15])) {
            $sections = array_merge(
                [
                    'Finance, Accounting',
                    'PDCA, HR, GA, Legal, Procurement, IT',
                    'Fin, Acc, Proc, HRGA & IT',
                    'HR, GA & Legal',
                    'PDCA, Procurement, IT',
                ],
                [
                    'PPC, Production CT',
                    'Production HT',
                    'Production MC & Machining Custom',
                    'Technical Support QC & Maintenance',
                ],
                [
                    'Sales Region I, II, III, IV',
                    'Sales Region I, II',
                    'Sales Region III, IV',
                    'Sales Region II',
                    'Sales Region I'
                ],
                [
                    'Logistics'
                ]
            );
        }

        // Fetch job positions along with their relations
        $jobPositions = TcJobPosition::with('role', 'user')->get();

        // Fetch TcPenilaian data based on the user for the Technical category
        $technicalPenilaians = DB::table('trs_penilaian_tcs as p')
            ->join('mst_tcs as tc', 'p.id_tc', '=', 'tc.id')
            ->select(
                DB::raw("'Technical' as category"),
                'tc.keterangan_tc as keterangan',
                'tc.nilai as nilai_standard',
                'p.nilai_tc as nilai_aktual',
                'p.id_user',
                'p.id_tc'
            )
            ->whereNotNull('p.id_tc')
            ->get();

        // Fetch TcPenilaian data based on the user for the Non-Technical category
        $nonTechnicalPenilaians = DB::table('trs_penilaian_tcs as p')
            ->join('mst_soft_skills as sk', 'p.id_sk', '=', 'sk.id')
            ->select(
                DB::raw("'Non-Technical' as category"),
                'sk.keterangan_sk as keterangan',
                'sk.nilai as nilai_standard',
                'p.nilai_sk as nilai_aktual',
                'p.id_user',
                'p.id_sk'
            )
            ->whereNotNull('p.id_sk')
            ->get();

        // Fetch TcPenilaian data based on the user for the Additional category
        $additionalPenilaians = DB::table('trs_penilaian_tcs as p')
            ->join('mst_additionals as ad', 'p.id_ad', '=', 'ad.id')
            ->select(
                DB::raw("'Additional' as category"),
                'ad.keterangan_ad as keterangan',
                'ad.nilai as nilai_standard',
                'p.nilai_ad as nilai_aktual',
                'p.id_user',
                'p.id_ad'
            )
            ->whereNotNull('p.id_ad')
            ->get();

        // Merge all evaluations into a single collection
        $penilaians = $technicalPenilaians->merge($nonTechnicalPenilaians)->merge($additionalPenilaians);

        // Fetch TcPeopleDevelopment data based on the modified_at timestamp
        $data = TcPeopleDevelopment::where('modified_at', $modified_at)
            ->with('user')
            ->get();

        // Pass data to the view, including sections, job positions, and evaluations
        return view('people_development.edit_develop', compact('data', 'sections', 'jobPositions', 'penilaians'));
    }

    public function editPdPengajuanHRGA()
    {
        // Mengambil role_id pengguna yang sedang login
        $roleId = auth()->user()->role_id;

        // Menentukan sections berdasarkan role_id
        $sections = [];

        // Role 11: Finance, Accounting, HRGA, IT
        if ($roleId == 11) {
            $sections = [
                'Finance, Accounting',
                'PDCA, HR, GA, Legal, Procurement, IT',
                'Fin, Acc, Proc, HRGA & IT',
                'HR, GA & Legal',
                'PDCA, Procurement, IT',
            ];
        }

        // Role 5: Production-related sections
        if ($roleId == 5) {
            $sections = [
                'PPC, Production CT',
                'Production HT',
                'Production MC & Machining Custom',
                'Technical Support QC & Maintenance',
            ];
        }

        // Role 2: Sales-related sections
        if ($roleId == 2) {
            $sections = [
                'Sales Region I, II, III, IV',
                'Sales Region I, II',
                'Sales Region III, IV',
                'Sales Region II',
                'Sales Region I'
            ];
        }

        // Role 2: Sales-related sections
        if ($roleId == 7) {
            $sections = [
                'Logistics'
            ];
        }

        // Role 1, 14, 15: Can access all sections
        if (in_array($roleId, [1, 14, 15])) {
            $sections = array_merge(
                [
                    'Finance, Accounting',
                    'PDCA, HR, GA, Legal, Procurement, IT',
                    'Fin, Acc, Proc, HRGA & IT',
                    'HR, GA & Legal',
                    'PDCA, Procurement, IT',
                ],
                [
                    'PPC, Production CT',
                    'Production HT',
                    'Production MC & Machining Custom',
                    'Technical Support QC & Maintenance',
                ],
                [
                    'Sales Region I, II, III, IV',
                    'Sales Region I, II',
                    'Sales Region III, IV',
                    'Sales Region II',
                    'Sales Region I'
                ],
                [
                    'Logistics'
                ]
            );
        }

        // Mengambil job positions beserta relasinya
        $jobPositions = TcJobPosition::with('role', 'user')->get();

        // Mengambil data TrsPenilaian dengan kategori Technical
        $technicalPenilaians = DB::table('trs_penilaian_tcs as p')
            ->join('mst_tcs as tc', 'p.id_tc', '=', 'tc.id')
            ->select(
                DB::raw("'Technical' as category"),
                'tc.keterangan_tc as keterangan',
                'tc.nilai as nilai_standard',
                'p.nilai_tc as nilai_aktual',
                'p.id_user',
                'p.id_tc'
            )
            ->whereNotNull('p.id_tc')
            ->get();

        // Mengambil data TrsPenilaian dengan kategori Non-Technical
        $nonTechnicalPenilaians = DB::table('trs_penilaian_tcs as p')
            ->join('mst_soft_skills as sk', 'p.id_sk', '=', 'sk.id')
            ->select(
                DB::raw("'Non-Technical' as category"),
                'sk.keterangan_sk as keterangan',
                'sk.nilai as nilai_standard',
                'p.nilai_sk as nilai_aktual',
                'p.id_user',
                'p.id_sk'
            )
            ->whereNotNull('p.id_sk')
            ->get();

        // Mengambil data TrsPenilaian dengan kategori Additional
        $additionalPenilaians = DB::table('trs_penilaian_tcs as p')
            ->join('mst_additionals as ad', 'p.id_ad', '=', 'ad.id')
            ->select(
                DB::raw("'Additional' as category"),
                'ad.keterangan_ad as keterangan',
                'ad.nilai as nilai_standard',
                'p.nilai_ad as nilai_aktual',
                'p.id_user',
                'p.id_ad'
            )
            ->whereNotNull('p.id_ad')
            ->get();

        // Gabungkan semua hasil penilaian
        $penilaians = $technicalPenilaians->merge($nonTechnicalPenilaians)->merge($additionalPenilaians);

        // Mengambil semua data TcPeopleDevelopment tanpa filter modified_at
        $data = TcPeopleDevelopment::with('user')->get();

        // Mengirimkan data ke view, menyertakan sections, job positions, dan penilaians
        return view('people_development.edit_develop_hrga', compact('data', 'sections', 'jobPositions', 'penilaians'));
    }

    public function update(Request $request)
    {
        $data = $request->all();

        // Ambil nama pengguna yang sedang login
        $userName = auth()->user()->name;

        // Ambil tahun depan
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;

        // Ambil jumlah baris data yang dikirimkan
        $rowCount = count($data['id_user']);

        // Loop melalui setiap baris data
        for ($index = 0; $index < $rowCount; $index++) {
            $id = $data['id'][$index] ?? null;

            // Update atau tambahkan data
            TcPeopleDevelopment::updateOrCreate(
                ['id' => $id], // Kriteria pencarian berdasarkan ID
                [   // Data yang akan diupdate atau ditambahkan
                    'section' => $data['section'][$index] ?? null,
                    'id_job_position' => $data['id_job_position'][$index] ?? null,
                    'id_user' => $data['id_user'][$index] ?? null,
                    'program_training' => $data['program_training'][$index] ?? null,
                    'kategori_competency' => $data['kategori_competency'][$index] ?? null,
                    'competency' => $data['competency'][$index] ?? null,
                    'due_date' => $data['due_date'][$index] ?? null,
                    'lembaga' => $data['lembaga'][$index] ?? null,
                    'keterangan_tujuan' => $data['keterangan_tujuan'][$index] ?? null,
                    'modified_at' => $userName, // Nama pengguna yang mengubah
                    'tahun_aktual' => $nextYear, // Tahun depan
                    'status_1' => 1, // Status 1 diset ke 1
                ]
            );

            // Log untuk memeriksa data yang diperbarui atau ditambahkan
            Log::info('Data diperbarui atau ditambahkan untuk ID: ' . ($id ?? 'baru'), [
                'data' => [
                    'section' => $data['section'][$index] ?? null,
                    'id_job_position' => $data['id_job_position'][$index] ?? null,
                    'id_user' => $data['id_user'][$index] ?? null,
                    'program_training' => $data['program_training'][$index] ?? null,
                    'kategori_competency' => $data['kategori_competency'][$index] ?? null,
                    'competency' => $data['competency'][$index] ?? null,
                    'due_date' => $data['due_date'][$index] ?? null,
                    'lembaga' => $data['lembaga'][$index] ?? null,
                    'keterangan_tujuan' => $data['keterangan_tujuan'][$index] ?? null,
                    'modified_at' => $userName,
                    'tahun_aktual' => $nextYear,
                    'status_1' => 1,
                ]
            ]);
        }

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('indexPD')->with('success', 'Data berhasil diperbarui');
    }

    public function updatePdPlan(Request $request)
    {
        $data = $request->all();
        $userName = Auth::user()->name;
        $currentYear = date('Y');

        // Mengolah data baru
        if (isset($data['new_section'])) {
            foreach ($data['new_section'] as $index => $section) {
                $tcPeopleDevelopment = new TcPeopleDevelopment();
                $tcPeopleDevelopment->status_1 = 2;
                $tcPeopleDevelopment->tahun_aktual = $currentYear + 1;
                $tcPeopleDevelopment->section = $section;
                $tcPeopleDevelopment->id_job_position = $data['new_id_job_position'][$index];
                $tcPeopleDevelopment->id_user = $data['new_id_user'][$index];
                // Set data lainnya
                $tcPeopleDevelopment->program_training_plan = $data['new_program_training_plan'][$index];
                $tcPeopleDevelopment->due_date_plan = $data['new_due_date_plan'][$index];
                $tcPeopleDevelopment->biaya_plan = $data['new_biaya_plan'][$index];
                $tcPeopleDevelopment->lembaga_plan = $data['new_lembaga_plan'][$index];
                $tcPeopleDevelopment->keterangan_plan = $data['new_keterangan_plan'][$index];
                $tcPeopleDevelopment->status_2 = $data['new_status_2'][$index];

                // Memeriksa dan menyimpan file jika ada
                if ($request->hasFile('new_file.' . $index)) {
                    $file = $request->file('new_file.' . $index);
                    $fileName = $file->getClientOriginalName();
                    $file->move(public_path('assets/people_development'), $fileName);
                    $tcPeopleDevelopment->file = $fileName;
                    $tcPeopleDevelopment->file_name = $fileName;
                }

                // Menyimpan modified_at berdasarkan section
                if (in_array($section, ['Sales Region I', 'Sales Region II'])) {
                    $tcPeopleDevelopment->modified_at = 'YULMAI RIDO WINANDA';
                } elseif (in_array($section, ['Sales Region III', 'Sales Region IV'])) {
                    $tcPeopleDevelopment->modified_at = 'ANDIK TOTOK SISWOYO';
                } elseif (in_array($section, ['Finance, Accounting', 'PDCA, HR, GA, Legal, Procurement, IT', 'HR, GA & Legal ', 'PDCA, Procurement, IT'])) {
                    $tcPeopleDevelopment->modified_at = 'MARTINUS CAHYO RAHASTO';
                } elseif ($section === 'Logistics') {
                    $tcPeopleDevelopment->modified_at = 'VITRI HANDAYANI';
                } elseif (in_array($section, ['PPC, Production CT', 'Production HT', 'Production MC & Machining Custom', 'Technical Support QC & Maintenance'])) {
                    $tcPeopleDevelopment->modified_at = 'ARY RODJO PRASETYO';
                }

                $tcPeopleDevelopment->modified_updated = $userName;
                $tcPeopleDevelopment->save();
            }
        }

        // Mengolah data lama (tidak ada perubahan dalam bagian ini)
        if (isset($data['existing_id'])) {
            foreach ($data['existing_id'] as $index => $id) {
                $tcPeopleDevelopment = TcPeopleDevelopment::find($id);
                if ($tcPeopleDevelopment) {
                    $tcPeopleDevelopment->section = $data['existing_section'][$index];
                    $tcPeopleDevelopment->id_job_position = $data['existing_id_job_position'][$index];
                    $tcPeopleDevelopment->id_user = $data['existing_id_user'][$index];
                    // Update data lainnya
                    $tcPeopleDevelopment->program_training = $data['existing_program_training'][$index];
                    $tcPeopleDevelopment->due_date = $data['existing_due_date'][$index];
                    $tcPeopleDevelopment->biaya = $data['existing_biaya'][$index];
                    $tcPeopleDevelopment->lembaga = $data['existing_lembaga'][$index];
                    $tcPeopleDevelopment->keterangan_tujuan = $data['existing_keterangan_tujuan'][$index];
                    $tcPeopleDevelopment->due_date_plan = $data['existing_due_date_plan'][$index];
                    $tcPeopleDevelopment->biaya_plan = $data['existing_biaya_plan'][$index];
                    $tcPeopleDevelopment->lembaga_plan = $data['existing_lembaga_plan'][$index];
                    $tcPeopleDevelopment->keterangan_plan = $data['existing_keterangan_plan'][$index];
                    $tcPeopleDevelopment->status_2 = $data['existing_status_2'][$index];
                    $tcPeopleDevelopment->program_training_plan = $data['existing_program_training_plan'][$index];

                    // Memeriksa dan menyimpan file jika ada
                    if ($request->hasFile('existing_file.' . $id)) {
                        $file = $request->file('existing_file.' . $id);
                        $fileName = $file->getClientOriginalName();
                        $file->move(public_path('assets/people_development'), $fileName);
                        $tcPeopleDevelopment->file = $fileName;
                        $tcPeopleDevelopment->file_name = $fileName;
                    }

                    $tcPeopleDevelopment->modified_updated = $userName;
                    $tcPeopleDevelopment->save();
                } else {
                    \Log::warning("Data not found for ID: $id");
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui']);
    }

    public function updateBtn(Request $request)
    {
        $status = $request->input('enabled') ? 1 : 0;

        // Simpan status ke database
        BtnStatus::updateOrCreate(
            ['id' => 1], // Misalkan hanya ada satu status yang diupdate, bisa disesuaikan
            ['status' => $status]
        );

        // Simpan status ke cache
        Cache::put('button_status', $status);

        return response()->json(['status' => 'success']);
    }

    public function downloadPDF($id)
    {
        // Find the record based on ID
        $data = TcPeopleDevelopment::find($id);

        // Check if the record exists
        if (!$data) {
            return redirect()->back()->withErrors('Data not found');
        }

        // Construct the file path
        $filePath = public_path('assets/people_development/' . $data->file);

        // Check if the file exists
        if (!file_exists($filePath)) {
            return redirect()->back()->withErrors('File not found');
        }

        // Return the file as a download response
        return response()->download($filePath, $data->file_name);
    }

    public function sendPD($modified_at)
    {
        // Mengupdate status menjadi 2 untuk semua entri dengan id_job_position yang sama
        TcPeopleDevelopment::where('modified_at', $modified_at)
            ->update(['status_1' => 2]);

        // Redirect atau kembali ke halaman yang diinginkan setelah update
        return redirect()->route('indexPD')->with('success', 'Status telah diubah menjadi Menunggu Persetujuan HRGA.');
    }

    public function sendPD2($tahun_aktual)
    {
        // Mengupdate status menjadi 3 untuk semua entri dengan tahun_aktual yang sama
        TcPeopleDevelopment::where('tahun_aktual', $tahun_aktual)
            ->update(['status_1' => 3]);

        // Redirect atau kembali ke halaman yang diinginkan setelah update
        return redirect()->route('indexPD2')->with('success', 'Status telah diubah menjadi Menunggu Persetujuan HRGA.');
    }

    public function getFilteredData(Request $request)
    {
        // Get the role ID from the request or default to the authenticated user's role ID
        $roleId = $request->input('role_id', auth()->user()->role_id); // Role ID filtering
        $yearEnd = $request->input('year', now()->year); // Default to the current year if not provided

        // Initialize the sections based on the role_id
        $sections = [];

        // Role 11: Finance, Accounting, HRGA, IT
        if ($roleId == 11) {
            $sections = [
                'Finance, Accounting',
                'PDCA, HR, GA, Legal, Procurement, IT',
                'Fin, Acc, Proc, HRGA & IT',
                'HR, GA & Legal',
                'PDCA, Procurement, IT',
            ];
        }

        // Role 5: Production-related sections
        if ($roleId == 5) {
            $sections = [
                'PPC, Production CT',
                'Production HT',
                'Production MC & Machining Custom',
                'Technical Support QC & Maintenance',
            ];
        }

        // Role 2: Sales-related sections
        if ($roleId == 2) {
            $sections = [
                'Sales Region I, II, III, IV',
                'Sales Region I, II',
                'Sales Region III, IV',
                'Sales Region II',
                'Sales Region I'
            ];
        }

        // Role 7: Logistics
        if ($roleId == 7) {
            $sections = [
                'Logistics'
            ];
        }

        // Role 1, 14, 15: Can access all sections
        if (in_array($roleId, [1, 14, 15])) {
            $sections = array_merge(
                [
                    'Finance, Accounting',
                    'PDCA, HR, GA, Legal, Procurement, IT',
                    'Fin, Acc, Proc, HRGA & IT',
                    'HR, GA & Legal',
                    'PDCA, Procurement, IT',
                ],
                [
                    'PPC, Production CT',
                    'Production HT',
                    'Production MC & Machining Custom',
                    'Technical Support QC & Maintenance',
                ],
                [
                    'Sales Region I, II, III, IV',
                    'Sales Region I, II',
                    'Sales Region III, IV',
                    'Sales Region II',
                    'Sales Region I'
                ],
                [
                    'Logistics'
                ]
            );
        }

        // Query the TcPeopleDevelopment model based on sections and optional filters
        $query = TcPeopleDevelopment::whereIn('section', $sections)
            ->where('status_2', 'Done') // Add condition for status_2 to be 'Done'
            ->with('user'); // Ensure the user relationship is loaded

        // Apply year range filter from 2000 to the selected year
        if ($yearEnd) {
            $query->whereBetween('created_at', ['2000-01-01', $yearEnd . '-12-31']);
        }

        // Execute the query and get the results
        $dataTcPeopleDevelopment = $query->get();

        // Return data as JSON
        return response()->json($dataTcPeopleDevelopment);
    }
}
