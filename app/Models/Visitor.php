<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    /**
     * Disable timestamps karena kita sudah ada visited_at
     */
    public $timestamps = false;

    /**
     * Mass assignment yang dibolehkan.
     *
     * @var array<string>
     */
    protected $fillable = [
        'ip_address',
        'user_agent',
        'country',
        'city',
        'visited_at',
    ];

    /**
     * Cast values untuk atribut.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'visited_at' => 'datetime',
    ];

    /**
     * Scopes untuk analytics
     */

    /**
     * Filter visitor berdasarkan tanggal
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('visited_at', $date);
    }

    /**
     * Filter visitor dalam periode tertentu
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('visited_at', [$startDate, $endDate]);
    }

    /**
     * Filter unique IP addresses
     */
    public function scopeUniqueIps($query)
    {
        return $query->distinct('ip_address');
    }

    /**
     * Hitung total visitor hari ini
     */
    public static function countToday()
    {
        return self::whereDate('visited_at', today())->count();
    }

    /**
     * Hitung total unique visitor hari ini
     */
    public static function countUniqueToday()
    {
        return self::whereDate('visited_at', today())->distinct('ip_address')->count('ip_address');
    }

    /**
     * Dapatkan data visitor untuk grafik
     */
    public static function getChartData($days = 7)
    {
        return self::selectRaw('DATE(visited_at) as date, COUNT(*) as count')
                   ->where('visited_at', '>=', now()->subDays($days))
                   ->groupBy('date')
                   ->orderBy('date')
                   ->get();
    }
}
