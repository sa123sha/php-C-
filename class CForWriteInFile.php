<?php
class CForWriteInFile
{
       
    public $path;
    public $day_now;
    public $mount_now;
    public $year_now;
    public $key;
    public $day_prev;
    public $mount_prev;
    public $year_prev;

    public static  $del_from_conf;
    public static  $mas_conf;
    public static  $list_tel;
    public static  $conf_path;
    public static  $add_phone;
    public static   $add_dogov;
    
    
    public function forSplit($str, $singt)
    {
        
        $_str = explode($singt,$str);
        for ($i = 0; $i < count($_str); $i++)
            $_str[$i] = trim($_str[$i]);
        return $_str;
    }
    public function del_phone()
    {
        $i=0;
        $start_del = false;
        try
        {
            
            $fd = fopen(self::$conf_path[6], 'r')or die;
            while (!feof($fd))
            {
                $line = fgets($fd);
                if ($line != "" and (trim($line) != "Phone"))
                { 
                    if (!in_array($line, self::$del_from_conf))
                    {    
                        self::$del_from_conf[$i] = trim($line);
                        $start_del = true;
                        $i++;
                    }
                }  
            }
            fclose($fd);
            $fd = fopen(CForWriteInFile::$conf_path[6], 'w') or die("?? ??????? ??????? ????");
            fputs($fd, "Phone");
            fclose($fd);
        }
        catch(Exception $e)
        {
           $this->WriteErr(CForWriteInFile::$conf_path[4], $e);
        }
        return $start_del;
    }
    
    public function AddPhone()
    {
        $lines = "";
        $rewrite = false;
        $add_start = false;
        $i = 0;
        //$class1 = new CForWriteInFile(); 
        try
        {
            
            $fd = fopen(CForWriteInFile::$conf_path[5], 'r') or die("?? ??????? ??????? ????");
            while (!feof($fd))
            {
                $line = fgets($fd);
                if ($line != "" and (trim($line) != "Dogovor;Phone"))
                {
                    try
                    {
                        
                        $str = $this->forSplit($line, ';');
                        if (!in_array($str[1], self::$add_phone))
                        {
                            
                            self::$add_phone[$i] = $str[1];
                            self::$add_dogov[$i] = $str[0];
                            $add_start = true;
                            $i++;
                        }
                    }
                    catch (Exception $e)
                    {
                        echo "Номер договора/телефона введены не корректно\n";
                        $this->WriteErr(self::$conf_path[4], $e);
                    }
                }
                
            }
            fclose($fd);
        }
        catch (Exception $e)
        {
            $this->WriteErr(self::$conf_path[4], $e);
        }
        if ($add_start)
        {
            $fd = fopen(self::$conf_path[5], 'w') or die("?? ??????? ??????? ????");
            fputs($fd, "Dogovor;Phone");
            fclose($fd);
            
            $fd = fopen(self::$conf_path[1], 'r') or die("?? ??????? ??????? ????");
            while (!feof($fd))
            {
                $line = fgets($fd);
                if ($line != "")
                {
                    $str = $this->forSplit($line, ';');

                    if (in_array($str[1], self::$add_phone))
                    {
                        
                        $key = array_search($str[1],self::$add_phone);
                        if (self::$add_dogov[$key] == $str[0])
                        {
                            self::$add_phone[$key] = "";
                            self::$add_dogov[$key] = "";
                            echo "{$str[1]} - Данный телефон уже присутствует в списке";
                        }
                        else
                        {
                            $rewrite = true;
                        }
                    }
                    else 
                    {
                        $lines .= trim($line);
                        $lines .="\n";
                    }
                }
            }
            fclose($fd);

            if ($rewrite)
            {
                $fd = fopen(self::$conf_path[1], 'w') or die("?? ??????? ??????? ????");
                fputs($fd, $lines);
                fclose($fd);
                $fd = fopen(self::$conf_path[1], 'a') or die("?? ??????? ??????? ????");
                for ($i = 0; $i < count(self::$add_phone); $i++)
                {
                    if (self::$add_phone[$i] != "")
                    {
                        $temp1 = self::$add_dogov[$i];
                        $temp2 = self::$add_phone[$i];
                        fputs($fd, "{$temp1};{$temp2}\n");
                        echo "Добавлен/Обновлен договор/телефон = {$temp2}/{$temp1}";
                    }
                }
                fclose($fd);

            }
            else
            {
                
                $fd = fopen(self::$conf_path[1], 'a') or die("?? ??????? ??????? ????");
                for ($i = 0; $i < count(self::$add_phone); $i++)
                {
                    if (self::$add_phone[$i] != "")
                    {
                        $temp1 = self::$add_dogov[$i];
                        $temp2 = self::$add_phone[$i];
                        fputs($fd, "{$temp1};{$temp2}");
                        echo "Добавлен/Обновлен договор/телефон = {$temp2}/{$temp1}";
                    }
                }
                fclose($fd);
            }
        }
    }

