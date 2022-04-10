<?php
namespace AlexNzr\BitUmcIntegration\Utils;

use AlexNzr\BitUmcIntegration\Config\Variables;
use Exception;

class Utils{
    private function __construct(){}

    /** clean request params
     * @param array $params
     * @return array
     */
    public static function cleanRequestData(array $params): array
    {
        $cleanParams = [];
        if (count($params)>0)
        {
            foreach ($params as $key => $param)
            {
                $cleanParam = $param;
                if (is_string($param))
                {
                    $cleanParam = trim(strip_tags(htmlspecialchars($param)));
                }
                elseif (is_array($param))
                {
                    $cleanParam = self::cleanRequestData($param);
                }

                if ($key === 'phone'){
                    $cleanParam = self::formatPhone($cleanParam);
                }

                $cleanParams[$key] = $cleanParam;
            }
        }
        return $cleanParams;
    }

    /** phone number formatting
     * @param string $phone
     * @return string
     */
    public static function formatPhone(string $phone): string
    {
        $phone = preg_replace(
            '/[^0-9]/',
            '',
            $phone);

        if(strlen($phone) > 10)
        {
            $phone = substr($phone, -10);
        }

        return  '+7' . $phone;
    }

    /** creates array with date interval
     * @param int $interval
     * @return array
     */
    public static function getDateInterval(int $interval): array
    {
        if (!is_int($interval)){
            $interval = 30;
        }
        $start = strtotime('today');
        $end = strtotime('today + ' . $interval . ' days');
        return [
            "startDate" => $start,
            "finishDate" => $end,
        ];
    }

    /** check required order params
     * @param array $params
     * @return bool
     */
    public static function validateOrderParams(array $params): bool
    {
        $isValid = true;
        $requiredParams = Variables::REQUIRED_ORDER_PARAMS;
        foreach ($requiredParams as $requiredParam) {
            if (empty($params[$requiredParam])){
                $isValid = false;
            }
        }
        return $isValid;
    }

    /** formatting date for 1c
     * @param string $dateString
     * @param bool $withTime
     * @return string
     */
    public static function formatDateToOrder(string $dateString, bool $withTime = false): string
    {
        if ($withTime){
            return date("YmdHis", strtotime($dateString));
        }
        return date("Ymd", strtotime($dateString));
    }

    /** create error message in json
     * @param string $message
     * @return string
     */
    public static function addError(string $message): string
    {
        return json_encode(["error" => $message]);
    }

    /** prints message
     * @param $message
     */
    public static function print($message): void
    {
        echo "<pre>";
        print_r($message);
        echo "</pre>";
    }

    /** prints param to file
     * @param $data
     */
    public static function printLog($data): void
    {
        $log = print_r($data, true);
        file_put_contents(
            Variables::PATH_TO_LOG_FILE,
            $log . PHP_EOL,
            FILE_APPEND
        );
    }

    /** Tests if an array is associative or not.
     * @param array array to check
     * @return boolean
     */
    public static function is_assoc(array $array): bool
    {
        if (!is_array($array)){
            return false;
        }

        // Keys of the array
        $keys = array_keys($array);
        // If the array keys of the keys match the keys, then the array must
        // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
        return array_keys($keys) !== $keys;
    }

