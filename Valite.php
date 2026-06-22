<?php
// author email: 1351606745@qq.com
// 本代码仅供学习参考，不提供任何技术保证。
// 切勿使用本代码用于非法用处，违者后果自负。


include_once("files.php");

class valite
{
    public function setImagePath($Image)
    {
        $this->ImagePath = $Image;
    }
    
    public function study($info)
    {
        // 做成字符串
        $data = array();
        $i    = 0;
        foreach ($this->data as $key => $value) {
            $data[$i] = "";
            foreach ($value as $skey => $svalue) {
                $data[$i] .= implode("", $svalue);
            }
            if (strlen($data[$i]) > $this->maxfontwith) {
                ++$i;
            }
        }
        
        if (count($data) != count($info)) {
            echo "字符数量不匹配(" . count($data) . " get " . count($info) . ")，学习失败\n";
            return false;
        }
        
        // 设置N级匹配模式
        foreach ($info as $key => $value) {
            if (isset($this->Keys[0][$value])) {
                $percent = 0.0;
                similar_text($this->Keys[0][$value], $data[$key], $percent);
                if (intval($percent) < 96) {
                    $i  = 1;
                    $OK = false;
                    while (isset($this->Keys[$i][$value])) {
                        $percent = 0.0;
                        similar_text($this->Keys[$i][$value], $data[$key], $percent);
                        if (intval($percent) > 96) {
                            $OK = true;
                            break;
                        }
                        ++$i;
                    }
                    if (!$OK) {
                        while (!isset($this->Keys[$i][$value])) {
                            $this->Keys[$i][$value] = $data[$key];
                        }
                    }
                }
            } else {
                $this->Keys[0][$value] = $data[$key]; // key 0,1,2,3  Keys[0]["A"]=data[0]
            }
        }
        
        return true;
    }
    
    
    
    
    public function run()
    {
        $result = "";
        // 做成字符串
        $data   = array();
        $i      = 0;
        foreach ($this->data as $key => $value) {
            $data[$i] = "";
            foreach ($value as $skey => $svalue) {
                $data[$i] .= implode("", $svalue);
            }
            if (strlen($data[$i]) > $this->maxfontwith) {
                ++$i;
            }
        }
        
        // 进行关键字匹配
        foreach ($data as $numKey => $numString) {
            $max = 0.0;
            $num = 0;
            // 查找最佳匹配数字
            foreach ($this->Keys as $key => $value) {
                $FindOk = false;
                foreach ($value as $skey => $svalue) {
                    $percent = 0.0;
                    similar_text($svalue, $numString, $percent);
                    if (intval($percent) > $max) {
                        $max = $percent;
                        $num = $skey;
                        if (intval($percent) > 96) {
                            $FindOk = true;
                            break;
                        }
                    }
                }
                if ($FindOk)
                    break;
            }
            $result .= $num;
        }
        return $result;
    }
    
