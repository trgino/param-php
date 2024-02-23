# param-php

    namespace Trgino;
    include('vendor/autoload.php');
    $param = new ParamPosClient([
    	'clientCode'  =>  10738,
    	'clientUsername'  =>  'Test',
    	'clientPassword'  =>  'Test',
    	'guid'  =>  '0c13d406-873b-403b-9c09-a5766840d98c',
    	'mode'  =>  'test',
    ]);



## check_bin

    $param->check_bin('6060432073705005')

#### check_bin results

    Array
    (
        [status] => 1
        [msg] => 606043
        [data] => Array
            (
                [0] => Array
                    (
                        [BIN] => 606043
                        [SanalPOS_ID] => 1013
                        [Kart_Banka] => TÜRKİYE FİNANS KATILIM BANKASI A.Ş.
                        [DKK] => 0
                        [Kart_Tip] => Debit Card
                        [Kart_Org] => MASTER CARD
                        [Banka_Kodu] => 206
                        [Kart_Ticari] => Hayır
                        [Kart_Marka] => Diğer Banka Kartları
                    )
    
            )
    
        [errorCode] => 0
    )

## get_installments

    $param->get_installments();

#### get_installments results

    Array
    (
        [status] => 1
        [msg] => Taksit oranlari
        [errorCode] => 0
        [data] => Array
            (
                [1052] => Array
                    (
                        [posId] => 1052
                        [bank] => 
                        [rates] => Array
                            (
                                [1] => 0
                                [2] => 0
                                [3] => 0
                                [4] => 0
                                [5] => 0
                                [6] => 0
                                [7] => 0
                                [8] => 0
                                [9] => 0
                                [10] => 0
                                [11] => 0
                                [12] => 0
                            )
    
                    )
    
                [1057] => Array
                    (
                        [posId] => 1057
                        [bank] => 
                        [rates] => Array
                            (
                                [1] => 0
                                [2] => 0
                                [3] => 0
                                [4] => 0
                                [5] => 0
                                [6] => 0
                                [7] => 0
                                [8] => 0
                                [9] => 0
                                [10] => 0
                                [11] => 0
                                [12] => 0
                            )
    
                    )
    
                [1068] => Array
                    (
                        [posId] => 1068
                        [bank] => 
                        [rates] => Array
                            (
                                [1] => 1.75
                            )
    
                    )
    
                [1014] => Array
                    (
                        [posId] => 1014
                        [bank] => Axess
                        [rates] => Array
                            (
                                [1] => 0
                                [2] => 0
                                [3] => 0
                                [4] => 0
                                [5] => 0
                                [6] => 0
                                [7] => 0
                                [8] => 0
                                [9] => 0
                                [10] => 0
                                [11] => 0
                                [12] => 0
                            )
    
                    )
    
                [1013] => Array
                    (
                        [posId] => 1013
                        [bank] => Bonus
                        [rates] => Array
                            (
                                [1] => 1.5
                                [2] => 0
                                [3] => 0
                                [4] => 0
                                [5] => 0
                                [6] => 0
                                [7] => 0
                                [8] => 0
                                [9] => 0
                                [10] => 0
                                [11] => 0
                                [12] => 0
                            )
    
                    )
    
                [1011] => Array
                    (
                        [posId] => 1011
                        [bank] => CardFinans
                        [rates] => Array
                            (
                                [1] => 0
                                [2] => 1
                                [3] => 0
                                [4] => 0
                                [5] => 0
                                [6] => 0
                                [7] => 0
                                [8] => 0
                                [9] => 0
                                [10] => 0
                                [11] => 0
                                [12] => 10.4
                            )
    
                    )
    
                [1008] => Array
                    (
                        [posId] => 1008
                        [bank] => Combo
                        [rates] => Array
                            (
                                [1] => 1
                                [2] => 0
                                [3] => 0
                                [4] => 0
                                [5] => 0
                                [6] => 0
                                [7] => 0
                                [8] => 0
                                [9] => 0
                                [10] => 0
                                [11] => 0
                                [12] => 0
                            )
    
                    )
    
                [1029] => Array
                    (
                        [posId] => 1029
                        [bank] => Diğer Banka Kartları
                        [rates] => Array
                            (
                                [1] => 1.75
                            )
    
                    )
    
                [1073] => Array
                    (
                        [posId] => 1073
                        [bank] => ISTANBULKART
                        [rates] => Array
                            (
                                [1] => 1.75
                            )
    
                    )
    
                [1028] => Array
                    (
                        [posId] => 1028
                        [bank] => Maximum
                        [rates] => Array
                            (
                                [1] => 0
                                [2] => 3
                                [3] => 5
                                [4] => 5.3
                                [5] => 5.95
                                [6] => 6.45
                                [7] => 7.2
                                [8] => 7.8
                                [9] => 8.65
                                [10] => 9.1
                                [11] => 9.8
                                [12] => 10.49
                            )
    
                    )
    
                [1012] => Array
                    (
                        [posId] => 1012
                        [bank] => Paraf
                        [rates] => Array
                            (
                                [1] => 1.75
                                [2] => 2
                                [3] => 5
                                [4] => 5.3
                                [5] => 5.95
                                [6] => 6.45
                                [7] => 7.2
                                [8] => 7.8
                                [9] => 8.65
                                [10] => 9.1
                                [11] => 9.8
                                [12] => 10.49
                            )
    
                    )
    
                [1018] => Array
                    (
                        [posId] => 1018
                        [bank] => Param
                        [rates] => Array
                            (
                                [1] => 1.25
                            )
    
                    )
    
                [1009] => Array
                    (
                        [posId] => 1009
                        [bank] => World
                        [rates] => Array
                            (
                                [1] => 1.75
                                [2] => 3
                                [3] => 5
                                [4] => 5.3
                                [5] => 5.95
                                [6] => 6.45
                                [7] => 7.2
                                [8] => 7.8
                                [9] => 8.65
                                [10] => 9.1
                                [11] => 9.8
                                [12] => 10.49
                            )
    
                    )
    
                [1023] => Array
                    (
                        [posId] => 1023
                        [bank] => Yurt Dışı Kartları
                        [rates] => Array
                            (
                                [1] => 1.75
                            )
    
                    )
    
            )
    
        [type] => user
    )

