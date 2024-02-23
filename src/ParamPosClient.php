<?php
namespace Trgino;

class ParamPosClient 
{
    
    public $clientCode = 10738;
    public $clientUsername = 'Test';
    public $clientPassword = 'Test';
    public $guid = '0c13d406-873b-403b-9c09-a5766840d98c';
    public $G;
    public $serviceUrl;

    private $soapOptions = [];
    private $customSoapOptions = [];
    private $client;
    private $data;
    private $temp;
    private $debug = false;
    private $userType; // user, merchant

    private $url = [
        'test' => 'https://test-dmz.param.com.tr/turkpos.ws/service_turkpos_test.asmx?wsdl',
        'live' => 'https://posws.param.com.tr/turkpos.ws/service_turkpos_prod.asmx?wsdl',
    ];

    public function __construct($data = []) {
        $this->temp = new \stdClass();

        $this->soapOptions = [
            'soap_version'   => 'SOAP_1_1',
            'cache_wsdl'     => WSDL_CACHE_NONE,
            'trace'          => 1,
            'stream_context' => stream_context_create(
                [
                    'ssl' => [
                        'crypto_method'     => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true,
                    ]
                ]
            )
        ];

        $this->clientCode = isset($data['clientCode']) ? $data['clientCode'] : $this->clientCode;
        $this->clientUsername = isset($data['clientUsername']) ? $data['clientUsername'] : $this->clientUsername;
        $this->clientPassword = isset($data['clientPassword']) ? $data['clientPassword'] : $this->clientPassword;
        $this->guid = isset($data['guid']) ? $data['guid'] : $this->guid;
        $this->serviceUrl = (isset($data['mode']) && $data['mode'] == 'test') ? $this->url['test'] : $this->url['live'];

        $this->userType = isset($data['userType']) ? $data['userType'] : 'user';

        $this->debug = (isset($data['debug']) && $data['debug']) ? true : false;

        $this->G = new \stdClass();
        $this->G->CLIENT_CODE  = $this->clientCode;
        $this->G->CLIENT_USERNAME = $this->clientUsername;
        $this->G->CLIENT_PASSWORD = $this->clientPassword;

        $this->client = new \SoapClient($this->serviceUrl, self::get_soap_options());
    }

    private function param_number_format($price, $decimal = 2, $bracket = '.'){
        $_price = preg_replace('/\s+/', '', $price);
        $_price = floatval($_price);
        $_price = number_format( $_price, ($decimal + 1), $bracket, '');
        $_price = substr($_price, 0, -1);
        return $_price;
    }

