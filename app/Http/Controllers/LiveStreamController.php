<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\JadwalSholat;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Models\SholatJumat;
use App\Models\SholatIdulFitri;
use App\Models\SholatIdulAdha;

class LiveStreamController extends Controller
{
    /**
     * Tampilan Siaran Langsung Mimbar / Khutbah (CCTV Masjid)
     */
    public function mimbarEmbed(Request $request)
    {
        $settings = AppSetting::first();
        $cctvUrl = $settings ? $settings->getCctvMimbarUrl() : '';
        $jadwalSholat = JadwalSholat::urutkan()->get();
        
        $sholatJumat = SholatJumat::first();
        $sholatIdulFitri = SholatIdulFitri::first();
        $sholatIdulAdha = SholatIdulAdha::first();

        // Tentukan petugas berdasarkan hari atau parameter
        $now = Carbon::now();
        $officers = [
            'type'    => 'SHOLAT JUM\'AT',
            'khatib'  => $sholatJumat->khatib ?? 'Ustadz / Khatib',
            'imam'    => $sholatJumat->imam ?? 'Imam Masjid',
            'muadzin' => $sholatJumat->muadzin ?? 'Muadzin Masjid',
            'bilal'   => $sholatJumat->bilal ?? 'Bilal Masjid',
        ];

        if ($request->get('event') === 'idul_fitri' || ($now->month == 10 && $now->day <= 3)) {
            $officers['type'] = 'SHOLAT IDUL FITRI';
            if ($sholatIdulFitri) {
                $officers['khatib'] = $sholatIdulFitri->khatib ?? $officers['khatib'];
                $officers['imam'] = $sholatIdulFitri->imam ?? $officers['imam'];
            }
        } elseif ($request->get('event') === 'idul_adha' || ($now->month == 12 && $now->day >= 10 && $now->day <= 13)) {
            $officers['type'] = 'SHOLAT IDUL ADHA';
            if ($sholatIdulAdha) {
                $officers['khatib'] = $sholatIdulAdha->khatib ?? $officers['khatib'];
                $officers['imam'] = $sholatIdulAdha->imam ?? $officers['imam'];
            }
        }

        // Tentukan tipe stream (YouTube, WebRTC/HLS/Iframe/Video)
        $isYouTube = false;
        $isIframe = false;
        $streamUrl = $cctvUrl;

        if (!empty($cctvUrl)) {
            if (str_contains($cctvUrl, 'youtube.com') || str_contains($cctvUrl, 'youtu.be')) {
                $isYouTube = true;
                $streamUrl = $this->buildEmbedUrl($cctvUrl, true);
            } elseif (str_starts_with($cctvUrl, 'http://') || str_starts_with($cctvUrl, 'https://')) {
                $isIframe = true;
            }
        }

        return view('live-stream.mimbar-embed', [
            'settings'        => $settings,
            'cctvUrl'         => $cctvUrl,
            'streamUrl'       => $streamUrl,
            'isYouTube'       => $isYouTube,
            'isIframe'        => $isIframe,
            'officers'        => $officers,
            'jadwalSholat'    => $jadwalSholat,
        ]);
    }
    /**
     * Tampilan Siaran Langsung Makkah (Masjidil Haram)
     */
    public function mekahEmbed()
    {
        $settings = AppSetting::first();
        $rawUrl = $settings ? $settings->getLiveMakkahUrl() : 'https://www.youtube.com/watch?v=live_stream?channel=UCr_yW_8sC_Yg_U9b_wH5Npg';
        $audioEnabled = $settings ? $settings->isLiveStreamAudioEnabled() : false;
        $overlayEnabled = $settings ? $settings->isLiveStreamOverlayEnabled() : true;

        $embedUrl = $this->buildEmbedUrl($rawUrl, $audioEnabled);
        $jadwalSholat = JadwalSholat::urutkan()->get();

        return view('live-stream', [
            'settings'        => $settings,
            'channelKey'      => 'mekah',
            'title'           => 'MASJIDIL HARAM, MAKKAH',
            'subtitle'        => 'Siaran Langsung Thawaf Ka\'bah & Sholat Berjamaah 24 Jam',
            'location'        => 'Makkah Al-Mukarramah, Arab Saudi',
            'embedUrl'        => $embedUrl,
            'audioEnabled'    => $audioEnabled,
            'overlayEnabled'  => $overlayEnabled,
            'jadwalSholat'    => $jadwalSholat,
            'themeColor'      => '#D4AF37', // Gold
            'badgeBg'         => 'rgba(212, 175, 55, 0.2)',
            'bgFallback'      => asset('img/background/bg_kaabah.jpg'),
        ]);
    }

