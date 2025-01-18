<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Logo;
use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\Logo as myLogo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class SettingController extends Controller
{
    function getLogo()
    {
        $logo = myLogo::orderBy('id', 'desc')->first();
        return response()->json(
            ['logo' => $logo]
        );
    }

    function setLogo(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'image' => 'required|string', // Base64 string
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Save the logo in the database
            $logo = Logo::create([
                'image' => $request->image,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Logo saved successfully.',
                'data' => $logo,
            ], 201);

        } catch (QueryException $e) {
            // Handle database errors
            return response()->json([
                'success' => false,
                'message' => 'Failed to save logo.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    function getImageAD()
    {
        $ad = Ad::orderBy('id', 'desc')->first();
        return response()->json(
            $ad
        );
    }

    function setImageAD(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string', // Base64 string
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Save the logo in the database
            $logo = AD::create([
                'image' => $request->image,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'ad saved successfully.',
                'data' => $logo,
            ], 201);

        } catch (QueryException $e) {
            // Handle database errors
            return response()->json([
                'success' => false,
                'message' => 'Failed to save ad.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
