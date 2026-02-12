<?php

namespace App\Modules\Assets\Services;

use App\Modules\Assets\Models\Asset;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QRCodeService
{
    /**
     * Generate QR code for an asset
     */
    public function generateForAsset(Asset $asset): string
    {
        // Create QR code URL pointing to incident report page
        $url = route('assets.report-incident', ['asset' => $asset->id]);
        
        // Generate QR code image
        $qrCode = QrCode::format('png')
            ->size(300)
            ->margin(1)
            ->generate($url);
        
        // Save to storage
        $filename = 'qr-codes/asset-' . $asset->code . '.png';
        Storage::disk('public')->put($filename, $qrCode);
        
        // Update asset with QR code path
        $asset->update(['qr_code' => $filename]);
        
        return $filename;
    }

    /**
     * Get QR code URL for display
     */
    public function getQRCodeUrl(Asset $asset): ?string
    {
        if (!$asset->qr_code) {
            return null;
        }
        
        return Storage::disk('public')->url($asset->qr_code);
    }

    /**
     * Delete QR code file
     */
    public function deleteQRCode(Asset $asset): void
    {
        if ($asset->qr_code && Storage::disk('public')->exists($asset->qr_code)) {
            Storage::disk('public')->delete($asset->qr_code);
        }
    }

    /**
     * Regenerate QR code
     */
    public function regenerateForAsset(Asset $asset): string
    {
        $this->deleteQRCode($asset);
        return $this->generateForAsset($asset);
    }
}