    /** prepare schedule data for frontend
     * @param array $data
     * @return array
     */
    public static function prepareScheduleData(array $data): array
    {
        try {
            $schedule = $data["ГрафикиДляСайта"]["ГрафикДляСайта"];
            if (!empty($schedule) && empty($schedule["error"]))
            {
                if (is_array($schedule)){
                    if (self::is_assoc($schedule))
                    {
                        $schedule = array($schedule);
                    }

                    $formattedSchedule = [];
                    foreach ($schedule as $key => $item)
                    {
                        if (isset($item["СотрудникID"])){
                            $formattedSchedule[$key]["refUid"] = $item["СотрудникID"];
                        }
                        if (isset($item["Специализация"])){
                            $formattedSchedule[$key]["specialty"] = $item["Специализация"];
                        }
                        if (isset($item["СотрудникФИО"])){
                            $formattedSchedule[$key]["name"] = $item["СотрудникФИО"];
                        }
                        if (isset($item["Клиника"])){
                            $formattedSchedule[$key]["clinicUid"] = $item["Клиника"];
                        }

                        $duration = 0;
                        if (isset($item["ДлительностьПриема"])){
                            $formattedSchedule[$key]["duration"] = $item["ДлительностьПриема"];
                            $duration = intval(date("H", strtotime($item["ДлительностьПриема"]))) * 3600
                                + intval(date("i", strtotime($item["ДлительностьПриема"]))) * 60;
                            $formattedSchedule[$key]["durationInSeconds"] = $duration;
                        }

                        $freeTime = is_array($item["ПериодыГрафика"]["СвободноеВремя"])
                            ? $item["ПериодыГрафика"]["СвободноеВремя"]["ПериодГрафика"] : [];
                        $busyTime = is_array($item["ПериодыГрафика"]["ЗанятоеВремя"])
                            ? $item["ПериодыГрафика"]["ЗанятоеВремя"]["ПериодГрафика"] : [];
                        if (self::is_assoc($freeTime)) {
                            $freeTime = array($freeTime);
                        }
                        if (self::is_assoc($busyTime)) {
                            $busyTime = array($busyTime);
                        }

                        $formattedSchedule[$key]["timetable"]["free"] = self::formatTimetable($freeTime, $duration);
                        $formattedSchedule[$key]["timetable"]["busy"] = self::formatTimetable($busyTime, 0, true);
                        $formattedSchedule[$key]["timetable"]["freeNotFormatted"] = self::formatTimetable($freeTime, 0, true);
                    }
                    return [
                        "schedule" => $formattedSchedule,
                    ];
                }
                else{
                    throw new Exception("Error on preparing schedule - data is not array");
                }
            }
            else{
                throw new Exception("Error on preparing schedule - data is empty or invalid");
            }
        }
        catch (Exception $e){
            return [
                "error" => $e->getMessage(),
            ];
        }
    }

    /** Beautify array of timelines
     * @param $array
     * @param int $duration
     * @param bool $useDefaultInterval
     * @return array
     */
    public static function formatTimetable($array, int $duration, $useDefaultInterval = false): array
    {
        if (!is_array($array) || empty($array)){
            return [];
        }

        if (!$duration > 0){
            $duration = Variables::DEFAULT_APPOINTMENT_DURATION;
        }

        if (!empty($array)){
            if (self::is_assoc($array)) {
                $array = array($array);
            }
            $formattedArray = [];
            foreach ($array as $item)
            {
                $timestampTimeBegin = strtotime($item["ВремяНачала"]);
                $timestampTimeEnd = strtotime($item["ВремяОкончания"]);

                if ($useDefaultInterval){
                    $formattedArray[] = [
                        "typeOfTimeUid" => $item["ВидВремени"],
                        "date" => $item["Дата"],
                        "timeBegin" => date("Y-m-d", $timestampTimeBegin) ."T". date("H:i:s", $timestampTimeBegin),
                        "timeEnd" => date("Y-m-d", $timestampTimeEnd) ."T". date("H:i:s", $timestampTimeEnd),
                        "formattedDate" => date("d-m-Y", strtotime($item["Дата"])),
                        "formattedTimeBegin" => date("H:i", $timestampTimeBegin),
                        "formattedTimeEnd" => date("H:i", $timestampTimeEnd),
                    ];
                }
                else
                {
                    $timeDifference = $timestampTimeEnd - $timestampTimeBegin;
                    $appointmentsCount = round($timeDifference / $duration);

                    for ($i = 0; $i < $appointmentsCount; $i++)
                    {
                        $start = $timestampTimeBegin + ($duration * $i);
                        $end = $timestampTimeBegin + ($duration * ($i+1));

                        $formattedArray[] = [
                            "typeOfTimeUid" => $item["ВидВремени"],
                            "date" => $item["Дата"],
                            "timeBegin" => date("Y-m-d", $start) ."T". date("H:i:s", $start),
                            "timeEnd" => date("Y-m-d", $end) ."T". date("H:i:s", $end),
                            "formattedDate" => date("d-m-Y", strtotime($item["Дата"])),
                            "formattedTimeBegin" => date("H:i", $start),
                            "formattedTimeEnd" => date("H:i", $end),
                        ];
                    }
                }
            }
            return $formattedArray;
        }
        else
        {
            return [];
        }
    }

    /** checking the validity of the json
     * @param $string
     * @return bool
     */
    public static function isJSON($string): bool
    {
        return is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string, true)));
    }
}