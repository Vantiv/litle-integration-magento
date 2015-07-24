<?php
$input_line = "(  'USD' =>'101'  , 'CND'  => '222')";

// $input_line = str_replace(" ", "", "(  'USD' =>'101'  , 'CND'  => '222')");
$output_array = array();
// preg_match("/\((\"(?P<cur>[A-Z]+)\"=>\"(?P<id>\d+)\",?)+\)/", $input_line, $output_array);
// preg_match("/\(( *(\"[A-Z]+\"|'[A-Z]+') *=> *(\"\d+\"|'\d+') *,?)+ *\)/", $input_line, $output_array);
// preg_match("/\(((\"[A-Z]+\"|'[A-Z]+')=>(\"\d+\"|'\d+'),?)+\)/", $input_line, $output_array);
preg_match_all("/(?P<cur>(\"[A-Z]+\"|'[A-Z]+')) *=> *(?P<id>(\"\d+\"|'\d+'))/", $input_line, $output_array);
var_dump($output_array);