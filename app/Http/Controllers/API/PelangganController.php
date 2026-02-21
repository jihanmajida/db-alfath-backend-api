<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $nama = $request->query('nama');

        $pelanggan = Pelanggan::query();

        if ($nama) {
            $pelanggan->where('nama_pelanggan', 'like', '%' . $nama . '%');
        }

        return response()->json([
            'result' => $pelanggan ->get()
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:50'
        ]);

        try {
            $pelanggan = Pelanggan::create($validated);

            return response()->json([
                'result' => $pelanggan
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $pelanggan = Pelanggan::find($id);

        if ($pelanggan) {
            return response()->json([
                'result' => $pelanggan
            ], 200);
        } else {
            return response()->json(['message' => 'Pelanggan not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:50'
        ]);

        $pelanggan = Pelanggan::find($id);

        if ($pelanggan) {
            try {
                $pelanggan->update($validated);

                return response()->json([
                    'result' => $pelanggan
                ], 200);
            } catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
        } else {
            return response()->json(['message' => 'Pelanggan not found'], 404);
        }
    }

    public function destroy($id)
    {
        $pelanggan = Pelanggan::find($id);

        if ($pelanggan) {
            try {
                $pelanggan->delete();

                return response()->json(['message' => 'Pelanggan deleted'], 200);
            } catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
        } else {
            return response()->json(['message' => 'Pelanggan not found'], 404);
        }
    }
}
