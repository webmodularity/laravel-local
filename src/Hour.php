<?php

namespace WebModularity\LaravelLocal;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Log;

class Hour extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'local_hours';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['weekday_index', 'time_start', 'time_end'];

    public static function isOpen($date = null) {
        $carbonDate = is_null($date) ? new Carbon() : $date;
        $time = $carbonDate->toTimeString();
        $weekdayIndex = $carbonDate->format('w');
        return (bool) static::where([
            ['weekday_index', '=', $weekdayIndex],
            ['time_start', '<=', $time],
            ['time_end', '>=', $time]
        ])->count();
    }

    public static function getWeekdayText($weekdayShort = false, $startWeekOnSunday = false, $phpDateFormat = 'g:ia') {
        $hours = static::orderBy('weekday_index', 'asc')->get();
        $normalHours = $weekdayText = [];
        foreach ($hours as $hour) {
            $normalHours[$hour->weekday_index][] = [
                'start' => $hour->time_start,
                'end' => $hour->time_end
            ];
        }
        $weekdayMap = $startWeekOnSunday
            ? [0, 1, 2, 3, 4, 5, 6]
            : [1, 2, 3, 4, 5, 6, 0];
        foreach ($weekdayMap as $weekdayIndex) {
            $weekdayName = $weekdayShort
                ? date('D', strtotime("Sunday +{$weekdayIndex} days"))
                : date('l', strtotime("Sunday +{$weekdayIndex} days"));
            $hourBlocks = [];
            if (isset($normalHours[$weekdayIndex])) {
                foreach ($normalHours[$weekdayIndex] as $hourPeriod) {
                    list($startHour, $startMinute, $startSecond) = explode(':', $hourPeriod['start'], 3);
                    list($endHour, $endMinute, $endSecond) = explode(':', $hourPeriod['end'], 3);
                    $startDate = Carbon::createFromTime($startHour, $startMinute, $startSecond);
                    $endDate = Carbon::createFromTime($endHour, $endMinute, $endSecond);
                    $hourBlocks[] = [
                        'open' => $startDate->format($phpDateFormat),
                        'close' => $endDate->format($phpDateFormat)
                    ];
                }
            }
            $weekdayText[$weekdayName] = $hourBlocks;
        }

        return $weekdayText;
    }

    public static function importFromGoogle($data) {
        $hourRecordsChanged = 0;
        $result = $data['result'];
        if (isset($result['opening_hours'])) {
            $hourPeriods = isset($result['opening_hours']['periods']) ? $result['opening_hours']['periods'] : [];
            $localHours = Hour::all()->toArray();
            $importHours = [];
            foreach ($hourPeriods as $period) {
                $weekdayIndex = $period['open']['day'];
                $timeOpen = substr($period['open']['time'], 0, 2) . ':' . substr($period['open']['time'], 2, 2) . ':00';
                $timeClose = isset($period['close'])
                    ? substr($period['close']['time'], 0, 2) . ':' . substr($period['close']['time'], 2, 2) . ':00'
                    : null;
                $importHours[] = [
                    'weekday_index' => $weekdayIndex,
                    'time_start' => $timeOpen,
                    'time_end' => $timeClose
                ];
            }
            if ($localHours !== $importHours) {
                // Wipe old hours
                static::getQuery()->delete();
                foreach ($importHours as $hour) {
                    static::create($hour);
                    $hourRecordsChanged++;
                }
            }
        } else {
            Log::warning('Google Places: No opening hours found.');
        }
        return $hourRecordsChanged;
    }
}