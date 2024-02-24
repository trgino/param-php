<?php
namespace Trgino;

include('vendor/autoload.php');

$param = new ParamPosClient([
    'clientCode' => 10738, //default 
    'clientUsername' => 'Test', //default 
    'clientPassword' => 'Test', //default 
    'guid' => '0c13d406-873b-403b-9c09-a5766840d98c', //default 
    'mode' => 'test', //default 
    'limitinstallment' => 12, //default 
    'advance' => true, // default false 
]);
ob_start();
echo '###########'.PHP_EOL.'check_bin:'.print_r($param->check_bin('6060432073705005'), true).PHP_EOL.'###########'.PHP_EOL;

echo '###########'.PHP_EOL.'get_installments:'.print_r($param->get_installments(), true).PHP_EOL.'###########'.PHP_EOL;

echo '###########'.PHP_EOL.'pay:'.print_r($param->pay([
    'KK_Sahibi' => 'Test test',
    'KK_No' => '6060432073705005',
    'KK_SK_Ay' => '05',
    'KK_SK_Yil' => '2026',
    'KK_CVC' => '000',
    'KK_Sahibi_GSM' => '5001231020',
    'Hata_URL' => 'http://localhost',
    'Basarili_URL' => 'http://localhost',
    'Siparis_ID' => '4444332',
    'Siparis_Aciklama' => 'aciklamalarr',
    'Taksit' => 1, //default
    'Islem_Tutar' => '100',
    'Toplam_Tutar' => '100',
    'Islem_ID' => '12345',
    'Data1' => 'data1test',
    'Data2' => 'data2test',
    'Doviz_Kodu' => 1000, //default 
]), true).PHP_EOL.'###########'.PHP_EOL;

echo '###########'.PHP_EOL.'check3d:'.print_r($param->check3d($_POST), true).PHP_EOL.'###########'.PHP_EOL;

echo '###########'.PHP_EOL.'checkPayment:'.print_r($param->checkPayment([
    'Dekont_ID' => 3003912366,
    'Siparis_ID' => '',
    'Islem_ID' => '',
]), true).PHP_EOL.'###########'.PHP_EOL;

file_put_contents('test.txt',ob_get_contents());
