<?php

class Polis 
{
    public static function get_polis_data()
    {
        $result = Array();
        $db = self::mvc_connect();
        $sql = "";
        $exact = false;
        switch (func_num_args()) {
            case 2:
            $series = func_get_arg(0);
            $number = func_get_arg(1);
            $sql = "{Call mypolis('$series','$number')}";
            break;
    
            case 4:
            $ln = func_get_arg(0);
            $fn = func_get_arg(1);
            $mn = func_get_arg(2);
            $bdate = func_get_arg(3);
            $sql = "{Call myfamily1('$ln','$fn','$mn','$bdate')}";
            break;

            case 5:
            $ln = func_get_arg(0);
            $fn = func_get_arg(1);
            $mn = func_get_arg(2);
            $bdate = func_get_arg(3);
            $exact = func_get_arg(4);
            $sql = "{Call myfamily1('$ln','$fn','$mn','$bdate')}";
            break;

            default:
            $sql = "";
            $result = FALSE;
            break;
        }

        if ($sql)
        {
            $w = odbc_exec($db, $sql);
            while ($a = odbc_fetch_array($w)) {
                if (($exact and strcmp($bdate, substr($a['dbirth'], 0, 10)) == 0) || !$exact) {
                    $result[] = Array(
                    "ln"            => $a['family'],
                    "fn"            => $a['name'],
                    "mn"            => $a['name2'],
                    "birthdate"     => $a['dbirth'],
                    "series"        => $a['seria'],
                    "number"        => $a['nump'],
                    "treaty_number" => $a['numdog'],
                    "treaty_date"   => $a['dog_dat'],
                    "issue_date"    => $a['dbegin'],
                    "codes"         => $a['codes'],
                    "social_group"  => $a['status'],
                    "job"           => $a['nameassure'],
                    "company"       => $a['Strax'],
                    "lpu"           => $a['plpu'],
                    "snils"         => $a['stnum'],
                    "address"       => $a['address']);
                }
            }
        }
        odbc_close($db);
        return $result;
    }

    public static function get_by_snils($snils)
    {
        $data = Array();
        if (!snils) {
            throw new Exception("Не указан номер СНИЛС");
        }
        
        $db = self::mvc_connect();
        $sql = "{Call mysnils('$snils')}";
        $result = odbc_fetch_array(odbc_exec($db, $sql));
        if (count($result) > 0) {
            $data = $result;
        }
        return $data;
    }
    
    public static function mvc_connect() 
    {
        $db = odbc_connect("imvc", "clinic", "clinic664000");
        if (!db) {
            throw new Exception("Нет подключения к базе");
        }
        return $db;
    }
}
//$f = 'Иванов' ;
//$n = 'Иван';
//$y = 1978;

//$result = Polis::get_polis_data($f, $n , '', $y );
//$result = Polis::get_by_snils('10893982905');
//print_r($result);
?>