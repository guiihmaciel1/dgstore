<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Services;

use App\Domain\WhatsApp\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaDownloadService
{
    /**
     * Baixa e armazena mídia localmente a partir do resultado do UazAPI download.
     */
    public function downloadAndStore(WhatsAppMessage $message): ?string
    {
        if ($message->local_media_path) {
            return $message->local_media_path;
        }

        $mediaTypes = ['image', 'imageMessage', 'video', 'videoMessage', 'audio', 'audioMessage', 'ptt', 'pttMessage', 'document', 'documentMessage', 'sticker', 'stickerMessage'];
        if (!in_array($message->message_type, $mediaTypes)) {
            return null;
        }

        try {
            $uazapi = app(UazapiService::class);
            $isAudio = in_array($message->message_type, ['audio', 'audioMessage', 'ptt', 'pttMessage']);
            $result = $uazapi->downloadMedia($message->wa_message_id, $isAudio);

            if (isset($result['error'])) {
                Log::warning('MediaDownload: erro no download', ['msg_id' => $message->id, 'error' => $result['error']]);
                return null;
            }

            $base64Data = $result['base64'] ?? $result['data'] ?? null;
            $mimeType = $result['mimetype'] ?? $result['mimeType'] ?? 'application/octet-stream';
            $transcription = $result['transcription'] ?? null;

            if (!$base64Data) {
                Log::warning('MediaDownload: sem dados base64', ['msg_id' => $message->id]);
                return null;
            }

            $ext = $this->getExtension($mimeType, $message->message_type);
            $dealId = $message->deal_id ?? 'unknown';
            $filename = Str::uuid() . ".{$ext}";
            $path = "whatsapp/received/{$dealId}/{$filename}";

            $decoded = base64_decode($base64Data);
            Storage::disk('public')->put($path, $decoded);

            $updateData = [
                'local_media_path' => $path,
                'media_mime_type' => $mimeType,
            ];

            if ($transcription && $isAudio) {
                $updateData['transcription'] = $transcription;
            }

            $message->update($updateData);

            Log::info('MediaDownload: mídia salva', [
                'msg_id' => $message->id,
                'path' => $path,
                'has_transcription' => !empty($transcription),
            ]);

            return $path;
        } catch (\Throwable $e) {
            Log::error('MediaDownload: exceção', ['msg_id' => $message->id, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Processa todas as mensagens de mídia pendentes de download.
     */
    public function processPending(int $limit = 20): int
    {
        $mediaTypes = ['image', 'imageMessage', 'video', 'videoMessage', 'audio', 'audioMessage', 'ptt', 'pttMessage', 'document', 'documentMessage'];

        $pending = WhatsAppMessage::whereIn('message_type', $mediaTypes)
            ->whereNull('local_media_path')
            ->where('direction', 'inbound')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        $processed = 0;
        foreach ($pending as $message) {
            if ($this->downloadAndStore($message)) {
                $processed++;
            }
        }

        return $processed;
    }

    private function getExtension(string $mimeType, string $messageType): string
    {
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'video/mp4' => 'mp4',
            'audio/ogg; codecs=opus' => 'ogg',
            'audio/ogg' => 'ogg',
            'audio/mpeg' => 'mp3',
            'audio/mp4' => 'm4a',
            'application/pdf' => 'pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        ];

        return $map[$mimeType] ?? match (true) {
            str_contains($messageType, 'image') => 'jpg',
            str_contains($messageType, 'audio'), str_contains($messageType, 'ptt') => 'ogg',
            str_contains($messageType, 'video') => 'mp4',
            default => 'bin',
        };
    }
}
