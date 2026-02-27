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
        // 1. Validasi semua data termasuk array pelanggan & detail
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required',
            'kamar' => 'required',
            'berat' => 'required|numeric',
            'jenisPakaian' => 'required',
            'jumlahOrang' => 'required|integer',
            'pelanggan' => 'required|array',
            'pelanggan.*.nama_pelanggan' => 'required|string',
            'detail_laundry' => 'required|array',
        ]);

        // 2. Tambahkan id_user secara manual ke array validated
        $validated['id_user'] = Auth::id();

        try {
            DB::beginTransaction();

            // 3. Simpan Grup menggunakan $validated (Laravel akan mengabaikan array pelanggan/detail jika tidak ada di $fillable)
            // Namun lebih aman memisahkan data grup saja:
            $grupData = collect($validated)->except(['pelanggan', 'detail_laundry'])->toArray();
            $grup = Grup::create($grupData);

            // 4. Proses relasi tetap manual karena kebutuhan Pivot (Detail Laundry)
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
            return response()->json($grup->load('pelanggan'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal: ' . $e->getMessage()], 500);
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
        // 1. Validasi semua data termasuk array pelanggan & detail
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required',
            'kamar' => 'required',
            'berat' => 'required|numeric',
            'jenisPakaian' => 'required',
            'jumlahOrang' => 'required|integer',
            'pelanggan' => 'required|array',
            'pelanggan.*.nama_pelanggan' => 'required|string',
            'detail_laundry' => 'required|array',
        ]);

        // 2. Tambahkan id_user secara manual ke array validated
        $validated['id_user'] = Auth::id();

        try {
            DB::beginTransaction();

            // 3. Simpan Grup menggunakan $validated (Laravel akan mengabaikan array pelanggan/detail jika tidak ada di $fillable)
            // Namun lebih aman memisahkan data grup saja:
            $grupData = collect($validated)->except(['pelanggan', 'detail_laundry'])->toArray();
            $grup = Grup::create($grupData);

            // 4. Proses relasi tetap manual karena kebutuhan Pivot (Detail Laundry)
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
            return response()->json($grup->load('pelanggan'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal: ' . $e->getMessage()], 500);
        }
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
