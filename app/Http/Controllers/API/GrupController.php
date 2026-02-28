<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grup;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GrupController extends Controller
{
    public function index(Request $request)
    {
        $nama = $request->query('nama');
        $tanggal = $request->query('tanggal');

        $grup = Grup::query();
        $grup->with('pelanggan');

        if ($nama) {
            $grup->whereHas('pelanggan', function ($query) use ($nama) {
                $query->where('nama_pelanggan', 'like', '%' . $nama . '%');
            });
        }

        if ($tanggal) {
            $grup->where('tanggal', $tanggal);
        }

        return response()->json($grup->get(), 200);
    }

    // Proses penambahan data
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required',
            'kamar' => 'required',
            'berat' => 'required|numeric',
            'jenis_pakaian' => 'required',
            'jumlah_orang' => 'required|integer',
            'status_data' => 'nullable',
            'pelanggan' => 'required|array',
            'pelanggan.*.nama_pelanggan' => 'required|string',
            'detail_laundry' => 'required|array',
        ]);

        $validated['id_user'] = Auth::id() ?? 1;

        try {
            DB::beginTransaction();

            $grupData = collect($validated)->except(['pelanggan', 'detail_laundry'])->toArray();
            $grup = Grup::create($grupData);

            // Ganti loop dengan safety check ?? []
            foreach ($request->pelanggan as $key => $pData) {
                $pelanggan = Pelanggan::firstOrCreate(['nama_pelanggan' => $pData['nama_pelanggan']]);

                // Ambil detail berdasarkan key, jika tidak ada gunakan array kosong
                $d = $request->detail_laundry[$key] ?? [];

                $grup->pelanggan()->attach($pelanggan->id_pelanggan, [
                    'baju'       => $d['baju'] ?? 0,
                    'rok'        => $d['rok'] ?? 0,
                    'jilbab'     => $d['jilbab'] ?? 0,
                    'kaos'       => $d['kaos'] ?? 0,
                    'keterangan' => $d['keterangan'] ?? ''
                ]);
            }

            DB::commit();
            return response()->json($grup->load('pelanggan'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal simpan: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $grup = Grup::with('pelanggan')->where('id_grup', $id)->first();
            if (!$grup) {
                return response()->json(['message' => 'Grup not found'], 404);
            }
            return response()->json(['result' => $grup], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // Proses edit data
   public function update(Request $request, $id)
{
    // PINDAHKAN VALIDASI KE PALING ATAS
    $validated = $request->validate([
        'tanggal' => 'required|date',
        'jam' => 'required',
        'kamar' => 'required',
        'berat' => 'required|numeric',
        'jenis_pakaian' => 'required',
        'jumlah_orang' => 'required|integer',
        'status_data' => 'nullable',
        'pelanggan' => 'required|array',
        'pelanggan.*.nama_pelanggan' => 'required|string',
        'detail_laundry' => 'required|array', // Field ini yang error di Android
    ]);

    try {
        DB::beginTransaction(); // Pastikan ini membungkus semua proses update

        $grup = Grup::findOrFail($id);
        $grupData = collect($validated)->except(['pelanggan', 'detail_laundry'])->toArray();
        
        // Update data utama
        $grup->update($grupData);

        // Hapus relasi lama
        $grup->pelanggan()->detach();

        // Pasang relasi baru
        foreach ($request->pelanggan as $key => $pData) {
            $pelanggan = Pelanggan::firstOrCreate(['nama_pelanggan' => $pData['nama_pelanggan']]);
            $d = $request->detail_laundry[$key] ?? [];

            $grup->pelanggan()->attach($pelanggan->id_pelanggan, [
                'baju' => $d['baju'] ?? 0,
                'rok' => $d['rok'] ?? 0,
                'jilbab' => $d['jilbab'] ?? 0,
                'kaos' => $d['kaos'] ?? 0,
                'keterangan' => $d['keterangan'] ?? ''
            ]);
        }

        DB::commit();
        return response()->json($grup->load('pelanggan'), 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Gagal update: ' . $e->getMessage()], 500);
    }
}

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status_data' => 'required|string']);

        $grup = Grup::findOrFail($id);
        $grup->status_data = $request->status_data;
        $grup->save();

        return response()->json(['message' => 'Status Berhasil Diperbarui'], 200);
    }

    public function destroy($id)
    {
        try {
            $grup = Grup::findOrFail($id);
            // Lepas relasi pivot dulu agar database tetap bersih
            $grup->pelanggan()->detach();
            $grup->delete();

            return response()->json(['message' => 'Berhasil Dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus: ' . $e->getMessage()], 500);
        }
    }
}
