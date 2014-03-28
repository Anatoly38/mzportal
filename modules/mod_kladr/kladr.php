<?php 
class Kladr
{
    public static function getDistrict($code)
    {   
        if (!$code) {
            return null;
        }
        $district = array();
        if (ereg ("([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{2})", $code, $kladr_code_array)) {
            switch (true) {
                case $kladr_code_array[2] != '000' && $kladr_code_array[3] == '000' :
                    $district['id'] = '38' . $kladr_code_array[2] . '00000000';
                    $data = self::getData($district['id']);
                    $district['name'] = trim($data['наименование']);
                    if (trim($data['сокращение']) == 'р-н') {
                        $district['prefix'] = 'Район';
                    } 
                    else {
                        $district['prefix'] = trim($data['сокращение']);
                    }
                break;
                case $kladr_code_array[2] == '000' && $kladr_code_array[3] != '000' :
                    $district['id'] = '380000000000001';
                    $district['name'] = 'Города субъектового подчинения';
                    $district['prefix'] = '';
                break;
            }
        }
        return $district;
    }

    public static function getCity($code)
    {   
        if (!$code) {
            return null;
        }
        if (ereg ("([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{2})", $code, $kladr_code_array)) {
            if ($kladr_code_array[2] == '000' && $kladr_code_array[3] != '000') {
                $city_code = $kladr_code_array[1] . $kladr_code_array[2] . $kladr_code_array[3] . '00000';
                $data = self::getData($city_code);
            }
            else {
                $data = self::getData(substr($code, 0, -2));
            }
            $city['id'] = trim($data['код_кладр']);
            $city['name'] = trim($data['наименование']);
            $city['prefix'] = trim($data['сокращение']);
            return $city;
        }
        return null;
    }

    public static function getStreet($code)
    {   
        if (!$code) {
            return null;
        }
        $data = self::getData($code .'00');
        $street['id'] = $code .'00';
        $street['name'] = trim($data['наименование']);
        $street['prefix'] = trim($data['сокращение']);
        return $street;
    }
    
    public static function getIndex($code)
    {   
        if (!$code) {
            return null;
        }
        $data = self::getData($code .'00');
        $index = trim($data['индекс']);
        return $index;
    }

    public static function getData($code)
    {
        $dbh = new DB_add();
        $query = "SELECT * FROM dic_kladr WHERE код_кладр = :1";
        $data = $dbh->prepare($query)->execute($code)->fetch_assoc();
            if(!$data) {
                return null;
            }
        return $data;
    }

    private function analyze_kladr_code($code)
    {
      $current_tag='';
      $parent_tag='';

      if (ereg ("([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{2})", $code, $kladr_code_array)) {
        switch (true) {
    // устаревший код
          case $kladr_code_array[5] <> '00' :
            $current_tag='obsolete';
            break;
    // первый уровень классификации - объекты федерального значения (регионы)
          case $kladr_code_array[2] == '000' && $kladr_code_array[3] == '000' && $kladr_code_array[4] == '000': 
            $current_tag=$kladr_code_array[1];
            $parent_tag='NULL';
            break;
    // второй уровень классификации  - районы
          case $kladr_code_array[2] != '000' && $kladr_code_array[3] == '000' && $kladr_code_array[4] == '000':
            $current_tag=$kladr_code_array[1].$kladr_code_array[2];
            $parent_tag=$kladr_code_array[1];
          break;
    // третий уровень классификации - города регионального подчинения 
          case $kladr_code_array[2] == '000' && $kladr_code_array[3] != '000' && $kladr_code_array[4] == '000':
            $current_tag=$kladr_code_array[1].$kladr_code_array[2].$kladr_code_array[3];
            $parent_tag=$kladr_code_array[1];
          break;
    // третий уровень классификации - города и сельсоветы районного подчинения 
          case $kladr_code_array[2] != '000' && $kladr_code_array[3] != '000' && $kladr_code_array[4] == '000':
            $current_tag=$kladr_code_array[1].$kladr_code_array[2].$kladr_code_array[3];
            $parent_tag=$kladr_code_array[1].$kladr_code_array[2];
          break;
    // четвертый уровень классификации - населенные пункты подчиненные непосредственно региону 
          case $kladr_code_array[2] == '000' && $kladr_code_array[3] == '000' && $kladr_code_array[4] != '000':
            $current_tag=$kladr_code_array[1].$kladr_code_array[2].$kladr_code_array[3].$kladr_code_array[4];
            $parent_tag=$kladr_code_array[1];
          break;
    // четвертый уровень классификации - населенные пункты районного подчинения 
          case $kladr_code_array[2] != '000' && $kladr_code_array[3] == '000' && $kladr_code_array[4] != '000':
            $current_tag=$kladr_code_array[1].$kladr_code_array[2].$kladr_code_array[3].$kladr_code_array[4];
            $parent_tag=$kladr_code_array[1].$kladr_code_array[2];
          break;
    // четвертый уровень классификации - населенные пункты подчиненные городам регионального или районного подчинения 
          case $kladr_code_array[3] != '000' && $kladr_code_array[4] != '000':
            $current_tag=$kladr_code_array[1].$kladr_code_array[2].$kladr_code_array[3].$kladr_code_array[4];
            $parent_tag=$kladr_code_array[1].$kladr_code_array[2].$kladr_code_array[3];
          break;
        }
      }
    }
}
?>