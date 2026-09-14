<?php
$inputFile = 'C:\Users\anthu\.gemini\antigravity-ide\brain\f6b5bf92-6ee9-4fe8-abb2-bef239e5f116\.system_generated\logs\transcript_full.jsonl';
$outputFile = __DIR__ . '/Export_Percakapan.md';

if (!file_exists($inputFile)) {
    die("Error: Transcript file not found at $inputFile\n");
}

$lines = file($inputFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$markdown = "# Riwayat Percakapan Pengembangan Aplikasi DIGITALv304\n\n";
$markdown .= "Percakapan ini diekspor secara otomatis.\n\n---\n\n";

foreach ($lines as $line) {
    $data = json_decode($line, true);
    if (!$data) continue;
    
    $type = $data['type'] ?? '';
    $content = $data['content'] ?? '';
    
    // Abaikan sistem message atau empty content jika bukan dari user/model yang relevan
    if ($type === 'USER_INPUT') {
        $markdown .= "## 👤 Anda (User)\n\n" . $content . "\n\n---\n\n";
    } elseif ($type === 'PLANNER_RESPONSE') {
        // Cek jika response memiliki text, hindari yang hanya berisi thought/tool call tanpa pesan ke user
        if (trim($content) !== '') {
            $markdown .= "## 🤖 Asisten (AI)\n\n" . $content . "\n\n---\n\n";
        }
    }
}

file_put_contents($outputFile, $markdown);
echo "Berhasil mengekspor percakapan ke " . $outputFile . "\n";