    public function read_conf_path()
    {
        $string = "D:\\test\\";
        $line = "";
        $i = 0;

        try
        {
            $fd = fopen("{$string}conf_path.ini", 'r') or die("");
            while (!feof($fd))
            {
                $line = fgets($fd);
                if ($line != "")
                {
                    $str = $this->forSplit($line, '/');
                    self::$conf_path[$i] = trim($str[0]);// = str[1];
                    //echo self::$conf_path[$i];
                    $i++;
                }
            }
            fclose($fd);
        }
        catch (Exception $e)
        {
            $date = new DateTime();
            //$date = $date->Now;
            echo "Exception: {$e->getMessage} {$date}";
            
            $fd = fopen("{$string}err\\error.txt",'a')or die;
            fputs($fd, "{$date} Exception: {$e->getMessage}");
            fclose($fd);
            
            Exit(0);
        }
    }
    
    public function read_files_ats($path, $date_prev, $date_now, & $result)
    {
        //echo $path;
        try
        {
            $fd = fopen($path, "r")or die();
            while (!feof($fd))
            {
                $line = fgets($fd);
                if ($line != "")
                { 
                    $str = $this->forSplit($line, '|');
                    $temp = "{$str[4]} {$str[5]}";
                    $date_file = strtotime($temp);
                    if (($date_file >= $date_prev and $date_file <= $date_now) and $this->check_in_mas_conf($str[2]))
                    {
                        //echo $line;
                        $temp = self::$mas_conf[$this->key];
                        $result .= "{$str[2]};{$temp};{$str[4]};{$str[5]}\n";
                    }

                }
            }
            fclose($fd);
        }
        catch (Exception $e)
        {
            $this->WriteErr(self::$conf_path[3], $e);
        }
    }
    
    public function check_need_del()
    {
        $line = "";
        
        $start_del = $this->del_phone();
        $i=0;
        try
        {
            $fd = fopen(self::$conf_path[0],'r')or die;
            while (!feof($fd))
            {
                $line = fgets($fd);
                if ($line != "")
                {
                        $str = $this->forSplit($line, ';');
                        if (count($str) == 5)
                        {
                            $str[4] = strtolower($str[4]);
                            if ($str[4] == "del" || $str[4] == "@@@")
                            {
                                if ($key = count(self::$del_from_conf))
                                {
                                        self::$del_from_conf[$i] = $str[0];
                                        $start_del = true;
                                        $i++;
                                }
                                else
                                {
                                        self::$del_from_conf[$key+1] = $str[0];
                                        $start_del = true;
                                }
                                
                            }
                        } 
                }
            }
            fclose($fd);
        }
        catch (Exception $e)
        {
            $this->WriteErr(self::$conf_path[4], $e);
        }

        if ($start_del) $this->del_from_config();
    }
    
    public function del_from_config()
    {
        
        $lines = "";
        try
        {
            
            $fd = fopen(self::$conf_path[1], 'r')or die;
            while (!feof($fd))
            {
                $line = fgets($fd);
                
                if ((trim($line," \n")) !== "")
                {
                    
                    $str = $this->forSplit($line, ';');

                    if (in_array("343{str[1]}", self::$del_from_conf) == false && in_array($str[1], self::$del_from_conf) == false)
                    {
                        $lines .= "{$line}";
                    }
                    else echo "Телефон будет удален = {$str[1]}";
                }
            }
            fclose($fd);
            
            $fd = fopen(self::$conf_path[1], 'w')or die;
            fputs($fd, $lines);
            fclose($fd);
        }
        catch (Exception $e)
        {
            $this->WriteErr(self::$conf_path[4], $e);
        }
    }
    
    public function read_conf()
    {
        $line = "";
        $i = 0;
        try
        {
            $fd = fopen(self::$conf_path[1],'r');
             if(!$fd)throw new Exception("some text");
            
            while (!feof($fd))
            {
                $line = fgets($fd);
                if ($line != "")
                {
                    
                    $str = $this->forSplit($line, ';');
                    if (strlen($str[1]) == 7)
                    {
                        $str[1] = "343{$str[1]}";
                    }
                    else if (strlen($str[1]) == 5)
                        $str[1] = "34370{$str[1]}";
                    self::$mas_conf[$i] = $str[0];
                    self::$list_tel[$i] = $str[1];
                    $i++;
                }
            }
            fclose($fd);
        }
        catch (Exception $e)
        {
            $this->WriteErr(self::$conf_path[4], $e);
        }
    }
    
    public function check_in_mas_conf ($str)
    {
        $bool = false;
        if ($this->key = array_search($str, self::$list_tel))
            $bool = true;
        else
        {
            $bool = false;
        }
        return $bool;
    }
    
    public function info_phone()
    {
        $fd = fopen(self::$conf_path[7],'w')or die;
        for ($i = 0; $i < count(self::$list_tel); $i++)
        {
            $temp = self::$list_tel[$i];
            fputs($fd, "{$temp}\n");
        }
        fclose($fd);
    }
    public function WriteErr($str, Exception $e)
    {
        $date = new DateTime();
        //$date = $date->now();
        echo "Exception:  {$e->getMessage()}";
        $fd = fopen($str, 'a')or die;
        fputs($fd, "Exception: {$e->getMessage()}");
        fclose($fd);

        /*if (!Directory.Exists("err"))
        {
            Directory.CreateDirectory("err");
        }*/
        $temp = "";
        $temp .= self::$conf_path[8];
        $fd = fopen("{$temp}err\\error.txt", 'a')or die;
        fputs($fd, "Exception: {$e->getMessage()}");
        fclose($fd);
    }
}