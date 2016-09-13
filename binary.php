<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 5/24/15
 * Time: 10:16 PM
 */
function test($string){
    $check = 0;
    for($i = 0;$i<strlen($string);$i++){
        if ($string[$i] == '(') {
            $check++;
        } elseif ($string[$i] == ')') {
            $check--;
        }
        if($check<0){ return FALSE;}
    }
    if($check!=0) {return FALSE;}
    else{return TRUE;}
}
function strallpos($pajar, $aguja, $offset = 0, &$count = null) {
    if ($offset > strlen($pajar)) trigger_error("strallpos(): Offset not contained in string.", E_USER_WARNING);
    $match = array();
    for ($count = 0; (($pos = strpos($pajar, $aguja, $offset)) !== false); $count++) {
        $match[] = $pos;
        $offset = $pos + strlen($aguja);
    }
    return $match;
}

function inside($expression) {
    $result = array();
    $all_start_pos = strallpos($expression, '(');
    foreach ($all_start_pos as $start_pos) {
        $check = 1;
        $end_pos = 0;
        for ($i = $start_pos + 1; $i < strlen($expression) && $check > 0; $i++) {
            if ($expression[$i] == '(') {
                $check++;
            } elseif ($expression[$i] == ')') {
                $check--;
                $end_pos = $i;
            }

        }
        $exp =substr($expression, ($start_pos + 1), $end_pos - $start_pos - 1);
        if(!in_array($exp,$result)) {
            $result[] = $exp;
        }
    }
    $result[] = $expression;
    return $result;
}

function setValue($values) {
    $result = array();
    $count = count($values);
    for ($i = 0; $i < pow(2, $count); $i++) {
        $bin = substr(str_pad(decbin($i), $count, "0", STR_PAD_LEFT), -$count);
        foreach ($values as $key => $value) {
            $result[$i][$value] = $bin[$key];
        }
    }
    return $result;
}

// check correct expression
$expression = strtoupper(strtr($_POST['expression'], array(' ' => '')));
if (preg_match('/^(([\\(*-?]*\\w+\\)*)[#|])*\\(?-?\\w+\\)*$/', $expression)&& test($expression)) {
//get all value
    $all_values = array();
    preg_match_all("/[a-zA-z]+/", $expression, $all_values);
    $values = array();
    foreach ($all_values[0] as $value) {
        if (!in_array($value, $values)) {
            $values[] = $value;
        }
    }
    //create all block
    $block_exp = inside($expression);
    $block_exp = array_diff($block_exp,$values);
    usort($block_exp, function($a, $b) {
        return strlen($a) - strlen($b);
    });
// convert expression to php
    $patterns = "/([a-zA-z]+)/";
    $replace = '($arr[\'$1\'])';
    $trans = array('#' => ' && ', '|' => ' || ', '-' => '!');
    $block = array();
    foreach ($block_exp as $exp) {
        $string = preg_replace($patterns, $replace, $exp);
        $block[] = strtr($string, $trans);
    }
// print table
    $all_variant = setValue($values);
    $table = '<table  style="border-collapse:collapse;text-align: center;"><thead><tr>';
    foreach ($values as $value) {
        $table .= '<th style="border:1px solid black;padding: 5px 8px;"> ' . $value . '</th>';
    }
    foreach ($block_exp as $exp) {
        $table .= '<th style="border:1px solid black;padding: 5px 8px;">' . strtr($exp, '#', '&') . '</th>';
    }
    $table .= '</tr></thead><tbody>';
    foreach ($all_variant as $key => $arr) {

        $table .= '<tr>';
        foreach ($arr as $value) {
            $table .= '<td style="border:1px solid black;padding: 5px 8px;"> ' . $value . '</td>';
        }
        foreach ($block as $string) {
            eval("\$result = $string;");
            $table .= '<td style="border:1px solid black;padding: 5px 8px;">' . (int)$result . '</td>';
        }

    }
    $table .= '</tr></tbody></table>';
    echo $table;
} else {
    echo 'Ошибка при вводе данных!';
}