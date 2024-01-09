<?php

class Serialization {
    // Deserialization function. Deserialized a serialized string.
    function deserialize($data) {
        $deserialized = [];
        $firstLayer = explode("|", $data);
        foreach ($firstLayer as $firstLayerVal) {
            $secondLayer = explode(";", $firstLayerVal);

            foreach ($secondLayer as $secondLayerVal) {
                if ($secondLayerVal[0] == "+") {
                    $class = substr($secondLayerVal, 1);
                }
                else {
                    $thirdLayer = explode(",", $secondLayerVal);
                    foreach ($thirdLayer as $thirdLayerVal) {
                        $fourthLayer = explode(":", $thirdLayerVal);
                        $deserialized[$class][$fourthLayer[0]] = $fourthLayer[1];
                    }
                }
            }
        }

        return $deserialized;
    }

    // Compare two arrays, code made by 'rogervila' on github.
    // https://raw.githubusercontent.com/rogervila/array-diff-multidimensional/master/src/ArrayDiffMultidimensional.php
    public function compare($array1, $array2)
    {
        $result = array();

        foreach ($array1 as $key => $value) {
            if (!is_array($array2) || !array_key_exists($key, $array2)) {
                $result[$key] = $value;
                continue;
            }

            if (is_array($value)) {
                $recursiveArrayDiff = static::compare($value, $array2[$key]);

                if (count($recursiveArrayDiff)) {
                    $result[$key] = $recursiveArrayDiff;
                }

                continue;
            }

            if ($value != $array2[$key]) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
