<?
class Filter {
    
    /**
     * String Filters
     */
    public static function getString($var_name, $nulls = true, $unset = false){
        $string  = filter_input(self::inputType($var_name), $var_name, FILTER_SANITIZE_STRING);
        if($unset){Filter::clear($var_name);}
        if(!$string && !$nulls){return " ";}
        return $string;
    }
 
    public static function getEmail($var_name, $nulls = true, $unset = false){
        $email = filter_input(self::inputType($var_name), $var_name, FILTER_SANITIZE_EMAIL);
        if($unset){Filter::clear($var_name);}
        if(!$email && !$nulls){return " ";}
        return $email;
    }

    public static function getUrl($var_name, $nulls = true, $unset = false){
        $url = filter_input(self::inputType($var_name), $var_name, FILTER_SANITIZE_URL);
        if($unset){Filter::clear($var_name);}
        if (!$url && !$nulls){return " ";}
        return $url;
    } 
    
    /**
     * Numeric Filters
     */
    public static function getInt($var_name, $nulls = true, $unset = false){
        $int = filter_input(self::inputType($var_name), $var_name, FILTER_SANITIZE_NUMBER_INT);
        if($unset){Filter::clear($var_name);}
        if (!$int && !$nulls){return 0;}
        return $int;
    }
    
    public static function getFloat($var_name, $nulls = true, $unset = false){
        $float = filter_input(self::inputType($var_name), $var_name, FILTER_SANITIZE_NUMBER_FLOAT);
        if($unset){Filter::clear($var_name);}
        if (!$float && !$nulls){return 0;}
        return $float;
    }
    
    /**
     * Specialty filters
     */
    public static function getEnum($var_name, $allowed_values = array(), $unset = false){
        $val = Filter::getString($var_name);
        if($unset){Filter::clear($var_name);}
        if (in_array($val, $allowed_values)){return $val;}
        else {return false;}
    }
    
    public static function getBool($var_name, $unset = false){
       $return = filter_input(self::inputType($var_name), $var_name, FILTER_UNSAFE_RAW);
        if($unset){Filter::clear($var_name);}
        
       //Handle ints
       if (is_int($return)){
            if   ($return == 1){return true;}
            else {return false;}
       }
       
       //Handle string
       if (is_string($return)){
            if   ($return == 'true' || $return = 'on'){return true;}
            else {return false;}
       }
       
       //Handle actual booleans
       if (is_bool($return)){return $return;}
       
    }
    
    /**
     * Bulk Filtering
     */
    public static function getAll($fields = array(), $nulls = false){
        $result = array();
        foreach ($fields as $var_name => $var_type){
            switch ($var_type){
                case 'decimal'           : $result[$var_name] = Filter::getDecimal($var_name);         break;
                case 'string'            : $result[$var_name] = Filter::getString($var_name, $nulls);  break;
                case 'int'               : $result[$var_name] = Filter::getInt($var_name);             break;
                case 'float'             : $result[$var_name] = Filter::getFloat($var_name);           break;
                case 'bool'              : $result[$var_name] = Filter::getBool($var_name);            break;
                case 'email'             : $result[$var_name] = Filter::getEmail($var_name);           break;
                case 'url'               : $result[$var_name] = Filter::getUrl($var_name);             break;
                case is_array($var_type) : $result[$var_name] = Filter::getEnum($var_name, $var_type); break;
            }
        }
        return $result;
    }
    
    public static function clear($var_name){
        if      (isset($_POST[$var_name])){unset($_POST[$var_name]);}
        else if (isset($_GET[$var_name])){unset($_POST[$var_name]);}
    }

    /**
     * Utility Functions
     */
    public static function inputType($var_name){
        if      (isset($_POST[$var_name])){return INPUT_POST;}
        else if (isset($_GET[$var_name])){return INPUT_GET;}
        else    {return null;}
    }
    
    public static function hasVal($var_name){
        $var = filter_input(self::inputType($var_name), $var_name, FILTER_UNSAFE_RAW);
        if (empty($var)){return false;}
        else {return true;}
    }
}


?>