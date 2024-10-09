<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\MstPoPengajuan;
use App\Models\TrsPoPengajuan;

class PoPengajuanController extends Controller
{
    //
    // Method untuk menampilkan data ke view
    public function indexPoPengajuan()
    {
        // Mengambil semua data dari model MstPoPengajuan
        $data = MstPoPengajuan::all()
            ->unique('no_fpb'); // Mengambil data unik berdasarkan no_fpb

        // Mengirim data ke view
        return view('po_pengajuan.index_po_pengajuan', compact('data'));
    }

    public function indexPoDeptHead()
    {
        // Mendapatkan role_id dari pengguna yang sedang login
        $roleId = auth()->user()->role_id;

        // Array mapping antara role_id dan nama yang diizinkan untuk ditampilkan
        $allowedNames = [];

        // Logika pemilihan nama berdasarkan role_id
        if ($roleId == 11) {
            $allowedNames = ['JESSICA PAUNE', 'SITI MARIA ULFA', 'MUHAMMAD DINAR FARISI'];
        } elseif ($roleId == 5) {
            $allowedNames = ['MUGI PRAMONO', 'ABDUR RAHMAN AL FAAIZ', 'RAGIL ISHA RAHMANTO'];
        } elseif ($roleId == 2) {
            $allowedNames = ['ILHAM CHOLID', 'JUN JOHAMIN PD', 'HERY HERMAWAN'];
        } elseif ($roleId == 7) {
            $allowedNames = ['RANGGA FADILLAH'];
        }

        // Mengambil data dari model MstPoPengajuan berdasarkan role_id dan nama yang diperbolehkan
        if (!empty($allowedNames)) {
            $data = MstPoPengajuan::whereIn('modified_at', $allowedNames)
                ->get()
                ->unique('no_fpb'); // Menghapus duplikat berdasarkan no_fpb
        } else {
            // Jika role_id tidak sesuai dengan yang ditentukan, return data kosong atau handle sesuai kebutuhan
            $data = collect(); // atau bisa return redirect dengan pesan error
        }

        // Mengirim data ke view
        return view('po_pengajuan.index_po_deptHead', compact('data'));
    }

    public function indexPoUser()
    {
        // Mendapatkan role_id dari pengguna yang sedang login
        $roleId = auth()->user()->role_id;

        // Array untuk menyimpan kategori yang diperbolehkan untuk ditampilkan
        $allowedCategories = [];

        // Logika pemilihan kategori_po berdasarkan role_id
        if (in_array($roleId, [50, 30])) {
            // Untuk role_id 50 & 30, hanya menampilkan kategori Consumable, Subcon, Spareparts
            $allowedCategories = ['Consumable', 'Subcon', 'Spareparts'];
        } elseif (in_array($roleId, [40, 14])) {
            // Untuk role_id 40 & 14, hanya menampilkan kategori IT
            $allowedCategories = ['IT'];
        } elseif (in_array($roleId, [39, 14])) {
            // Untuk role_id 39 & 14, hanya menampilkan kategori GA
            $allowedCategories = ['GA'];
        }

        // Mengambil data dari model MstPoPengajuan berdasarkan role_id dan kategori_po yang diperbolehkan
        if (!empty($allowedCategories)) {
            // Mengambil data sesuai kategori_po yang diperbolehkan
            $data = MstPoPengajuan::whereIn('kategori_po', $allowedCategories)
                ->get()
                ->groupBy('no_fpb') // Mengelompokkan data berdasarkan no_fpb
                ->map(function ($group) {
                    // Mengambil hanya data pertama dari setiap grup
                    return $group->first();
                });
        } else {
            // Jika role_id tidak sesuai dengan yang ditentukan, return data kosong atau handle sesuai kebutuhan
            $data = collect(); // atau bisa return redirect dengan pesan error
        }
        // Mengirim data ke view
        return view('po_pengajuan.index_po_user', compact('data'));
    }

    public function indexPoFinance()
    {
        // Mengambil semua data dari MstPoPengajuan
        $data = MstPoPengajuan::all()
            ->unique('no_fpb'); // Mengambil data unik berdasarkan no_fpb

        // Mengirim data ke view
        return view('po_pengajuan.index_po_finance', compact('data'));
    }

    public function indexPoProcurement()
    {
        // Mengambil semua data dari MstPoPengajuan
        $data = MstPoPengajuan::all()
            ->unique('no_fpb'); // Mengambil data unik berdasarkan no_fpb

        // Mencari data yang memiliki catatan "Cancel Item"
        $pengajuanCancel = $data->filter(function ($item) {
            return strpos($item->catatan, 'Terdapat Cancel Item') !== false && $item->status_1 != 9;
        })->pluck('no_fpb')->toArray(); // Mengambil no_fpb untuk data yang memiliki catatan "Cancel Item"

        // Mengirim data ke view
        return view('po_pengajuan.index_po_procurment', compact('data', 'pengajuanCancel'));
    }

