<?php
include "class CForWriteInFile.php";


$class1 = new cForWriteInFile();
            
$class1->read_conf_path();
$class1->AddPhone();
$class1->check_need_del();
$class1->read_conf();
$class1->info_phone();
			
$date = new DateTime();
//$date = $date->Now();

$date_now = new DateTime();
//$date->modify('-1 day');
$date_prev = new DateTime();
$date_prev->modify('-1 day');
$now_m = $date_now->format('m');
$now_y = $date_now->format('Y');
$now_d = $date_now->format('d');
$now_H = $date_now->format('H');
$now_i = $date_now->format('i');
$now_s = $date_now->format('s');
$sec_now = strtotime("{$now_y}-{$now_m}-{$now_d} 13:59:59");
$prev_m = $date_prev->format('m');
$prev_y = $date_prev->format('Y');
$prev_d = $date_prev->format('d');
$prev_H = $date_prev->format('H');
$prev_i = $date_prev->format('i');
$prev_s = $date_prev->format('s');
$sec_prev = strtotime("{$prev_y}-{$prev_m}-{$prev_d} 14:00:00");
echo "{$prev_y}{$prev_m}{$prev_d}\n";
//echo $date_now;
echo "{$now_y}{$now_m}{$now_d}\n";
echo "{$sec_prev} = {$sec_now}\n";

$temp = CForWriteInFile::$conf_path[2];           
$result = "";
try
{
    $class1->read_files_ats("{$temp}{$prev_d}{$prev_m}{$prev_y}.txt", $sec_prev, $sec_now, $result);
    $class1->read_files_ats("{$temp}{$now_d}{$now_m}{$now_y}.txt", $sec_prev, $sec_now, $result);

    try
    {
        $fd = fopen(CForWriteInFile::$conf_path[0], 'w') or die("?? ??????? ??????? ????");
        fputs($fd, "Phone;Dogovor;Date;Time\n");
        fputs($fd, $result);
        fclose($fd);
        echo("Записано в файл:\n{$result}");
        echo("Нажмите Enter для выхода...");
        
        
    }

    catch (Exception $e)
    {
        $this->WriteErr(CForWriteInFile::$conf_path[3], $e);
    }



}
catch (Exception $e)
{
    $this->WriteErr(CForWriteInFile::$conf_path[4], $e);
}




    