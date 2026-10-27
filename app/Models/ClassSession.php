<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    protected $guarded = [];

    protected $casts = ['starts_at' => 'datetime', 'qr_open' => 'boolean'];

    const LATE_AFTER = 10;

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function rate()
    {
        $total = $this->attendances->count();

        if (! $total) {
            return null;
        }

        $came = $this->attendances->whereIn('status', ['present', 'late'])->count();

        return (int) round($came * 100 / $total);
    }

    public function qrCode($minute = null)
    {
        if ($minute === null) {
            $minute = floor(time() / 60);
        }

        $hash = hash_hmac('sha256', $this->id . '-' . $minute, $this->qr_secret);
        $number = hexdec(substr($hash, 0, 8)) % 1000000;

        return str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function checkCode($code)
    {
        if (!$this->qr_open || !$this->qr_secret) {
            return false;
        }

        $minute = floor(time() / 60);

        return hash_equals($this->qrCode($minute), $code) || hash_equals($this->qrCode($minute - 1), $code);
    }
}