    public function filterInfo()
    {
        $this->data = array();
        $data       = array();
        $num        = 0;
        $b          = false;
        $Continue   = 0;
        $XStart     = 0;
        //字符分割
        //width
        for ($i = 0; $i < $this->ImageSize[0]; ++$i) {
            //height
            for ($j = 0; $j < $this->ImageSize[1]; ++$j) {
                //这一列有1,有1的是字符图像
                if ($this->DataArray[$j][$i] == "1") {
                    $b = true;
                    ++$Continue;
                    break;
                } else {
                    $b = false;
                }
            }
            //这一列有1
            if ($b == true) {
                //将这一列数据存data
                for ($jj = 0; $jj < $this->ImageSize[1]; ++$jj) {
                    $data[$num][$jj][$XStart] = $this->DataArray[$jj][$i];
                }
                //列数加一
                ++$XStart;
                
            } else {
                //没1且上一列有1
                if ($Continue > 0) {
                    $XStart   = 0;
                    $Continue = 0;
                    ++$num;
                }
            }
        }
        
        // 粘连字符分割
        $sData = array();
        $inum     = 0;
        for ($num = 0; $num < count($data); ++$num) {
            //获取字符宽度
            $str       = implode("", $data[$num][0]);
            $charWidth = strlen($str);
            // 超过标准长度，分割为两个字符
            if ($charWidth > $this->maxfontwith) {
                $len = ($charWidth + 1) / 2;
                $ih  = 0;
                foreach ($data[$num] as $key => $value) {
                    $ix  = 0;
                    $ixx = 0;
                    foreach ($value as $skey => $svalue) {
                        if ($skey < $len) {
                            $sData[$inum][$ih][$ix] = $svalue;
                            ++$ix;
                        } else {
                            $sData[$inum + 1][$ih][$ixx] = $svalue;
                            ++$ixx;
                        }
                    }
                    ++$ih;
                }
                //多了一个字符，加1
                ++$inum;
            } else {
                $i = 0;
                foreach ($data[$num] as $key => $value) {
                    $sData[$inum][$i] = $value;
                    ++$i;
                }
            }
            ++$inum;
        }
        
        // 按行去掉全0行数据
        $tnumf = 0;
        foreach ($sData as $key => $value) {
            $tnums = 0;
            foreach ($value as $skey => $svalue) {
                $str = implode("", $svalue);
                $pos = strpos($str, "1");
                if ($pos !== false) {
                    $this->data[$tnumf][$tnums] = $svalue;
                    ++$tnums;
                }
            }
            ++$tnumf;
        }
        return true;
    }
    
    /*
     * 画出10化图像
     */
    public function Draw()
    {
        //按行输出 $this->DataArray
        //weight = this->ImageSize[0], height = this->ImageSize[1] 
        for ($i = 0; $i < $this->ImageSize[1]; ++$i) {
            echo implode("", $this->DataArray[$i]);
            echo "\n";
        }
    }
    
    /*
     * 根据RGB把图像10化
     */
    public function generateBinImage()
    {
        $res  = imagecreatefromjpeg($this->ImagePath);
        $size = getimagesize($this->ImagePath);
        $data = array();
        for ($i = 0; $i < $size[1]; ++$i) {
            for ($j = 0; $j < $size[0]; ++$j) {
                $rgb      = imagecolorat($res, $j, $i);
                $rgbarray = imagecolorsforindex($res, $rgb);
                if ($rgbarray['red'] > 120 && ($rgbarray['green'] < 80 || $rgbarray['blue'] < 80)) {
                    $data[$i][$j] = 1;
                } else {
                    $data[$i][$j] = 0;
                }
            }
        }
        
        // 如果1的周围数字不为1，修改为0
        for ($i = 0; $i < $size[1]; ++$i) {
            for ($j = 0; $j < $size[0]; ++$j) {
                $num = 0;
                if ($data[$i][$j] == 1) {
                    // 上
                    if (isset($data[$i - 1][$j])) {
                        $num += $data[$i - 1][$j];
                    }
                    // 下
                    if (isset($data[$i + 1][$j])) {
                        $num += $data[$i + 1][$j];
                    }
                    // 左
                    if (isset($data[$i][$j - 1])) {
                        $num += $data[$i][$j - 1];
                    }
                    // 右
                    if (isset($data[$i][$j + 1])) {
                        $num += $data[$i][$j + 1];
                    }
                    // 上左
                    if (isset($data[$i - 1][$j - 1])) {
                        $num += $data[$i - 1][$j - 1];
                    }
                    // 上右
                    if (isset($data[$i - 1][$j + 1])) {
                        $num += $data[$i - 1][$j + 1];
                    }
                    // 下左
                    if (isset($data[$i + 1][$j - 1])) {
                        $num += $data[$i + 1][$j - 1];
                    }
                    // 下右
                    if (isset($data[$i + 1][$j + 1])) {
                        $num += $data[$i + 1][$j + 1];
                    }
                }
                if ($num == 0) {
                    $data[$i][$j] = 0;
                }
            }
        }
        
        $this->DataArray = $data;
        $this->ImageSize = $size;
    }
    
