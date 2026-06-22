<?php
// author email: 1351606745@qq.com
// 本代码仅供学习参考，不提供任何技术保证。
// 切勿使用本代码用于非法用处，违者后果自负。

include("Valite.php");

$valite = new Valite();
$data   = array(
    "4dvnq.jpeg" => array(
        '4',
        'd',
        'v',
        'n',
        'q'
    ),
    "r7dyq.jpeg" => array(
        'r',
        '7',
        'd',
        'y',
        'q'
    ),
    "anfdd.jpeg" => array(
        'a',
        'n',
        'f',
        'd',
        'd'
    ),
    "ec6uu.jpeg" => array(
        'e',
        'c',
        '6',
        'u',
        'u'
    ),
    "hw6kg.jpeg" => array(
        'h',
        'w',
        '6',
        'k',
        'g'
    ),
    "mwq7a.jpeg" => array(
        'm',
        'w',
        'q',
        '7',
        'a'
    ),
    "n6wnw.jpeg" => array(
        'n',
        '6',
        'w',
        'n',
        'w'
    ),
    "nmndu.jpeg" => array(
        'n',
        'm',
        'n',
        'd',
        'u'
    ),
    "pwrkk.jpeg" => array(
        'p',
        'w',
        'r',
        'k',
        'k'
    ),
    "quzpd.jpeg" => array(
        'q',
        'u',
        'z',
        'p',
        'd'
    ),
    "wncdx.jpeg" => array(
        'w',
        'n',
        'c',
        'd',
        'x'
    ),
    "ydndw.jpeg" => array(
        'y',
        'd',
        'n',
        'd',
        'w'
    ),
    "queaa.jpeg" => array(
        'q',
        'u',
        'e',
        'a',
        'a'
    ),
    "zacfd.jpeg" => array(
        'z',
        'a',
        'c',
        'f',
        'd'
    )
);

foreach ($data as $key => $value) {
    echo "文件:" . "$key\n";
    $valite->setImagePath($key);
    $valite->generateBinImage();
	$valite->Draw();
    $valite->filterInfo();
    $valite->study($value);
	
	echo "\n结果是：";
	echo $valite->run() . "\n\n";
}
//学习玩保存数据
$valite->savaDatabase();

?>