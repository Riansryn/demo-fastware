<?php

namespace App\Http\Controllers;

use App\Models\Customer; // Impor model InquirySales
use App\Models\DetailInquiry;
use App\Models\InquirySales;
use App\Models\TypeMaterial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InquirySalesController extends Controller
{
    public function createInquirySales()
    {
        $statuses = [0, 1, 2, 3, 1, 4, 5, 6, 7];
        $inquiries = InquirySales::with('customer')
            ->whereIn('status', $statuses)
            ->orderByRaw('FIELD(status, 2, 3, 1, 0, 4, 5, 6, 7)')
            ->get()
            ->unique('kode_inquiry');

        $customers = Customer::all(); // Ambil semua data pelanggan

        return view('inquiry.create', compact('inquiries', 'customers'));
    }

    public function showFormSS($id)
    {
        $inquiry = InquirySales::with('details.type_materials')->findOrFail($id);

        // Fetch all detail inquiries based on id_inquiry from the main inquiry
        $materials = DetailInquiry::where('id_inquiry', $inquiry->id)->with('type_materials')->get();

        $typeMaterials = TypeMaterial::all(); // Ambil semua data TypeMaterial, sesuaikan dengan kebutuhan

        return view('inquiry.showFormSS', compact('inquiry', 'materials', 'typeMaterials'));
    }

    public function historyFormSS($id)
    {
        $inquiry = InquirySales::with('details.type_materials')->findOrFail($id);

        // Fetch all detail inquiries based on id_inquiry from the main inquiry
        $materials = DetailInquiry::where('id_inquiry', $inquiry->id)->with('type_materials')->get();

        $typeMaterials = TypeMaterial::all(); // Ambil semua data TypeMaterial, sesuaikan dengan kebutuhan

        return view('inquiry.historyFormSS', compact('inquiry', 'materials', 'typeMaterials'));
    }

    public function konfirmInquiry()
    {
        $statuses = [3, 1, 4, 5, 6, 7];
        $inquiries = InquirySales::with('customer', 'details.type_materials')
            ->whereIn('status', $statuses)
            ->orderByRaw('FIELD(status, 3, 1, 4, 5, 6, 7)')
            ->get();

        // Ambil semua data TypeMaterial, sesuaikan dengan kebutuhan
        $typeMaterials = TypeMaterial::all();
        $customers = Customer::all(); // Ambil semua data pelanggan

        // Variabel untuk menyimpan semua detail inquiries
        $allMaterials = [];

        // Loop untuk setiap inquiry dalam $inquiries
        foreach ($inquiries as $inquiry) {
            // Fetch all detail inquiries based on id_inquiry from the main inquiry
            $materials = DetailInquiry::where('id_inquiry', $inquiry->id)
                ->with('type_materials')
                ->get();

            // Tambahkan ke dalam array $allMaterials
            $allMaterials[$inquiry->id] = $materials;
        }

        return view('inquiry.konfirmInquiry', compact('inquiries', 'allMaterials', 'typeMaterials', 'customers'));
    }

    public function validasiInquiry()
    {
        $statuses = [3, 1, 4, 5, 6, 7];
        $inquiries = InquirySales::with('customer', 'details.type_materials')
            ->whereIn('status', $statuses)
            ->orderByRaw('FIELD(status, 3, 1, 4, 5, 6, 7)')
            ->get();

        // Ambil semua data TypeMaterial, sesuaikan dengan kebutuhan
        $typeMaterials = TypeMaterial::all();
        $customers = Customer::all(); // Ambil semua data pelanggan

        // Variabel untuk menyimpan semua detail inquiries
        $allMaterials = [];

        // Loop untuk setiap inquiry dalam $inquiries
        foreach ($inquiries as $inquiry) {
            // Fetch all detail inquiries based on id_inquiry from the main inquiry
            $materials = DetailInquiry::where('id_inquiry', $inquiry->id)
                ->with('type_materials')
                ->get();

            // Tambahkan ke dalam array $allMaterials
            $allMaterials[$inquiry->id] = $materials;
        }

        return view('inquiry.validasi', compact('inquiries', 'allMaterials', 'typeMaterials', 'customers'));
    }

    public function reportInquiry()
    {
        $statuses = [1, 3, 4, 5, 6, 7];
        $inquiries = InquirySales::with('customer', 'details.type_materials')
            ->whereIn('status', $statuses)
            ->orderByRaw('FIELD(status, 3, 4, 1, 5, 6, 7)')
            ->get();

        // Ambil semua data TypeMaterial, sesuaikan dengan kebutuhan
        $typeMaterials = TypeMaterial::all();
        $customers = Customer::all(); // Ambil semua data pelanggan

        // Variabel untuk menyimpan semua detail inquiries
        $allMaterials = [];

        // Loop untuk setiap inquiry dalam $inquiries
        foreach ($inquiries as $inquiry) {
            // Fetch all detail inquiries based on id_inquiry from the main inquiry
            $materials = DetailInquiry::where('id_inquiry', $inquiry->id)
                ->with('type_materials')
                ->get();

            // Tambahkan ke dalam array $allMaterials
            $allMaterials[$inquiry->id] = $materials;
        }

        return view('inquiry.report', compact('inquiries'));
    }

    public function formulirInquiry($id)
    {
        $inquiry = InquirySales::with('details.type_materials')->findOrFail($id);
        $materials = DetailInquiry::where('id_inquiry', $inquiry->id)->with('type_materials')->get();
        $typeMaterials = TypeMaterial::all();

        return view('inquiry.formulirInquiry', compact('inquiry', 'materials', 'typeMaterials'));
    }

    public function tindakLanjutInquiry($id)
    {
        $inquiry = InquirySales::with('details.type_materials')->findOrFail($id);
        $materials = DetailInquiry::where('id_inquiry', $inquiry->id)->with('type_materials')->get();
        $typeMaterials = TypeMaterial::all();

        return view('inquiry.tindakLanjutInquiry', compact('inquiry', 'materials', 'typeMaterials'));
    }

    public function storeInquirySales(Request $request)
    {
        $request->validate([
            'jenis_inquiry' => 'required',
            'id_customer' => 'required',
        ]);

        // Generate inquiry code
        $jenisInquiry = $request->jenis_inquiry;
        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');

        // Check if there's an existing inquiry with the same combination
        $lastInquiry = InquirySales::where('jenis_inquiry', $jenisInquiry)
            ->where('id_customer', $request->id_customer)
            ->first();

        if ($lastInquiry) {
            return redirect()->route('createinquiry')->with('info', 'Inquiry with the same combination already exists.');
        } else {
            $lastKodeInquiry = InquirySales::where('jenis_inquiry', $jenisInquiry)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->orderBy('kode_inquiry', 'desc')
                ->first();

            $nextNumber = 1;
            if ($lastKodeInquiry) {
                $lastKodeParts = explode('/', $lastKodeInquiry->kode_inquiry);
                $nextNumber = intval(end($lastKodeParts)) + 1;
            }

            $kodeInquiry = sprintf('%s/%02d/%04d/%03d', $jenisInquiry, $currentMonth, $currentYear, $nextNumber);

            $inquiry = new InquirySales();
            $inquiry->kode_inquiry = $kodeInquiry;
            $inquiry->jenis_inquiry = $jenisInquiry;
            $inquiry->id_customer = $request->id_customer;
            $inquiry->to_approve = 'Waiting';
            $inquiry->to_validate = 'Waiting';
            $inquiry->status = 2;
            $inquiry->create_by = Auth::user()->name;
            $inquiry->save();
        }

        return redirect()->route('createinquiry')->with('success', 'Inquiry successfully saved.');
    }

    public function editInquiry($id)
    {
        $inquiry = InquirySales::find($id);
        $customers = Customer::all();

        if (!$inquiry) {
            return response()->json(['error' => 'Inquiry not found'], 404);
        }

        return response()->json([
            'id' => $inquiry->id,
            'kode_inquiry' => $inquiry->kode_inquiry,
            'jenis_inquiry' => $inquiry->jenis_inquiry,
            'id_customer' => $inquiry->id_customer,
            'customer_name' => $inquiry->customer->name_customer, // Assuming a relation is set up
            'customers' => $customers,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_inquiry' => 'required',
            'id_customer' => 'required',
        ]);

        $inquiry = InquirySales::findOrFail($id);

        // Generate kode inquiry
        $jenisInquiry = $request->jenis_inquiry; // RO atau SPOR
        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');
        $lastInquiry = InquirySales::where('jenis_inquiry', $jenisInquiry)->orderBy('id', 'desc')->first();
        $nextNumber = $lastInquiry ? intval(substr($lastInquiry->kode_inquiry, -3)) + 1 : 1;
        $kodeInquiry = sprintf('%s/%02d/%04d/%03d', $jenisInquiry, $currentMonth, $currentYear, $nextNumber);

        $inquiry->kode_inquiry = $kodeInquiry;
        $inquiry->to_approve = 'Waiting';
        $inquiry->to_validate = 'Waiting';
        $inquiry->status = 2;

        // Assign the name of the logged-in user to the create_by field
        $inquiry->create_by = Auth::user()->name;

        $inquiry->update($request->except('order_from')); // Update excluding order_from

        return redirect()->route('createinquiry')->with('success', 'Inquiry updated successfully');
    }

    public function delete($id)
    {
        // Temukan data berdasarkan ID
        $inquiry = InquirySales::findOrFail($id);

        // Ubah status menjadi 0
        $inquiry->status = 0;

        // Simpan perubahan
        $inquiry->save();

        return response()->json(['success' => 'Inquiry deleted successfully']);
    }

    public function approvedInquiry(Request $request, $id)
    {
        $inquiry = InquirySales::findOrFail($id);

        $inquiry->to_validate = 'Waiting';

        if ($request->action_type == 'approved') {
            $inquiry->to_approve = 'Approved';
            $inquiry->status = 5;
        } elseif ($request->action_type == 'not_approved') {
            $inquiry->to_approve = 'Not Approved';
            $inquiry->status = 0; // Or any other status code you want to set for not approved
        }

        $inquiry->save(); // Save the inquiry to update the database

        return redirect()->route('konfirmInquiry')->with('success', 'Inquiry updated successfully');
    }

    public function validateInquiry(Request $request, $id)
    {
        $inquiry = InquirySales::findOrFail($id);

        // Handle validation
        if ($request->action_type == 'validated') {
            $inquiry->to_validate = 'Validated';
            $inquiry->status = 6;

            // Save note
            $inquiry->note = $request->note;

            // Save attachment file
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = $file->getClientOriginalName(); // Use the original file name
                $file->move(public_path('assets/files'), $fileName); // Save to public/assets/files directory
                $inquiry->attach_file = $fileName;
            }
        } elseif ($request->action_type == 'not_validated') {
            $inquiry->to_validate = 'Not Validated';
            $inquiry->status = 0;
        }

        $inquiry->save(); // Save changes to the database

        return redirect()->route('reportInquiry')->with('success', 'Inquiry updated successfully');
    }

    public function previewSS(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_inquiry' => 'required|integer',
            'materials.*.id_type' => 'required|integer', // Validate id_type
            'materials.*.jenis' => 'required|string', // Validate jenis
            'materials.*.thickness' => 'nullable|string',
            'materials.*.weight' => 'nullable|string',
            'materials.*.inner_diameter' => 'nullable|string',
            'materials.*.outer_diameter' => 'nullable|string',
            'materials.*.length' => 'nullable|string',
            'materials.*.pcs' => 'nullable|string',
            'materials.*.qty' => 'nullable|string',
        ]);

        // Ambil id_inquiry dari request
        $id_inquiry = $request->id_inquiry;
        Log::info('ID Inquiry:', ['id_inquiry' => $id_inquiry]);

        // Iterasi dan simpan atau update material
        foreach ($request->materials as $material) {
            Log::info('Processing Material:', $material);

            // Cari detail inquiry berdasarkan id_inquiry dan id_type
            $detailInquiry = DetailInquiry::where('id_inquiry', $id_inquiry)
                                          ->where('id_type', $material['id_type'])
                                          ->first();
            if ($detailInquiry) {
                // Jika detail inquiry ditemukan, perbarui data detail inquiry
                $detailInquiry->update([
                    'id_type' => $material['id_type'],
                    'jenis' => $material['jenis'],
                    'thickness' => $material['thickness'],
                    'weight' => $material['weight'],
                    'inner_diameter' => $material['inner_diameter'],
                    'outer_diameter' => $material['outer_diameter'],
                    'length' => $material['length'],
                    'pcs' => $material['pcs'],
                    'qty' => $material['qty'],
                ]);
                Log::info('DetailInquiry updated', ['id' => $detailInquiry->id]);
            } else {
                // Jika detail inquiry tidak ditemukan, buat detail inquiry baru
                $newDetailInquiry = DetailInquiry::create([
                    'id_inquiry' => $id_inquiry,
                    'id_type' => $material['id_type'],
                    'jenis' => $material['jenis'],
                    'thickness' => $material['thickness'],
                    'weight' => $material['weight'],
                    'inner_diameter' => $material['inner_diameter'],
                    'outer_diameter' => $material['outer_diameter'],
                    'length' => $material['length'],
                    'pcs' => $material['pcs'],
                    'qty' => $material['qty'],
                ]);
                Log::info('DetailInquiry created', ['id' => $newDetailInquiry->id]);
            }
        }

        // Cari inquiry berdasarkan id_inquiry
        $inquiry = InquirySales::find($id_inquiry);
        if ($inquiry) {
            // Perbarui status inquiry menjadi 3
            $inquiry->status = 3;
            $inquiry->save();
            Log::info('Inquiry status updated to 3', ['id' => $inquiry->id]);
        } else {
            Log::warning('Inquiry not found', ['id_inquiry' => $id_inquiry]);
        }

        return response()->json(['message' => 'Detail Inquiry updated or created successfully']);
    }

    public function saveTindakLanjut(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_inquiry' => 'required|integer',
            'materials.*.id_type' => 'required|integer', // Validate id_type
            'materials.*.jenis' => 'required|string', // Validate jenis
            'materials.*.thickness' => 'nullable|string',
            'materials.*.weight' => 'nullable|string',
            'materials.*.inner_diameter' => 'nullable|string',
            'materials.*.outer_diameter' => 'nullable|string',
            'materials.*.length' => 'nullable|string',
            'materials.*.pcs' => 'nullable|string',
            'materials.*.qty' => 'nullable|string',
            'materials.*.konfirmasi' => 'nullable|string',
            'materials.*.no_po' => 'nullable|string',
            'materials.*.rencana_kedatangan' => 'nullable|string',
        ]);

        // Ambil id_inquiry dari request
        $id_inquiry = $request->id_inquiry;
        Log::info('ID Inquiry:', ['id_inquiry' => $id_inquiry]);

        // Iterasi dan simpan atau update material
        foreach ($request->materials as $material) {
            Log::info('Processing Material:', $material);

            // Cari detail inquiry berdasarkan id_inquiry dan id_type
            $detailInquiry = DetailInquiry::where('id_inquiry', $id_inquiry)
                                          ->where('id_type', $material['id_type'])
                                          ->first();
            if ($detailInquiry) {
                // Jika detail inquiry ditemukan, perbarui data detail inquiry
                $detailInquiry->update([
                    'id_type' => $material['id_type'],
                    'jenis' => $material['jenis'],
                    'thickness' => $material['thickness'],
                    'weight' => $material['weight'],
                    'inner_diameter' => $material['inner_diameter'],
                    'outer_diameter' => $material['outer_diameter'],
                    'length' => $material['length'],
                    'pcs' => $material['pcs'],
                    'qty' => $material['qty'],
                    'konfirmasi' => $material['konfirmasi'],
                    'no_po' => $material['no_po'],
                    'rencana_kedatangan' => $material['rencana_kedatangan'],
                ]);
                Log::info('DetailInquiry updated', ['id' => $detailInquiry->id]);
            } else {
                // Jika detail inquiry tidak ditemukan, buat detail inquiry baru
                $newDetailInquiry = DetailInquiry::create([
                    'id_inquiry' => $id_inquiry,
                    'id_type' => $material['id_type'],
                    'jenis' => $material['jenis'],
                    'thickness' => $material['thickness'],
                    'weight' => $material['weight'],
                    'inner_diameter' => $material['inner_diameter'],
                    'outer_diameter' => $material['outer_diameter'],
                    'length' => $material['length'],
                    'pcs' => $material['pcs'],
                    'qty' => $material['qty'],
                    'konfirmasi' => $material['konfirmasi'],
                    'no_po' => $material['no_po'],
                    'rencana_kedatangan' => $material['rencana_kedatangan'],
                ]);
                Log::info('DetailInquiry created', ['id' => $newDetailInquiry->id]);
            }
        }

        // Cari inquiry berdasarkan id_inquiry
        $inquiry = InquirySales::find($id_inquiry);
        if ($inquiry) {
            // Perbarui status inquiry menjadi 3
            $inquiry->status = 7;
            $inquiry->save();
            Log::info('Inquiry status updated to 3', ['id' => $inquiry->id]);
        } else {
            Log::warning('Inquiry not found', ['id_inquiry' => $id_inquiry]);
        }

        return response()->json(['message' => 'Detail Inquiry updated or created successfully']);
    }
}
