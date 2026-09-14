<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AiController extends Controller
{
    protected GeminiService $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    /**
     * Endpoint Publik Display TV: Slide Mutiara Hadits & Hikmah (/hikmah-embed)
     */
    public function hikmahEmbed()
    {
        $setting = AppSetting::first();
        $hikmah = $this->gemini->getDailyHikmah(false);

        return view('hikmah-embed', compact('setting', 'hikmah'));
    }

    /**
     * Endpoint AJAX Admin: Generate Pengumuman dengan AI
     */
    public function generatePengumuman(Request $request): JsonResponse
    {
        $request->validate([
            'poin'     => 'required|string|min:3|max:1500',
            'kategori' => 'nullable|string|max:100',
        ]);

        $setting = AppSetting::first();
        $namaMasjid = $setting->nama_aplikasi ?? "Masjid Jami' Al-Jihad";
        $category = $request->input('kategori', 'Kajian Rutin');
        $rawPoints = $request->input('poin');

        $result = $this->gemini->generateAnnouncement($rawPoints, $category, $namaMasjid);

        return response()->json($result);
    }

    /**
     * Endpoint AJAX Admin: Refresh Hadits Hari Ini dengan AI
     */
    public function refreshHikmah(Request $request): JsonResponse
    {
        $hikmah = $this->gemini->getDailyHikmah(true);

        return response()->json([
            'success' => true,
            'message' => 'Mutiara Hadits hari ini berhasil diperbarui!',
            'data'    => $hikmah
        ]);
    }

    /**
     * Endpoint AJAX Admin: Uji Koneksi Google Gemini API
     */
    public function testConnection(): JsonResponse
    {
        $result = $this->gemini->testConnection();
        return response()->json($result);
    }
}