    public function bmp2jpeg($file)
    {
        $res = $this->imagecreatefrombmp($file);
        imagejpeg($res, $file . ".jpeg");
    }
    
    public function imagecreatefrombmp($file)
    {
        global $CurrentBit, $echoMode;
        
        $f      = fopen($file, "r");
        $Header = fread($f, 2);
        
        if ($Header == "BM") {
            $Size             = $this->freaddword($f);
            $Reserved1        = $this->freadword($f);
            $Reserved2        = $this->freadword($f);
            $FirstByteOfImage = $this->freaddword($f);
            
            $SizeBITMAPINFOHEADER    = $this->freaddword($f);
            $Width                   = $this->freaddword($f);
            $Height                  = $this->freaddword($f);
            $biPlanes                = $this->freadword($f);
            $biBitCount              = $this->freadword($f);
            $RLECompression          = $this->freaddword($f);
            $WidthxHeight            = $this->freaddword($f);
            $biXPelsPerMeter         = $this->freaddword($f);
            $biYPelsPerMeter         = $this->freaddword($f);
            $NumberOfPalettesUsed    = $this->freaddword($f);
            $NumberOfImportantColors = $this->freaddword($f);
            
            if ($biBitCount < 24) {
                $img    = imagecreate($Width, $Height);
                $Colors = pow(2, $biBitCount);
                for ($p = 0; $p < $Colors; $p++) {
                    $B         = $this->freadbyte($f);
                    $G         = $this->freadbyte($f);
                    $R         = $this->freadbyte($f);
                    $Reserved  = $this->freadbyte($f);
                    $Palette[] = imagecolorallocate($img, $R, $G, $B);
                }
                
                
                
                
                if ($RLECompression == 0) {
                    $Zbytek = (4 - ceil(($Width / (8 / $biBitCount))) % 4) % 4;
                    
                    for ($y = $Height - 1; $y >= 0; $y--) {
                        $CurrentBit = 0;
                        for ($x = 0; $x < $Width; $x++) {
                            $C = freadbits($f, $biBitCount);
                            imagesetpixel($img, $x, $y, $Palette[$C]);
                        }
                        if ($CurrentBit != 0) {
                            $this->freadbyte($f);
                        }
                        for ($g = 0; $g < $Zbytek; $g++)
                            $this->freadbyte($f);
                    }
                    
                }
            }
            
            
            if ($RLECompression == 1) //$BI_RLE8
                {
                $y = $Height;
                
                $pocetb = 0;
                
                while (true) {
                    $y--;
                    $prefix = $this->freadbyte($f);
                    $suffix = $this->freadbyte($f);
                    $pocetb += 2;
                    
                    $echoit = false;
                    
                    if ($echoit)
                        echo "Prefix: $prefix Suffix: $suffix<BR>";
                    if (($prefix == 0) and ($suffix == 1))
                        break;
                    if (feof($f))
                        break;
                    
                    while (!(($prefix == 0) and ($suffix == 0))) {
                        if ($prefix == 0) {
                            $pocet = $suffix;
                            $Data .= fread($f, $pocet);
                            $pocetb += $pocet;
                            if ($pocetb % 2 == 1) {
                                $this->freadbyte($f);
                                $pocetb++;
                            }
                        }
                        if ($prefix > 0) {
                            $pocet = $prefix;
                            for ($r = 0; $r < $pocet; $r++)
                                $Data .= chr($suffix);
                        }
                        $prefix = $this->freadbyte($f);
                        $suffix = $this->freadbyte($f);
                        $pocetb += 2;
                        if ($echoit)
                            echo "Prefix: $prefix Suffix: $suffix<BR>";
                    }
                    
                    for ($x = 0; $x < strlen($Data); $x++) {
                        imagesetpixel($img, $x, $y, $Palette[ord($Data[$x])]);
                    }
                    $Data = "";
                    
                }
                
            }
            
            
            if ($RLECompression == 2) //$BI_RLE4
                {
                $y      = $Height;
                $pocetb = 0;
                
                //while(!feof($f))
                //echo $this->freadbyte($f)."_".$this->freadbyte($f)."<BR>";
                while (true) {
                    //break;
                    $y--;
                    $prefix = $this->freadbyte($f);
                    $suffix = $this->freadbyte($f);
                    $pocetb += 2;
                    
                    $echoit = false;
                    
                    if ($echoit)
                        echo "Prefix: $prefix Suffix: $suffix<BR>";
                    if (($prefix == 0) and ($suffix == 1))
                        break;
                    if (feof($f))
                        break;
                    
                    while (!(($prefix == 0) and ($suffix == 0))) {
                        if ($prefix == 0) {
                            $pocet = $suffix;
                            
                            $CurrentBit = 0;
                            for ($h = 0; $h < $pocet; $h++)
                                $Data .= chr(freadbits($f, 4));
                            if ($CurrentBit != 0)
                                freadbits($f, 4);
                            $pocetb += ceil(($pocet / 2));
                            if ($pocetb % 2 == 1) {
                                $this->freadbyte($f);
                                $pocetb++;
                            }
                        }
                        if ($prefix > 0) {
                            $pocet = $prefix;
                            $i     = 0;
                            for ($r = 0; $r < $pocet; $r++) {
                                if ($i % 2 == 0) {
                                    $Data .= chr($suffix % 16);
                                } else {
                                    $Data .= chr(floor($suffix / 16));
                                }
                                $i++;
                            }
                        }
                        $prefix = $this->freadbyte($f);
                        $suffix = $this->freadbyte($f);
                        $pocetb += 2;
                        if ($echoit)
                            echo "Prefix: $prefix Suffix: $suffix<BR>";
                    }
                    
                    for ($x = 0; $x < strlen($Data); $x++) {
                        imagesetpixel($img, $x, $y, $Palette[ord($Data[$x])]);
                    }
                    $Data = "";
                    
                }
                
            }
            
            
            if ($biBitCount == 24) {
                $img    = imagecreatetruecolor($Width, $Height);
                $Zbytek = $Width % 4;
                
                for ($y = $Height - 1; $y >= 0; $y--) {
                    for ($x = 0; $x < $Width; $x++) {
                        $B     = $this->freadbyte($f);
                        $G     = $this->freadbyte($f);
                        $R     = $this->freadbyte($f);
                        $color = imagecolorexact($img, $R, $G, $B);
                        if ($color == -1)
                            $color = imagecolorallocate($img, $R, $G, $B);
                        imagesetpixel($img, $x, $y, $color);
                    }
                    for ($z = 0; $z < $Zbytek; $z++)
                        $this->freadbyte($f);
                }
            }
            return $img;
            
        }
        
        
        fclose($f);
    }
    
    public function freadbyte($f)
    {
        return ord(fread($f, 1));
    }
    
    public function freadword($f)
    {
        $b1 = $this->freadbyte($f);
        $b2 = $this->freadbyte($f);
        return $b2 * 256 + $b1;
    }
    
    public function freaddword($f)
    {
        $b1 = $this->freadword($f);
        $b2 = $this->freadword($f);
        return $b2 * 65536 + $b1;
    }
    
    public function __construct()
    {
        $keysfiles  = new files;
        $this->Keys = $keysfiles->fileunserialize();
        if ($this->Keys == false)
            $this->Keys = array();
        unset($keysfiles);
    }
    
    public function __destruct()
    {
    }
    
    public function savaDatabase()
    {
        $keysfiles = new files;
        $keysfiles->fileserialize($this->Keys);
        //print_r($this->Keys);
    }
    
    protected $ImagePath;
    protected $DataArray;
    protected $ImageSize;
    protected $data;
    protected $Keys;
    protected $NumStringArray;
    public $maxfontwith = 16;
    
}
?>