    public function indexPoProcurement2()
    {
        // Mengambil semua data dari MstPoPengajuan
        $data = MstPoPengajuan::all();

        // Mencari data yang memiliki catatan "Cancel Item"
        $pengajuanCancel = $data->filter(function ($item) {
            return strpos($item->catatan, 'Terdapat Cancel Item') !== false && $item->status_1 != 9;
        })->pluck('no_fpb')->toArray(); // Mengambil no_fpb untuk data yang memiliki catatan "Cancel Item"

        // Mengirim data ke view
        return view('po_pengajuan.index_po_procurment', compact('data', 'pengajuanCancel'));
    }

    public function showFPBForm($id)
    {
        // Mengambil data berdasarkan id
        $poPengajuan = MstPoPengajuan::find($id);

        if (!$poPengajuan) {
            return abort(404); // Jika data dengan ID tersebut tidak ditemukan
        }

        // Mengambil semua data berdasarkan no_fpb yang sama
        $mstPoPengajuans = MstPoPengajuan::where('no_fpb', $poPengajuan->no_fpb)->get();

        // Tentukan Dept. Head berdasarkan nilai modified_at
        $deptHead = '';
        if (in_array($poPengajuan->modified_at, ['JESSICA PAUNE', 'SITI MARIA ULFA', 'MUHAMMAD DINAR FARISI'])) {
            $deptHead = 'MARTINUS CAHYO RAHASTO';
        } elseif (in_array($poPengajuan->modified_at, ['MUGI PRAMONO', 'ABDUR RAHMAN AL FAAIZ', 'RAGIL ISHA RAHMANTO'])) {
            $deptHead = 'ARY RODJO PRASETYO';
        } elseif (in_array($poPengajuan->modified_at, ['ILHAM CHOLID', 'JUN JOHAMIN PD', 'HERY HERMAWAN'])) {
            $deptHead = 'YULMAI RIDO WINANDA';
        } elseif ($poPengajuan->modified_at == 'RANGGA FADILLAH') {
            $deptHead = 'VITRI HANDAYANI';
        }

        // Tentukan User Acc berdasarkan kategori_po
        $userAcc = '';
        if ($poPengajuan->kategori_po == 'IT') {
            $userAcc = 'IT: MEDI KRISNANTO';
        } elseif ($poPengajuan->kategori_po == 'GA') {
            $userAcc = 'GA: MUHAMMAD DINAR FARISI';
        } elseif (in_array($poPengajuan->kategori_po, ['Consumable', 'Subcon', 'Spareparts', 'Indirect Material'])) {
            $userAcc = 'Warehouse: NURSALIM';
        }

        // Mengirimkan data ke view
        return view('po_pengajuan.view_form_FPB', compact('mstPoPengajuans', 'deptHead', 'userAcc'));
    }

    public function showFPBProc($id)
    {
        // Mengambil data berdasarkan id
        $poPengajuan = MstPoPengajuan::find($id);

        if (!$poPengajuan) {
            return abort(404); // Jika data dengan ID tersebut tidak ditemukan
        }

        // Mengambil semua data berdasarkan no_fpb yang sama
        $mstPoPengajuans = MstPoPengajuan::where('no_fpb', $poPengajuan->no_fpb)->get();

        // Tentukan Dept. Head berdasarkan nilai modified_at
        $deptHead = '';
        if (in_array($poPengajuan->modified_at, ['JESSICA PAUNE', 'SITI MARIA ULFA', 'MUHAMMAD DINAR FARISI'])) {
            $deptHead = 'MARTINUS CAHYO RAHASTO';
        } elseif (in_array($poPengajuan->modified_at, ['MUGI PRAMONO', 'ABDUR RAHMAN AL FAAIZ', 'RAGIL ISHA RAHMANTO'])) {
            $deptHead = 'ARY RODJO PRASETYO';
        } elseif (in_array($poPengajuan->modified_at, ['ILHAM CHOLID', 'JUN JOHAMIN PD', 'HERY HERMAWAN'])) {
            $deptHead = 'YULMAI RIDO WINANDA';
        } elseif ($poPengajuan->modified_at == 'RANGGA FADILLAH') {
            $deptHead = 'VITRI HANDAYANI';
        }

        // Tentukan User Acc berdasarkan kategori_po
        $userAcc = '';
        if ($poPengajuan->kategori_po == 'IT') {
            $userAcc = 'MEDI KRISNANTO';
        } elseif ($poPengajuan->kategori_po == 'GA') {
            $userAcc = 'MUHAMMAD DINAR FARISI';
        } elseif (in_array($poPengajuan->kategori_po, ['Consumable', 'Subcon', 'Spareparts', 'Indirect Material'])) {
            $userAcc = 'NURSALIM';
        }

        // Mengirimkan data ke view
        return view('po_pengajuan.trs_po_procurment', compact('mstPoPengajuans', 'deptHead', 'userAcc'));
    }

    public function createPoPengajuan()
    {

        // Mengirim data ke view
        return view('po_pengajuan.create_po_pengajuan');
    }

    public function edit($id)
    {
        // Find the record by id
        $pengajuanPo = MstPoPengajuan::findOrFail($id);

        // Fetch all records with the same no_fpb
        $pengajuanPoList = MstPoPengajuan::where('no_fpb', $pengajuanPo->no_fpb)->get();

        return view('po_pengajuan.edit_po_pengajuan', compact('pengajuanPo', 'pengajuanPoList'));
    }

