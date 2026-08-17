<?php

namespace App\Services;

class PrayerEngine
{
    /**
     * Status Prayer Engine
     */
    private string $state = 'IDLE';

    /**
     * Prayer yang sedang aktif
     */
    private ?string $activePrayer = null;

    /**
     * Ambil status sekarang
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * Ambil nama sholat aktif
     */
    public function getActivePrayer(): ?string
    {
        return $this->activePrayer;
    }
}