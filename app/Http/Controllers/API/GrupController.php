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
        // 1. Validasi (Nama field disesuaikan dengan snake_case agar sinkron dengan model)
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required',
            'kamar' => 'required',
            'berat' => 'required|numeric',
            'jenis_pakaian' => 'required', // Sesuaikan dengan database
            'jumlah_orang' => 'required|integer', // Sesuaikan dengan database
            'status_data' => 'nullable',
            'pelanggan' => 'required|array',
            'pelanggan.*.nama_pelanggan' => 'required|string',
            'detail_laundry' => 'required|array',
        ]);

        // 2. Tambahkan id_user (Pastikan User sudah login/Auth session jalan)
        $validated['id_user'] = Auth::id() ?? 1; // Fallback ke 1 untuk testing jika Auth null

        try {
            DB::beginTransaction();

            // 3. Simpan Grup
            $grupData = collect($validated)->except(['pelanggan', 'detail_laundry'])->toArray();
            $grup = Grup::create($grupData);

            // 4. Proses relasi Many-to-Many via Pivot (detail_laundry)
            foreach ($request->pelanggan as $key => $pData) {
                // Cari pelanggan atau buat baru jika belum ada
                $pelanggan = Pelanggan::firstOrCreate(['nama_pelanggan' => $pData['nama_pelanggan']]);

                $d = $request->detail_laundry[$key];
                $grup->pelanggan()->attach($pelanggan->id_pelanggan, [
                    'baju' => $d['baju'] ?? 0,
                    'rok' => $d['rok'] ?? 0,
                    'jilbab' => $d['jilbab'] ?? 0,
                    'kaos' => $d['kaos'] ?? 0,
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

    //Get Data by Id group
    public function show($id)
    {
        try {
            $grup = Grup::with('pelanggan')->where('id_grup', $id)->first();
            if (!$grup) {
                return response()->json(['message' => 'Grup not found'], 404);
            }
            return response()->json([
                'result' => $grup
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e], 500);
        }
    }

    // Proses edit data
    public function update(Request $request, string $id)
    {
        // 1. Validasi
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

        try {
            DB::beginTransaction();

            // 2. Cari data grup yang mau diupdate
            $grup = Grup::findOrFail($id);

            $grupData = collect($validated)->except(['pelanggan', 'detail_laundry'])->toArray();
            $grup->update($grupData);

            // 3. Update Relasi (Hapus yang lama dulu, baru pasang yang baru)
            $grup->pelanggan()->detach();

            foreach ($request->pelanggan as $key => $pData) {
                $pelanggan = Pelanggan::firstOrCreate(['nama_pelanggan' => $pData['nama_pelanggan']]);

                $d = $request->detail_laundry[$key];
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
        $request->validate([
            'status_data' => 'required|string'
        ]);

        $grup = Grup::findOrFail($id);
        $grup->status_data = $request->status_data;
        $grup->save();

        return response()->json(['message' => 'Status Berhasil Diperbarui'], 200);
    }

    public function destroy(Request $request, string $id)
    {
        $id = $request->id;

        $grup = Grup::findOrFail($id);
        $grup->delete();

        return response()->json([
            'message' => 'Berhasil Dihapus'
        ], 200);
    }
}