    public function editDept($id)
    {
        // Find the record by id
        $pengajuanPo = MstPoPengajuan::findOrFail($id);

        // Fetch all records with the same no_fpb
        $pengajuanPoList = MstPoPengajuan::where('no_fpb', $pengajuanPo->no_fpb)->get();

        return view('po_pengajuan.edit_po_deptHead', compact('pengajuanPo', 'pengajuanPoList'));
    }

    public function store(Request $request)
    {
        try {
            Log::info('Received request data for purchase order:', $request->all()); // Log all incoming data

            $validatedData = $request->validate([
                'kategori_po' => 'required|string',
                'nama_barang.*' => 'required|string',
                'spesifikasi.*' => 'required|string',
                'pcs.*' => 'required|integer',
                'file.*' => 'nullable|file|max:12048',
                'price_list.*' => 'nullable|numeric',
                'total_harga.*' => 'nullable|numeric',
                'target_cost.*' => 'nullable|numeric',
                'lead_time.*' => 'nullable|date',
                'rekomendasi.*' => 'nullable|string',
                'nama_customer.*' => 'nullable|string',
                'nama_project.*' => 'nullable|string',
            ]);

            Log::info('Validated data:', $validatedData); // Log the validated data

            // Generate no_fpb
            $currentYear = date('Y');
            $latestPo = MstPoPengajuan::whereYear('created_at', $currentYear)
                ->orderBy('id', 'desc')
                ->first();

            $newPoNumber = 1; // Default value if no existing PO
            if ($latestPo) {
                $lastPoNumber = (int)substr($latestPo->no_fpb, strrpos($latestPo->no_fpb, '/') + 1);
                $newPoNumber = $lastPoNumber + 1;
            }

            $no_fpb = 'FPB/' . $currentYear . '/' . str_pad($newPoNumber, 5, '0', STR_PAD_LEFT); // Format to 00001
            Log::info('Generated PO number: ' . $no_fpb); // Log the generated PO number

            // Check if the generated no_fpb already exists in the database
            while (MstPoPengajuan::where('no_fpb', $no_fpb)->exists()) {
                Log::warning('Duplicate PO number found, generating a new one.');
                // If it exists, generate a new no_fpb
                $newPoNumber++;
                $no_fpb = 'FPB/' . $currentYear . '/' . str_pad($newPoNumber, 5, '0', STR_PAD_LEFT);
                Log::info('Newly generated PO number: ' . $no_fpb); // Log the newly generated PO number
            }

            foreach ($validatedData['nama_barang'] as $index => $nama_barang) {
                $purchaseOrder = new MstPoPengajuan();
                $purchaseOrder->no_fpb = $no_fpb; // Store generated no_fpb
                $purchaseOrder->kategori_po = $validatedData['kategori_po'];
                $purchaseOrder->nama_barang = $nama_barang;
                $purchaseOrder->spesifikasi = $validatedData['spesifikasi'][$index];
                $purchaseOrder->pcs = $validatedData['pcs'][$index];
                $purchaseOrder->price_list = isset($validatedData['price_list'][$index]) ?
                    str_replace(',', '', $validatedData['price_list'][$index]) : null;

                // Menghitung total harga (pcs * price_list)
                if (isset($validatedData['pcs'][$index]) && isset($validatedData['price_list'][$index])) {
                    $purchaseOrder->total_harga = $validatedData['pcs'][$index] * str_replace(',', '', $validatedData['price_list'][$index]);
                } else {
                    $purchaseOrder->total_harga = null;
                }

                Log::info('Creating purchase order for item:', [
                    'no_fpb' => $no_fpb,
                    'kategori_po' => $validatedData['kategori_po'],
                    'nama_barang' => $nama_barang,
                    'spesifikasi' => $validatedData['spesifikasi'][$index],
                ]); // Removed qty reference from log

                // Handle file upload with UUID
                if ($request->hasFile('file.' . $index)) {
                    $file = $request->file('file.' . $index);
                    // Generate a unique file name
                    $hashedName = uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('assets/pre_order'), $hashedName);
                    $purchaseOrder->file = $hashedName; // Store the hashed file name
                    $purchaseOrder->file_name = $file->getClientOriginalName(); // Original file name (optional)
                    Log::info('Uploaded file for item:', [
                        'file_name' => $file->getClientOriginalName(),
                        'hashed_name' => $hashedName,
                    ]);
                }

                // Handle Subcon fields if they are set
                if ($validatedData['kategori_po'] === 'Subcon') {
                    $purchaseOrder->target_cost = isset($validatedData['target_cost'][$index]) ?
                        str_replace(',', '', $validatedData['target_cost'][$index]) : null;
                    $purchaseOrder->lead_time = $validatedData['lead_time'][$index] ?? null;
                    $purchaseOrder->rekomendasi = $validatedData['rekomendasi'][$index] ?? null;
                    $purchaseOrder->nama_customer = $validatedData['nama_customer'][$index] ?? null;
                    $purchaseOrder->nama_project = $validatedData['nama_project'][$index] ?? null;

                    Log::info('Subcon fields for item:', [
                        'target_cost' => $purchaseOrder->target_cost,
                        'lead_time' => $purchaseOrder->lead_time,
                        'rekomendasi' => $purchaseOrder->rekomendasi,
                        'nama_customer' => $purchaseOrder->nama_customer,
                        'nama_project' => $purchaseOrder->nama_project,
                    ]);
                }

                $purchaseOrder->status_1 = 1;
                $purchaseOrder->modified_at = $request->user()->name;

                // Attempt to save the purchase order
                try {
                    $purchaseOrder->save();
                    Log::info('Purchase order saved successfully:', ['no_fpb' => $no_fpb]);
                } catch (\Exception $e) {
                    Log::error('Failed to save purchase order: ' . $e->getMessage(), [
                        'no_fpb' => $no_fpb,
                        'data' => json_encode($purchaseOrder->toArray()), // Convert to string for logging
                    ]);
                    return redirect()->route('index.PO')->with('error', 'Data failed to save: ' . $e->getMessage());
                }
            }

            return redirect()->route('index.PO')->with('success', 'Data successfully saved!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed: ' . json_encode($e->errors())); // Convert errors to string
            return redirect()->route('index.PO')->with('error', 'Validation failed: ' . implode(', ', $e->errors()));
        } catch (\Exception $e) {
            Log::error('Unexpected error: ' . $e->getMessage());
            return redirect()->route('index.PO')->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        // Retrieve PO submission data by ID
        $pengajuanPo = MstPoPengajuan::findOrFail($id);

        // Update MstPoPengajuan data (kategori_po column)
        $pengajuanPo->kategori_po = $request->kategori_po;
        $pengajuanPo->save();

        // Loop to update or add new items via addRow
        if ($request->has('id')) {
            foreach ($request->id as $index => $itemId) {
                if (!empty($itemId)) {
                    // If item has an ID, update the existing data
                    $pengajuanPoItem = MstPoPengajuan::find($itemId);
                    if ($pengajuanPoItem) {
                        $pengajuanPoItem->kategori_po = $pengajuanPo->kategori_po; // Update kategori_po
                        $pengajuanPoItem->nama_barang = $request->nama_barang[$index];
                        $pengajuanPoItem->spesifikasi = $request->spesifikasi[$index];
                        $pengajuanPoItem->pcs = $request->pcs[$index];
                        $pengajuanPoItem->price_list = $request->price_list[$index];
                        $pengajuanPoItem->total_harga = $request->pcs[$index] * $request->price_list[$index];

                        // Handle file uploads
                        if ($request->hasFile('file.' . $index)) {
                            if ($pengajuanPoItem->file) {
                                Storage::delete('public/assets/pre_order/' . $pengajuanPoItem->file);
                            }
                            $file = $request->file('file.' . $index);
                            $hashedName = uniqid() . '.' . $file->getClientOriginalExtension();
                            $file->move(public_path('assets/pre_order'), $hashedName);

                            $pengajuanPoItem->file = $hashedName;
                            $pengajuanPoItem->file_name = $file->getClientOriginalName();
                        }

                        // Special handling for Subcon category
                        if ($pengajuanPo->kategori_po === 'Subcon') {
                            $pengajuanPoItem->target_cost = $request->target_cost[$index] ?? null;
                            $pengajuanPoItem->lead_time = $request->lead_time[$index] ?? null;
                            $pengajuanPoItem->rekomendasi = $request->rekomendasi[$index] ?? null;
                            $pengajuanPoItem->nama_customer = $request->nama_customer[$index] ?? null;
                            $pengajuanPoItem->nama_project = $request->nama_project[$index] ?? null;
                        }

                        $pengajuanPoItem->save();
                    }
                } else {
                    // Add new items if no ID is provided
                    $pengajuanPoItem = new MstPoPengajuan();
                    $pengajuanPoItem->no_fpb = $pengajuanPo->no_fpb;
                    $pengajuanPoItem->kategori_po = $pengajuanPo->kategori_po;
                    $pengajuanPoItem->nama_barang = $request->nama_barang[$index];
                    $pengajuanPoItem->spesifikasi = $request->spesifikasi[$index];
                    $pengajuanPoItem->pcs = $request->pcs[$index];
                    $pengajuanPoItem->price_list = $request->price_list[$index];
                    $pengajuanPoItem->total_harga = $request->pcs[$index] * $request->price_list[$index];
                    $pengajuanPoItem->status_1 = 1;
                    $pengajuanPoItem->modified_at = $request->user()->name;

                    // Handle file upload for new item
                    if ($request->hasFile('file.' . $index)) {
                        $file = $request->file('file.' . $index);
                        $hashedName = uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('assets/pre_order'), $hashedName);

                        $pengajuanPoItem->file = $hashedName;
                        $pengajuanPoItem->file_name = $file->getClientOriginalName();
                    }

                    // Special handling for Subcon category
                    if ($pengajuanPo->kategori_po === 'Subcon') {
                        $pengajuanPoItem->target_cost = $request->target_cost[$index] ?? null;
                        $pengajuanPoItem->lead_time = $request->lead_time[$index] ?? null;
                        $pengajuanPoItem->rekomendasi = $request->rekomendasi[$index] ?? null;
                        $pengajuanPoItem->nama_customer = $request->nama_customer[$index] ?? null;
                        $pengajuanPoItem->nama_project = $request->nama_project[$index] ?? null;
                    }

                    $pengajuanPoItem->save();
                }
            }
        }

