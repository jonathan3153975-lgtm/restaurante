<?php

declare(strict_types=1);

namespace App\Service;

use App\Core\App;

final class QrCodeService
{
    public function imageUrl(string $token): string
    {
        $target = App::config('app.url') . '/mesa/' . $token;

        return 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' . urlencode($target);
    }
}