## pay

    $param->pay([
    	'KK_Sahibi'  =>  'Test test',
    	'KK_No'  =>  '6060432073705005',
    	'KK_SK_Ay'  =>  '05',
    	'KK_SK_Yil'  =>  '2026',
    	'KK_CVC'  =>  '000',
    	'KK_Sahibi_GSM'  =>  '5001231020',
    	'Hata_URL'  =>  'http://localhost',
    	'Basarili_URL'  =>  'http://localhost',
    	'Siparis_ID'  =>  '4444332',
    	'Siparis_Aciklama'  =>  'aciklamalarr',
    	'Taksit'  =>  '1',
    	'Islem_Tutar'  =>  '100',
    	'Toplam_Tutar'  =>  '100',
    	'Islem_ID'  =>  '12345',
    	'Data1'  =>  'data1test',
    	'Data2'  =>  'data2test',
    	'Doviz_Kodu'  =>  1000,
    ]);

#### pay results

    Array
    (
        [status] => 1
        [msg] => İşlem Başarılı
        [errorCode] => 0
        [Islem_ID] => 6018216713
        [url] => https://test-pos.param.com.tr/3D_Secure/AkilliKart_3DPay_EST.aspx?rURL=TURKPOS_3D_TRAN&SID=9fa73bab-f419-4c69-a9d2-3038d975cb90
        [bankCode] => -1
    )

## check3d

    $param->check3d($_POST);

#### check3d results

    Array
    (
        [status] => 
        [msg] => İşlem geçersiz.
        [errorCode] => 0
        [data] => Array
            (
            )
    
    )

## checkPayment

    $param->checkPayment([
    	'Dekont_ID'  =>  3003912366,
    	'Siparis_ID'  =>   2366,
    	'Islem_ID'  => 3012386236,
    ]);

#### checkPayment results

    Array
    (
        [status] => 1
        [msg] => İşlem Başarılı
        [errorCode] => 0
        [data] => Array
            (
                [Odeme_Sonuc] => 1
                [Odeme_Sonuc_Aciklama] => İşlem Başarılı
                [Dekont_ID] => 3003912366
                [Siparis_ID] => TBSEPos_OdemeNS019
                [Islem_ID] => 
                [Durum] => SUCCESS
                [Tarih] => 13.07.2023 17:25:49
                [Toplam_Tutar] => 100
                [Komisyon_Oran] => 1.75
                [Komisyon_Tutar] => 1.72
                [Banka_Sonuc_Aciklama] => 
                [Taksit] => 1
                [Ext_Data] => |||||||||
                [Toplam_Iade_Tutar] => 0
                [KK_No] => 606043******0190
                [Islem_Tip] => SALE
                [Bank_Trans_ID] => 319417004502
                [Bank_AuthCode] => 477436
                [Bank_HostRefNum] => 319417004502
            )
    
    )


