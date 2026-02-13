<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grup;
use Illuminate\Http\Request;

class GrupController extends Controller
{
    // Proses penambahan data
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_user' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'kamar' => 'required|string|max:30',
            'berat' => 'required|numeric|min:0.1|decimal:2',
            'jenis_pakaian' => 'required|string|max:30',
            'jumlah_orang' => 'required|integer',
            'status_data' => 'required|string|max:20'
        ]);

        try {
            $grup = Grup::create($validated);

            return response()->json([
                'result' => $grup
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e], 500);
        }
    }

    // Proses edit data
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'id_user' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'kamar' => 'required|string|max:30',
            'berat' => 'required|numeric|min:0.1|decimal:2',
            'jenis_pakaian' => 'required|string|max:30',
            'jumlah_orang' => 'required|integer',
            'status_data' => 'required|string|max:20'
        ]);

        try {
            $grup = Grup::findOrFail($id);
            $grup->update($validated);
            return response()->json([
                'result' => $grup
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e], 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        $grup = Grup::findOrFail($id);
        $grup->delete();
        return response()->json(['message'=>'Berhasil Dihapus'],200);
    }
}
