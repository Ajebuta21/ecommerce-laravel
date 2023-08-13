<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        $ad = new Ad($validatedData);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename =
                Str::random(8) . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/ads', $filename);
            $ad->image = url(Storage::url($path));
        }
        $ad->save();

        return response()->json([$ad, 'message' => 'Ad created'], 201);
    }

    public function index(){
        $ad = Ad::all();

        return response()->json($ad, 201);
    }

    public function destroy( $id)
    {
        $ad = Ad::findOrFail($id);
        if (!$ad) {
            return response()->json([$ad, 'message' => 'Ad not found'], 401);
        }
        $ad->delete();

        return response()->json([$ad, 'message' => 'Ad deleted'], 201);
    }
}