        return redirect()->route('index.PO')->with('success', 'Data PO berhasil diperbarui.');
    }

    public function deletePoPengajuan($id)
    {
        $pengajuan = MstPoPengajuan::find($id);

        if (!$pengajuan) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        $pengajuan->delete();

        return response()->json(['message' => 'Data deleted successfully']);
    }

    public function updateDept(Request $request, $id)
    {
        // Retrieve PO submission data by ID
        $pengajuanPo = MstPoPengajuan::findOrFail($id);

        // Update MstPoPengajuan data (kategori_po column)
        $pengajuanPo->kategori_po = $request->kategori_po;
        $pengajuanPo->save();

        // Loop to update or add new items via addRow
        if ($request->has('id')) {
            foreach ($request->id as $index => $itemId) {
                if (!empty($itemId)) {
                    // If item has an ID, update the existing data
                    $pengajuanPoItem = MstPoPengajuan::find($itemId);
                    if ($pengajuanPoItem) {
                        $pengajuanPoItem->kategori_po = $pengajuanPo->kategori_po; // Update kategori_po
                        $pengajuanPoItem->nama_barang = $request->nama_barang[$index];
                        $pengajuanPoItem->spesifikasi = $request->spesifikasi[$index];
                        $pengajuanPoItem->pcs = $request->pcs[$index];
                        $pengajuanPoItem->price_list = $request->price_list[$index];
                        $pengajuanPoItem->total_harga = $request->pcs[$index] * $request->price_list[$index];

                        // Handle file uploads
                        if ($request->hasFile('file.' . $index)) {
                            if ($pengajuanPoItem->file) {
                                Storage::delete('public/assets/pre_order/' . $pengajuanPoItem->file);
                            }
                            $file = $request->file('file.' . $index);
                            $hashedName = uniqid() . '.' . $file->getClientOriginalExtension();
                            $file->move(public_path('assets/pre_order'), $hashedName);

                            $pengajuanPoItem->file = $hashedName;
                            $pengajuanPoItem->file_name = $file->getClientOriginalName();
                        }

                        // Special handling for Subcon category
                        if ($pengajuanPo->kategori_po === 'Subcon') {
                            $pengajuanPoItem->target_cost = $request->target_cost[$index] ?? null;
                            $pengajuanPoItem->lead_time = $request->lead_time[$index] ?? null;
                            $pengajuanPoItem->rekomendasi = $request->rekomendasi[$index] ?? null;
                            $pengajuanPoItem->nama_customer = $request->nama_customer[$index] ?? null;
                            $pengajuanPoItem->nama_project = $request->nama_project[$index] ?? null;
                        }

                        $pengajuanPoItem->save();
                    }
                } else {
                    // Add new items if no ID is provided
                    $pengajuanPoItem = new MstPoPengajuan();
                    $pengajuanPoItem->no_fpb = $pengajuanPo->no_fpb;
                    $pengajuanPoItem->kategori_po = $pengajuanPo->kategori_po;
                    $pengajuanPoItem->nama_barang = $request->nama_barang[$index];
                    $pengajuanPoItem->spesifikasi = $request->spesifikasi[$index];
                    $pengajuanPoItem->pcs = $request->pcs[$index];
                    $pengajuanPoItem->price_list = $request->price_list[$index];
                    $pengajuanPoItem->total_harga = $request->pcs[$index] * $request->price_list[$index]; // Hitung total harga
                    $pengajuanPoItem->status_1 = 1;
                    $pengajuanPoItem->modified_at = $pengajuanPo->modified_at;

                    // Handle file upload for new item
                    if ($request->hasFile('file.' . $index)) {
                        $file = $request->file('file.' . $index);
                        $hashedName = uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('assets/pre_order'), $hashedName);

                        $pengajuanPoItem->file = $hashedName;
                        $pengajuanPoItem->file_name = $file->getClientOriginalName();
                    }

                    // Special handling for Subcon category
                    if ($pengajuanPo->kategori_po === 'Subcon') {
                        $pengajuanPoItem->target_cost = $request->target_cost[$index] ?? null;
                        $pengajuanPoItem->lead_time = $request->lead_time[$index] ?? null;
                        $pengajuanPoItem->rekomendasi = $request->rekomendasi[$index] ?? null;
                        $pengajuanPoItem->nama_customer = $request->nama_customer[$index] ?? null;
                        $pengajuanPoItem->nama_project = $request->nama_project[$index] ?? null;
                    }

                    $pengajuanPoItem->save();
                }
            }
        }

        return redirect()->route('index.PO.Dept')->with('success', 'Data PO berhasil diperbarui.');
    }

    public function updateStatusByNoFPB($no_fpb)
    {
        // Log no_fpb yang diterima
        \Log::info('Received no_fpb: ' . $no_fpb);

        // Ubah kembali tanda minus (-) menjadi garis miring (/)
        $no_fpb = str_replace('-', '/', $no_fpb);
        \Log::info('Transformed no_fpb: ' . $no_fpb);

        // Cari data berdasarkan no_fpb yang telah diubah
        $pengajuanList = MstPoPengajuan::where('no_fpb', $no_fpb)->get();

        if ($pengajuanList->isEmpty()) {
            \Log::error('No data found for no_fpb: ' . $no_fpb); // Log jika data tidak ditemukan
            return redirect()->back()->with('error', 'Data not found');
        }

        // Update status data yang sesuai
        foreach ($pengajuanList as $pengajuan) {
            $pengajuan->update(['status_1' => 2]);

            // Tambah data ke dalam model TrsPoPengajuan untuk setiap id yang ditemukan
            TrsPoPengajuan::create([
                'id_fpb' => $pengajuan->id,
                'nama_barang' => $pengajuan->nama_barang, // Menggunakan id dari setiap data MstPoPengajuan
                'modified_at' => auth()->user()->name, // Menyimpan nama user yang login
                'status' => 2,
            ]);
        }

        return redirect()->back()->with('success', 'Status updated successfully for no_fpb: ' . $no_fpb);
    }

    public function updateStatusByDeptHead($no_fpb)
    {
        // Log no_fpb yang diterima
        \Log::info('Received no_fpb: ' . $no_fpb);

        // Ubah kembali tanda minus (-) menjadi garis miring (/)
        $no_fpb = str_replace('-', '/', $no_fpb);
        \Log::info('Transformed no_fpb: ' . $no_fpb);

        // Cari data berdasarkan no_fpb yang telah diubah
        $pengajuanList = MstPoPengajuan::where('no_fpb', $no_fpb)->get();

        if ($pengajuanList->isEmpty()) {
            \Log::error('No data found for no_fpb: ' . $no_fpb); // Log jika data tidak ditemukan
            return redirect()->back()->with('error', 'Data not found');
        }

        // Update status data yang sesuai
        foreach ($pengajuanList as $pengajuan) {
            $pengajuan->update(['status_1' => 3]);

            // Tambah data ke dalam model TrsPoPengajuan untuk setiap id yang ditemukan
            TrsPoPengajuan::create([
                'id_fpb' => $pengajuan->id,
                'nama_barang' => $pengajuan->nama_barang, // Menggunakan id dari setiap data MstPoPengajuan
                'modified_at' => auth()->user()->name, // Menyimpan nama user yang login
                'status' => 3,
            ]);
        }

        return redirect()->back()->with('success', 'Status updated successfully for no_fpb: ' . $no_fpb);
    }

    public function updateStatusByUser($no_fpb)
    {
        // Log no_fpb yang diterima
        \Log::info('Received no_fpb: ' . $no_fpb);

        // Ubah kembali tanda minus (-) menjadi garis miring (/)
        $no_fpb = str_replace('-', '/', $no_fpb);
        \Log::info('Transformed no_fpb: ' . $no_fpb);

        // Cari data berdasarkan no_fpb yang telah diubah
        $pengajuanList = MstPoPengajuan::where('no_fpb', $no_fpb)->get();

        if ($pengajuanList->isEmpty()) {
            \Log::error('No data found for no_fpb: ' . $no_fpb); // Log jika data tidak ditemukan
            return redirect()->back()->with('error', 'Data not found');
        }

        // Update status data yang sesuai
        foreach ($pengajuanList as $pengajuan) {
            $pengajuan->update(['status_1' => 4]);

            // Tambah data ke dalam model TrsPoPengajuan untuk setiap id yang ditemukan
            TrsPoPengajuan::create([
                'id_fpb' => $pengajuan->id,
                'nama_barang' => $pengajuan->nama_barang, // Menggunakan id dari setiap data MstPoPengajuan
                'modified_at' => auth()->user()->name, // Menyimpan nama user yang login
                'status' => 4,
            ]);
        }

        return redirect()->back()->with('success', 'Status updated successfully for no_fpb: ' . $no_fpb);
    }

    public function updateStatusByFinance($no_fpb)
    {
        // Log no_fpb yang diterima
        \Log::info('Received no_fpb: ' . $no_fpb);

        // Ubah kembali tanda minus (-) menjadi garis miring (/)
        $no_fpb = str_replace('-', '/', $no_fpb);
        \Log::info('Transformed no_fpb: ' . $no_fpb);

        // Cari data berdasarkan no_fpb yang telah diubah
        $pengajuanList = MstPoPengajuan::where('no_fpb', $no_fpb)->get();

        if ($pengajuanList->isEmpty()) {
            \Log::error('No data found for no_fpb: ' . $no_fpb); // Log jika data tidak ditemukan
            return redirect()->back()->with('error', 'Data not found');
        }

        // Update status data yang sesuai
        foreach ($pengajuanList as $pengajuan) {
            $pengajuan->update(['status_1' => 5]);

            // Tambah data ke dalam model TrsPoPengajuan untuk setiap id yang ditemukan
            TrsPoPengajuan::create([
                'id_fpb' => $pengajuan->id,
                'nama_barang' => $pengajuan->nama_barang, // Menggunakan id dari setiap data MstPoPengajuan
                'modified_at' => auth()->user()->name, // Menyimpan nama user yang login
                'status' => 5,
            ]);
        }

        return redirect()->back()->with('success', 'Status updated successfully for no_fpb: ' . $no_fpb);
    }

    public function updateStatusByProcurement($no_fpb)
    {
        // Log no_fpb yang diterima
        \Log::info('Received no_fpb: ' . $no_fpb);

        // Ubah kembali tanda minus (-) menjadi garis miring (/)
        $no_fpb = str_replace('-', '/', $no_fpb);
        \Log::info('Transformed no_fpb: ' . $no_fpb);

        // Cari data berdasarkan no_fpb yang telah diubah
        $pengajuanList = MstPoPengajuan::where('no_fpb', $no_fpb)->get();

        if ($pengajuanList->isEmpty()) {
            \Log::error('No data found for no_fpb: ' . $no_fpb); // Log jika data tidak ditemukan
            return redirect()->back()->with('error', 'Data not found');
        }

        // Update status data yang sesuai
        foreach ($pengajuanList as $pengajuan) {
            $pengajuan->update(['status_1' => 6]);

            // Tambah data ke dalam model TrsPoPengajuan untuk setiap id yang ditemukan
            TrsPoPengajuan::create([
                'id_fpb' => $pengajuan->id,
                'nama_barang' => $pengajuan->nama_barang, // Menggunakan id dari setiap data MstPoPengajuan
                'modified_at' => auth()->user()->name, // Menyimpan nama user yang login
                'status' => 6,
            ]);
        }

        return redirect()->back()->with('success', 'Status updated successfully for no_fpb: ' . $no_fpb);
    }

    public function updateConfirmByProcurment($id)
    {
        // Validate the incoming request
        request()->validate([
            'keterangan' => 'required|string',
            'no_po' => 'nullable|string', // Tambahkan validasi opsional untuk no_po
        ]);

        // Log id yang diterima
        \Log::info('Received id: ' . $id);

        // Cari data berdasarkan id
        $pengajuan = MstPoPengajuan::find($id);

        if (!$pengajuan) {
            \Log::error('No data found for id: ' . $id); // Log jika data tidak ditemukan
            return response()->json(['message' => 'Data not found'], 404);
        }

        // Jika no_po diisi, ubah status menjadi 7
        if (request()->filled('no_po')) {
            \Log::info('No PO filled for id: ' . $id);
            $pengajuan->update([
                'no_po' => request('no_po'),
                'status_1' => 7, // Ubah status menjadi 7
                'status_2' => 7,
            ]);
        } else {
            // Jika no_po tidak diisi, tetap simpan keterangan dan status
            $pengajuan->update([
                'status_1' => 6,
                'status_2' => 6, // Ubah status menjadi 6
            ]);
        }

        // Save the keterangan in TrsPoPengajuan
        TrsPoPengajuan::create([
            'id_fpb' => $pengajuan->id,
            'nama_barang' => $pengajuan->nama_barang,
            'modified_at' => auth()->user()->name, // Menyimpan nama user yang login
            'keterangan' => request('keterangan'), // Get keterangan from request body
            'status' => $pengajuan->status_1, // Simpan status yang terbaru
        ]);

        return response()->json(['message' => 'Status updated successfully for id: ' . $id]);
    }

    public function updateCancelByProcurment($id)
    {
        // Validate the incoming request
        request()->validate([
            'keterangan' => 'required|string',
            'no_po' => 'nullable|string', // Tambahkan validasi opsional untuk no_po
        ]);

        // Log id yang diterima
        \Log::info('Received id: ' . $id);

        // Cari data berdasarkan id
        $pengajuan = MstPoPengajuan::find($id);

        if (!$pengajuan) {
            \Log::error('No data found for id: ' . $id); // Log jika data tidak ditemukan
            return response()->json(['message' => 'Data not found'], 404);
        }

        // Jika no_po diisi, ubah status menjadi 7
        if (request()->filled('no_po')) {
            \Log::info('No PO filled for id: ' . $id);
            $pengajuan->update([
                'no_po' => request('no_po'),
                'status_1' => 10, // Ubah status menjadi 7
                'status_2' => 10,
            ]);
        } else {
            // Jika no_po tidak diisi, tetap simpan keterangan dan status
            $pengajuan->update([
                'status_1' => 10,
                'status_2' => 10, // Ubah status menjadi 6
            ]);
        }

        // Save the keterangan in TrsPoPengajuan
        TrsPoPengajuan::create([
            'id_fpb' => $pengajuan->id,
            'nama_barang' => $pengajuan->nama_barang,
            'modified_at' => auth()->user()->name, // Menyimpan nama user yang login
            'keterangan' => request('keterangan'), // Get keterangan from request body
            'status' => $pengajuan->status_1, // Simpan status yang terbaru
        ]);

        return response()->json(['message' => 'Status updated successfully for id: ' . $id]);
    }

    public function updateCancelBySecHead($id)
    {
        // Validate the incoming request
        request()->validate([
            'keterangan' => 'required|string',
            'no_po' => 'nullable|string', // Tambahkan validasi opsional untuk no_po
        ]);

        // Log id yang diterima
        \Log::info('Received id: ' . $id);

        // Cari data berdasarkan id
        $pengajuan = MstPoPengajuan::find($id);

        if (!$pengajuan) {
            \Log::error('No data found for id: ' . $id); // Log jika data tidak ditemukan
            return response()->json(['message' => 'Data not found'], 404);
        }

        // Update status hanya untuk data dengan ID tertentu
        if (request()->filled('no_po')) {
            \Log::info('No PO filled for id: ' . $id);
            $pengajuan->update([
                'status_1' => 11, // Ubah status menjadi 11 (spesifik untuk ID ini saja)
                'status_2' => 11,
            ]);
        } else {
            $pengajuan->update([
                'status_1' => 11, // Ubah status menjadi 10 (spesifik untuk ID ini saja)
                'status_2' => 11,
            ]);
        }

        // Cari semua data yang memiliki no_fpb yang sama dan update catatan saja
        MstPoPengajuan::where('no_fpb', $pengajuan->no_fpb)
            ->update(['catatan' => 'Terdapat Cancel Item']); // Update catatan untuk semua entri dengan no_fpb yang sama

        // Save the keterangan in TrsPoPengajuan untuk baris yang diperbarui
        TrsPoPengajuan::create([
            'id_fpb' => $pengajuan->id,
            'nama_barang' => $pengajuan->nama_barang,
            'modified_at' => auth()->user()->name, // Menyimpan nama user yang login
            'keterangan' => request('keterangan'), // Get keterangan from request body
            'status' => $pengajuan->status_1, // Simpan status yang terbaru
        ]);

        return response()->json(['message' => 'Status updated successfully for id: ' . $id . ' and catatan updated for no_fpb: ' . $pengajuan->no_fpb]);
    }

    public function updateFinishByProcurment($no_fpb)
    {
        // Log no_fpb yang diterima
        \Log::info('Received no_fpb: ' . $no_fpb);

        // Ubah kembali tanda minus (-) menjadi garis miring (/)
        $no_fpb = str_replace('-', '/', $no_fpb);
        \Log::info('Transformed no_fpb: ' . $no_fpb);

        // Cari data berdasarkan no_fpb yang telah diubah
        $pengajuanList = MstPoPengajuan::where('no_fpb', $no_fpb)->get();

        if ($pengajuanList->isEmpty()) {
            \Log::error('No data found for no_fpb: ' . $no_fpb); // Log jika data tidak ditemukan
            return redirect()->back()->with('error', 'Data not found');
        }

        // Update status data yang sesuai
        foreach ($pengajuanList as $pengajuan) {
            $pengajuan->update([
                'status_1' => 9,
                'catatan' => '', // Atau gunakan '' untuk string kosong
            ]);

            // Tambah data ke dalam model TrsPoPengajuan untuk setiap id yang ditemukan
            TrsPoPengajuan::create([
                'id_fpb' => $pengajuan->id,
                'nama_barang' => $pengajuan->nama_barang, // Menggunakan id dari setiap data MstPoPengajuan
                'modified_at' => auth()->user()->name, // Menyimpan nama user yang login
                'status' => 9,
            ]);
        }

        return redirect()->route('index.PO.procurement')->with('success', 'Status updated successfully for no_fpb: ' . $no_fpb);
    }

    public function downloadFile($id)
    {
        // Cari data berdasarkan ID
        $mstPoPengajuan = MstPoPengajuan::find($id);

        if (!$mstPoPengajuan) {
            return abort(404, 'File tidak ditemukan.');
        }

        // Dapatkan nama file dari model
        $fileName = $mstPoPengajuan->file;

        if (!$fileName) {
            return abort(404, 'Tidak ada file yang terlampir.');
        }

        // Tentukan path file di direktori public/assets/pre_order
        $filePath = public_path('assets/pre_order/' . $fileName);

        // Cek apakah file ada di server
        if (!file_exists($filePath)) {
            return abort(404, 'File tidak ditemukan di server.');
        }

        // Kembalikan file sebagai response download
        return response()->download($filePath, $fileName);
    }

    public function getPoHistory($id)
    {
        $history = DB::table('mst_po_pengajuans as mst')
            ->join('trs_po_pengajuans as trs', 'mst.id', '=', 'trs.id_fpb')
            ->select('mst.no_fpb', 'mst.no_po', 'mst.nama_barang', 'trs.keterangan', 'trs.status', 'trs.modified_at', 'trs.created_at')
            ->where('mst.id', $id)
            ->orderBy('trs.created_at', 'desc') // Urutkan berdasarkan modified_at secara descending
            ->get();

        return response()->json(['data' => $history]);
    }
}