    private function getClientIp() {
        if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])){
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }

		if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
			if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
				$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				return current($ips);
			} else {
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
		}
        return (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1');
	}

    private function limit_string($str, $limit = 255){
        if(strlen($str) > $limit){
            return substr($str, 0, $limit);
        }
        return $str;
    }
    
    private function limit_phone($str){
        return substr($str, -10);
    }

    private function object_2_array($obj){
        return json_decode(json_encode($obj), true);
    }

    private function get_currency_code($data){
        if(isset($data['Doviz_Kodu'])){
            if(in_array($data['Doviz_Kodu'], [1000, 1001, 1002, 1003])){
                return $data['Doviz_Kodu'];
            }elseif(in_array($data['Doviz_Kodu'], ['TRL', 'TRY', 'EUR', 'USD', 'GBP'])){
                return self::currency_code($data['Doviz_Kodu']);
            }
        }
        return 1000;
    }

    private function currency_code($currency = 'TRL') {
        $code = 1000;
        $code_list = [
            'TRL' => 1000,
            'TRY' => 1000,
            'USD' => 1001,
            'EUR' => 1002,
            'GBP' => 1003,
        ];
        if(isset($code_list[$currency])){
            $code = $code_list[$currency];
        }
        return $code;
    }

    public function set_soap_options($custom = []){
        if($custom && is_array($custom)){
            $this->soapOptions = array_merge($this->soapOptions, $custom);
        }
        return $this;
    }

    private function get_soap_options() {
        return $this->soapOptions;
    }

    private function bin_request($bin) {
        $_bin = new \stdClass();
        $_bin->BIN = $bin;
        $_bin->G = $this->G;

        return $this->client->BIN_SanalPos($_bin);
    }

    public function check_bin($bin) {
        $results = ['status' => false, 'msg' => 'Bin sonucu bulunamadı.', 'data' => [], 'errorCode' => 0];
        $bin = strlen($bin) >= 6 ? substr($bin, 0, 6) : '' ;
        $bin = ctype_digit($bin) ? $bin : '' ;

        $bin_results = self::bin_request($bin);
        if($this->debug){
            $results['debug'] = $bin_results;
        }

        if($bin_results && isset($bin_results->BIN_SanalPosResult) ){ //BIN_SanalPosResponse
            $_SanalPosResult = $bin_results->BIN_SanalPosResult;
            if(isset($_SanalPosResult->Sonuc) && intval($_SanalPosResult->Sonuc) == 1){
            
                $DT_Bilgi = $_SanalPosResult->DT_Bilgi;
                $xml = '<?xml version="1.0" encoding="utf-8"?><root>'.$DT_Bilgi->any.'</root>';
                try {
                    $sxe = new \SimpleXMLElement($xml);
                    $sxe->registerXPathNamespace('d', 'urn:schemas-microsoft-com:xml-diffgram-v1');
                    $newDataSet = $sxe->xpath("//NewDataSet");
                    
                    if( $newDataSet && isset($newDataSet[0]) && !empty($newDataSet[0]) && isset($newDataSet[0]->Temp) && !empty($newDataSet[0]->Temp) ){
                        foreach($newDataSet[0]->Temp as $_bin_data){
                            $results['data'][] = (array)$_bin_data;
                        }
                        if(!empty($results['data'])){
                            $results['status'] = true;
                            $results['msg'] = $bin;
                        }
                    }else{
                        $results['msg'] = 'Bin bulunamadı.';
                    }
                } catch (Exception $e) {
                    $results['msg'] = 'Bin çözümleme hatası.';
                }
            }else{
                $results['msg'] = $_SanalPosResult->Sonuc_Str;
                $results['errorCode'] = intval($_SanalPosResult->Sonuc);
            }
        }

        
        return $results;
    }

    private function sha2b64($extra = []){
        $security = [
            $this->clientCode,
            $this->guid,
            $this->data->Taksit,
            $this->data->Islem_Tutar,
            $this->data->Toplam_Tutar,
            $this->data->Siparis_ID,
            $this->data->Hata_URL,
            $this->data->Basarili_URL,
        ];
        if($extra){
            array_push($security, $extra);
        }
        $sha2B64 = new \stdClass();
        $sha2B64->Data = implode('', $security);
        $sha2B64->G = $this->G;
        return $this->client->SHA2B64($sha2B64)->SHA2B64Result;
    }

    private function organize_installments($data){
        $_temp = [];
        foreach($data as $_k => $_v){
            if(strpos($_k, 'MO_') !== false && floatval($_v) >= 0){
                $_no = intval(substr($_k, 3));
                $_temp[$_no] = floatval($_v);
            }
        }
        return $_temp;
    }

    private function fetch_installments($type) {
        $results = ['status' => false, 'msg' => 'Taksit oranları çözümlenemedi.', 'errorCode' => 0, 'xml' => ''];
        $_soap_results = false;
        $installments = new \stdClass();
        $installments->G = $this->G;
        $installments->GUID = $this->guid;
        if($type == 'user'){
            $_liste = $this->client->TP_Ozel_Oran_SK_Liste($installments);
            if( $_liste && isset($_liste->TP_Ozel_Oran_SK_ListeResult) ){
                $_soap_results = $_liste->TP_Ozel_Oran_SK_ListeResult;
                $_path = 'DT_Ozel_Oranlar_SK';
            }
        }elseif($type == 'merchant'){
            $_liste = $this->client->TP_Ozel_Oran_Liste($installments);
            if( $_liste && isset($_liste->TP_Ozel_Oran_ListeResult) ){
                $_soap_results = $_liste->TP_Ozel_Oran_ListeResult;
                $_path = 'DT_Ozel_Oranlar';
            }
        }
        if(isset($_soap_results->Sonuc) && intval($_soap_results->Sonuc) == 1){
            $results['xml'] = '<?xml version="1.0" encoding="utf-8"?><root>'.$_soap_results->DT_Bilgi->any.'</root>';
            $results['path'] = $_path;
            $results['msg'] = 'xml';
            $results['status'] = true;
        }else{
            $results['msg'] = $_soap_results->Sonuc_Str;
            $results['errorCode'] = intval($_soap_results->Sonuc);
        }
        return $results;
    } 

    public function get_installments($type = 'user') {
        $results = ['status' => false, 'msg' => 'Taksit oranları getirelemedi.', 'errorCode' => 0, 'data' => [], 'type' => $type];
        $installments = self::fetch_installments($type);
        if( $installments['status'] ){
            $xml = $installments['xml'];
            try {
                $sxe = new \SimpleXMLElement($xml);
                $sxe->registerXPathNamespace('d', 'urn:schemas-microsoft-com:xml-diffgram-v1');
                $newDataSet = $sxe->xpath("//NewDataSet");
                if( $newDataSet && isset($newDataSet[0]) && isset($newDataSet[0]->{$installments['path']}) && !empty($newDataSet[0]->{$installments['path']}) ){
                    foreach($newDataSet[0]->{$installments['path']} as $_oran_data){
                        $_oranlar = self::organize_installments($_oran_data);
                        if($_oranlar && isset($_oran_data)){
                            $bank = self::object_2_array($_oran_data->Kredi_Karti_Banka);
                            $results['data'][intval($_oran_data->SanalPOS_ID)] = [
                                'posId' => intval($_oran_data->SanalPOS_ID),
                                'bank' => (isset($_oran_data->Kredi_Karti_Banka) ? end($bank) : ''),
                                'rates' => $_oranlar,
                            ];
                        }
                    }
                    if(!empty($results['data'])){
                        $results['status'] = true;
                        $results['msg'] = 'Taksit oranlari';
                    }
                }else{
                    $results['msg'] = 'Taksit orani bulunamadı.';
                }
            } catch (Exception $e) {
                $results['msg'] = 'Taksit çözümleme hatası.';
            }
        }else{
            $results['msg'] = $installments['msg'];
            $results['errorCode'] = $installments['errorCode'];
        }

        return $results;
    }

    private function prepare_payment($data) {
        $this->temp->Doviz_Kodu = self::get_currency_code($data);

        $this->data = new \stdClass();
        $this->data->GUID = $this->guid;
        $this->data->KK_Sahibi = isset($data['KK_Sahibi']) ? self::limit_string($data['KK_Sahibi'], 100) : '';
        $this->data->KK_No = isset($data['KK_No']) ? self::limit_string($data['KK_No'], 16) : '';
        $this->data->KK_SK_Ay = isset($data['KK_SK_Ay']) ? self::limit_string($data['KK_SK_Ay'], 2) : '';
        $this->data->KK_SK_Yil = isset($data['KK_SK_Yil']) ? self::limit_string($data['KK_SK_Yil'], 4) : '';
        $this->data->KK_CVC = isset($data['KK_CVC']) ? self::limit_string($data['KK_CVC'], 3) : '';
        $this->data->KK_Sahibi_GSM = isset($data['KK_Sahibi_GSM']) ? self::limit_phone($data['KK_Sahibi_GSM']) : '';
        $this->data->Hata_URL = isset($data['Hata_URL']) ? self::limit_string($data['Hata_URL']) : '';
        $this->data->Basarili_URL = isset($data['Basarili_URL']) ? self::limit_string($data['Basarili_URL']) : '';
        $this->data->Siparis_ID = isset($data['Siparis_ID']) ? self::limit_string($data['Siparis_ID'], 50) : '';
        $this->data->Siparis_Aciklama = isset($data['Siparis_Aciklama']) ? self::limit_string($data['Siparis_Aciklama']) : '';
        $this->data->Taksit = isset($data['Taksit']) ? $data['Taksit'] : 1;
        $this->data->Islem_Tutar = isset($data['Islem_Tutar']) ? self::param_number_format($data['Islem_Tutar'], 2, ',') : '';
        $this->temp->Islem_Tutar = isset($data['Islem_Tutar']) ? self::param_number_format($data['Islem_Tutar'], 2, '.') : '';
        $this->data->Toplam_Tutar = isset($data['Toplam_Tutar']) ? self::param_number_format($data['Toplam_Tutar'], 2, ',') : '';
        $this->temp->Toplam_Tutar = isset($data['Toplam_Tutar']) ? self::param_number_format($data['Toplam_Tutar'], 2, '.') : '';
        $this->temp->Komisyon_Tutar = 0;
        $this->data->Islem_ID = isset($data['Islem_ID']) ? $data['Islem_ID'] : '';
        $this->data->IPAdr = isset($data['IPAdr']) ? self::limit_string($data['IPAdr'], 50) : self::getClientIp();
        $this->data->Ref_URL = isset($data['Ref_URL']) ? self::limit_string($data['Ref_URL']) : '';
        $this->data->Data1 = isset($data['Data1']) ? self::limit_string($data['Data1'], 250) : '';
        $this->data->Data2 = isset($data['Data2']) ? self::limit_string($data['Data2'], 250) : '';
        $this->data->Data3 = isset($data['Data3']) ? self::limit_string($data['Data3'], 250) : '';
        $this->data->Data4 = isset($data['Data4']) ? self::limit_string($data['Data4'], 250) : '';
        $this->data->Data5 = isset($data['Data5']) ? self::limit_string($data['Data5'], 250) : '';
        
        return $this;
    }

    public function pay($data = []){
        $results = ['status' => false, 'msg' => 'Beklenmeyen bir hata oluştu', 'errorCode' => 0];
        self::prepare_payment($data);

        $installmentRate = 0;
        $checkBin = $this->check_bin($this->data->KK_No);
        $getInstallments = $this->get_installments($this->userType);

        if(!$checkBin['status']){
            $results['msg'] = $checkBin['msg'];
            $results['errorCode'] = $checkBin['errorCode'];
            return $results;
        }

        if(!$getInstallments['status']){
            $results['msg'] = $getInstallments['msg'];
            $results['errorCode'] = $getInstallments['errorCode'];
            return $results;
        }

        $binData = end($checkBin['data']);
        if(isset($getInstallments['data'][$binData['SanalPOS_ID']])){
            $installementsRates = $getInstallments['data'][$binData['SanalPOS_ID']]['rates'];
            if(!isset($installementsRates[$this->data->Taksit])){
                $results['msg'] = 'Kartınız '.$this->data->Taksit.' taksit desteklemiyor.';
                return $results;
            }
            $installmentRate = $installementsRates[$this->data->Taksit];
        }

        if($installmentRate >= 0){
            $totalRate = 100 + floatval($installmentRate);
            $oldToplamTotar = floatval($this->temp->Islem_Tutar);
            $this->temp->Toplam_Tutar = $oldToplamTotar / 100 * $totalRate;
            $this->data->Toplam_Tutar = self::param_number_format($this->temp->Toplam_Tutar, 2, ',');
            $this->temp->Komisyon_Tutar = floatval($this->temp->Toplam_Tutar) - $oldToplamTotar;
        }
        
        $transaction = self::transaction();

        return array_merge($results, $transaction);
    }

    private function transaction(){
        $results = ['status' => false, 'msg' => 'Beklenmeyen bir hata oluştu', 'errorCode' => 0];
        $this->data->G = $this->G;
        if(isset($this->temp->Doviz_Kodu) && $this->temp->Doviz_Kodu == 1000){
            $this->data->Islem_Guvenlik_Tip = '3D';
            $this->data->Islem_Hash = self::sha2b64();
            $_response = $this->client->Pos_Odeme($this->data);
            $_path = 'Pos_OdemeResult';
        }else{
            $this->data->Islem_Guvenlik_Tip = '3D';
            $this->data->Islem_Hash = self::sha2b64('TTP_Islem_Odeme_WD_3D0001');
            $this->data->Doviz_Kodu = $this->temp->Doviz_Kodu;
            $_response = $this->client->TP_Islem_Odeme_WD($this->data);
            $_path = 'TP_Islem_Odeme_WDResult';
        }
        if($_response && isset($_response->{$_path})){
            $response = $_response->{$_path};
            if($response->Sonuc > 0){
                $results['status'] = true;
                $results['Islem_ID'] = $response->Islem_ID;
                $results['url'] = $response->UCD_URL;
                $results['msg'] = $response->Sonuc_Str;
                $results['bankCode'] = $response->Banka_Sonuc_Kod;
            }else{
                $results['msg'] = $response->Sonuc_Str;
                $results['errorCode'] = $response->Sonuc;
                $results['bankCode'] = $response->Banka_Sonuc_Kod;
            }
        }

        return $results;
    }
    
    public function check3d($post = []){
        $results = ['status' => false, 'msg' => 'İşlem geçersiz.', 'errorCode' => 0, 'data' => []];
        $post = $post ? $post : $_POST;
        if(isset($post['TURKPOS_RETVAL_Sonuc'])){
            foreach($post as $_k => $_v){
                if(strpos($_k,'TURKPOS_RETVAL') !== false){
                    $results['data'][$_k] = $_v;
                }
            }
            if(isset($results['data']['TURKPOS_RETVAL_Islem_Tarih'])){
                $results['data']['TURKPOS_RETVAL_Islem_Tarih_Unix'] = strtotime($results['data']['TURKPOS_RETVAL_Islem_Tarih']);
            }

            $results['msg'] = $results['data']['TURKPOS_RETVAL_Sonuc_Str'];

            if(intval($results['data']['TURKPOS_RETVAL_Sonuc']) > 0){
                $results['status'] = true;
            }
        }
        return $results;
    }

    public function checkPayment($data){
        $results = ['status' => false, 'msg' => 'Kontrol Sağlanamadı.', 'errorCode' => 0];
        $_check_data = new \stdClass();
        $_check_data->GUID = $this->guid;
        $_check_data->Dekont_ID = $data['Dekont_ID'];
        $_check_data->Siparis_ID = $data['Siparis_ID'];
        $_check_data->Islem_ID  = $data['Islem_ID'];
        $_check_data->G = $this->G;
        $response = $this->client->TP_Islem_Sorgulama4($_check_data);
        if($response && isset($response->TP_Islem_Sorgulama4Result)){
            $_response = $response->TP_Islem_Sorgulama4Result;
            if($_response->Sonuc > 0){
                if(intval($_response->DT_Bilgi->Odeme_Sonuc) == 1){
                    $results['status'] = true;
                    $results['msg'] = $_response->DT_Bilgi->Odeme_Sonuc_Aciklama;
                    $results['data'] = self::object_2_array($_response->DT_Bilgi);
                }else{
                    $results['msg'] = $_response->DT_Bilgi->Odeme_Sonuc_Aciklama;
                }
            }else{
                $results['msg'] = $_response->Sonuc_Str;
                $results['errorCode'] = $_response->Sonuc;
            }
        }
        return $results;
    }
}