    /**
     * Tampilan Siaran Langsung Madinah (Masjid Nabawi)
     */
    public function madinahEmbed()
    {
        $settings = AppSetting::first();
        $rawUrl = $settings ? $settings->getLiveMadinahUrl() : 'https://www.youtube.com/watch?v=live_stream?channel=UCaT_20Vp2Zq0FzXp3m4bVrg';
        $audioEnabled = $settings ? $settings->isLiveStreamAudioEnabled() : false;
        $overlayEnabled = $settings ? $settings->isLiveStreamOverlayEnabled() : true;

        $embedUrl = $this->buildEmbedUrl($rawUrl, $audioEnabled);
        $jadwalSholat = JadwalSholat::urutkan()->get();

        return view('live-stream', [
            'settings'        => $settings,
            'channelKey'      => 'madinah',
            'title'           => 'MASJID NABAWI, MADINAH',
            'subtitle'        => 'Siaran Langsung Kubah Hijau, Raudhah & Sholat Berjamaah 24 Jam',
            'location'        => 'Madinah Al-Munawwarah, Arab Saudi',
            'embedUrl'        => $embedUrl,
            'audioEnabled'    => $audioEnabled,
            'overlayEnabled'  => $overlayEnabled,
            'jadwalSholat'    => $jadwalSholat,
            'themeColor'      => '#10B981', // Emerald
            'badgeBg'         => 'rgba(16, 185, 129, 0.2)',
            'bgFallback'      => asset('img/background/bg_madinah.jpg'),
        ]);
    }

    /**
     * Helper mengubah berbagai format link YouTube menjadi Embed Iframe URL yang aman
     */
    private function buildEmbedUrl(string $url, bool $audioEnabled = false): string
    {
        $url = trim($url);
        $muteParam = $audioEnabled ? '0' : '1';

        // 1. Jika sudah berupa embed URL
        if (str_contains($url, 'youtube.com/embed') || str_contains($url, 'youtube-nocookie.com/embed')) {
            $separator = str_contains($url, '?') ? '&' : '?';
            return $url . $separator . "autoplay=1&mute={$muteParam}&controls=0&showinfo=0&rel=0&loop=1&enablejsapi=1";
        }

        // 2. Channel live stream (e.g. channel=UCxxxx atau live_stream?channel=UCxxxx)
        if (preg_match('/channel[=\/]([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $channelId = $matches[1];
            return "https://www.youtube-nocookie.com/embed/live_stream?channel={$channelId}&autoplay=1&mute={$muteParam}&controls=0&showinfo=0&rel=0&loop=1&enablejsapi=1";
        }

        // 3. Format /live/VIDEO_ID
        if (preg_match('/youtube\.com\/live\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $videoId = $matches[1];
            return "https://www.youtube-nocookie.com/embed/{$videoId}?autoplay=1&mute={$muteParam}&controls=0&showinfo=0&rel=0&loop=1&playlist={$videoId}&enablejsapi=1";
        }

        // 4. Format watch?v=VIDEO_ID
        if (preg_match('/[?&]v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $videoId = $matches[1];
            return "https://www.youtube-nocookie.com/embed/{$videoId}?autoplay=1&mute={$muteParam}&controls=0&showinfo=0&rel=0&loop=1&playlist={$videoId}&enablejsapi=1";
        }

        // 5. Format youtu.be/VIDEO_ID
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $videoId = $matches[1];
            return "https://www.youtube-nocookie.com/embed/{$videoId}?autoplay=1&mute={$muteParam}&controls=0&showinfo=0&rel=0&loop=1&playlist={$videoId}&enablejsapi=1";
        }

        // 6. Jika pengguna hanya memasukkan 11-digit Video ID secara langsung
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return "https://www.youtube-nocookie.com/embed/{$url}?autoplay=1&mute={$muteParam}&controls=0&showinfo=0&rel=0&loop=1&playlist={$url}&enablejsapi=1";
        }

        // Fallback default
        return "https://www.youtube-nocookie.com/embed/live_stream?channel=UCr_yW_8sC_Yg_U9b_wH5Npg&autoplay=1&mute={$muteParam}&controls=0&showinfo=0&rel=0&loop=1&enablejsapi=1";
    }
}
