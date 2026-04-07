<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WilayahController extends Controller
{
    private const BASE_URL = 'https://emsifa.github.io/api-wilayah-indonesia/api';

    public function ajaxPage()
    {
        return view('pages.ajax.wilayah-ajax');
    }

    public function axiosPage()
    {
        return view('pages.ajax.wilayah-axios');
    }

    public function getProvinsi(): JsonResponse
    {
        return $this->fetchAndRespond(self::BASE_URL . '/provinces.json');
    }

    public function getKota(Request $request): JsonResponse
    {
        $request->validate([
            'provinsi_id' => 'required|string',
        ]);

        return $this->fetchAndRespond(self::BASE_URL . '/regencies/' . $request->provinsi_id . '.json');
    }

    public function getKecamatan(Request $request): JsonResponse
    {
        $request->validate([
            'kota_id' => 'required|string',
        ]);

        return $this->fetchAndRespond(self::BASE_URL . '/districts/' . $request->kota_id . '.json');
    }

    public function getKelurahan(Request $request): JsonResponse
    {
        $request->validate([
            'kecamatan_id' => 'required|string',
        ]);

        return $this->fetchAndRespond(self::BASE_URL . '/villages/' . $request->kecamatan_id . '.json');
    }

    private function fetchAndRespond(string $url): JsonResponse
    {
        try {
            $response = Http::timeout(15)->acceptJson()->get($url);

            if (! $response->ok()) {
                return response()->json([
                    'status' => 'error',
                    'code' => $response->status(),
                    'message' => 'Failed to fetch data from wilayah API',
                    'data' => [],
                ], $response->status());
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data received successfully',
                'data' => $response->json(),
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Internal server error',
                'data' => [],
            ], 500);
        }
    }
}
