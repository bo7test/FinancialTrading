<?php namespace App\Services;

class MySqlObjectCreate  {

    public $createTableText = '';

    function convertCamelCaseToSnakeCase($text) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $text, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    function characterCountRightOfDecimal($string) {
        return strlen(substr(strrchr($string, "."), 1));
    }

    function characterCountLeftOfDecimal($string) {
        return strlen(round($string));
    }

    function isDecimal( $val )
    {
        if (strpos($val, '.') !== false) {
            return true;
        }
        else {
            return false;
        }
    }

    function getColumnType($string) {
        $length = strlen($string) + 2;

        if (is_numeric($string)) {
            if (!$this->isDecimal($string)) {
                if ($length >= 10) {
                    return 'bigint('.$length.')';
                }
                else {
                    return 'int('.$length.')';
                }
            }
            else {
                $decimalCharacterCount = $this->characterCountRightOfDecimal($string);
                $charactersLeftOfDecimalCount = $this->characterCountRightOfDecimal($string) + 5;
                $maximumDigits = $decimalCharacterCount + $charactersLeftOfDecimalCount;
                return 'decimal('.$maximumDigits.','.$decimalCharacterCount.')';
            }
        }
        elseif ($this->checkIfDate($string)) {
            return 'datetime';
        }
        else {
            $stringLength = strlen($string) + 5;
            return 'varchar('.$stringLength.')';
        }
    }

    public function checkIfDate($string) {
        if (\DateTime::createFromFormat('Y-m-d H:i:s', $string) !== FALSE) {
            return true;
        }
        else {
            return false;
        }
    }

    public function addCreateTableLineBreak() {
        $this->createTableText = $this->createTableText.'<BR>';
    }

    public function addTextToCreateTable($text) {
        $this->createTableText = $this->createTableText.$text;
        $this->addCreateTableLineBreak();
    }

    public function addColumnDefinitionsToTable($columnDefinitions) {
        foreach ($columnDefinitions as $columnDefinition) {
            $this->addTextToCreateTable($columnDefinition['name'].' '.$columnDefinition['type'].',');
        }
    }

    public function addDatesToTable() {

        $this->addTextToCreateTable('created_at datetime,');
        $this->addTextToCreateTable('updated_at datetime,');
    }

    public function createTableFromJson($tableName, $json) {
        $jsonObject = json_decode($json);

        $columnDefinitions = [];

        foreach ($jsonObject as $label => $value) {
            $columnName = $this->convertCamelCaseToSnakeCase($label);
            $columnType = $this->getColumnType($value);
            $columnDefinitions[] = [
                'name'=>$columnName,
                'type'=>$columnType
            ];
        }


        $this->addTextToCreateTable('create table '.$tableName.' (');
        $this->addTextToCreateTable('`id` bigint(20) NOT NULL AUTO_INCREMENT,');

        $this->addColumnDefinitionsToTable($columnDefinitions);

        $this->addDatesToTable();

        $this->addTextToCreateTable('PRIMARY KEY (`id`)');
        $this->addTextToCreateTable(');');

        dd($this->createTableText);
    }
}