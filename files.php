<?php
// author email: 1351606745@qq.com
// 本代码仅供学习参考，不提供任何技术保证。
// 切勿使用本代码用于非法用处，违者后果自负。

class files
{
    public function setFileName($filename)
    {
        $this->filename = $filename;
    }
    public function fileserialize($data)
    {
        $this->fileContent = serialize($data);
        $file = fopen($this->filename, "w"); // 打开文件用于写入，如果文件不存在则创建它，如果文件存在则清空内容
        if ($file === false) {
            echo "无法打开数据库文件\n";
            return false;
        }
        
        if (!flock($file, LOCK_EX)) { //LOCK_NB,排它型锁定
            echo "无法锁定数据库文件\n";
            return false;
        }
        
        if (!fwrite($file, $this->fileContent)) {
            echo "无法写入缓存文件\n";
            return false;
        }
        
        flock($file, LOCK_UN); //释放锁定
        fclose($file);
        return true;
    }
    
    public function fileunserialize()
    {
        if (!file_exists($this->filename)) {
            echo "无法读取序列化文件\n";
            return false;
        } else {
            if (!filesize($this->filename) <= 0) {
                $file              = fopen($this->filename, 'r');
                $this->fileContent = fread($file, filesize($this->filename));
                fclose($file);
                return unserialize($this->fileContent);
            } else {
                echo "序列化文件无内容\n";
                return false;
            }
        }
        echo "文件反序列化未知错误\n";
        return false;
    }
    
    public function __construct()
    {
    }
    protected $filename = "keys";
    protected $fileContent;
    
}
?>