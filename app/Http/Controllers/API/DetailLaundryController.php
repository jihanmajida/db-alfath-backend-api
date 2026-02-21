<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DetailLaundry;
use Illuminate\Http\Request;

class DetailLaundryController extends Controller
{
    public function index(Request $request)
    {

        $detail = DetailLaundry::query();
        $detail->with('pelanggan');

        return response()->json([
            'result' => $detail->get()
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'id_grup' => 'required|exists:grup,id_grup',
            'baju' => 'required|integer',
            'rok' => 'required|integer',
            'jilbab' => 'required|integer',
            'kaos' => 'required|integer',
            'keterangan' => 'nullable|string'

        ]);

        try {
            $detail = DetailLaundry::create($validated);

            return response()->json([
                'result' => $detail
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show(DetailLaundry $detail)
    {
        return response()->json([
            'result' => $detail
        ], 200);
    }

    public function update(Request $request, DetailLaundry $detail)
    {
        $validated = $request->validate([
            'id_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'id_grup' => 'required|exists:grup,id_grup',
            'baju' => 'required|integer',
            'rok' => 'required|integer',
            'jilbab' => 'required|integer',
            'kaos' => 'required|integer',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $detail->update($validated);

            return response()->json([
                'result' => $detail
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(DetailLaundry $detail)
    {
        try {
            $detail->delete();

            return response()->json(['message' => 'DetailLaundry deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
