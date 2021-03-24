<?php
    interface APIS
    {
        public function setConf($confname);
        public function setDate($dateformat);
        public function setAPIURL($credentials,$formatteddates,$apiname);
        public function getTransactions($urls, $requestformat, $startdate);
        public function setTrackingVariables($data, $apiname, $campaignid, $bonusaffs);
        public function collectTrackingVariables($totalcost, $commission, $refid, $extradata, $date, $orderid, $currency, $country, $commissiongroupid, $campaignid, $flag, $filter, $productid, $bonusaffs, $variables);
        public function setTrackingURL($apiname, $merchantname, $variables, $actioncodes, $campaignid, $merchantid);
        public function exportcsv($apiname, $merchantname, $totalcost, $orderid, $commission, $campaignid, $refid, $productid, $date, $currency, $url, $countrycode, $extradata, $actioncode);
    }

    class API implements APIS{

        public function setConf($confname){
            $confpath = dirname(__FILE__).'/confs/'.$confname;
            $confile = file_exists($confpath) or exit('File '.$confpath.' does not exist.');
            $json = file_get_contents($confpath);
            $credentials = json_decode($json);
            // print_r($credentials); //-> To see all the credentials from config files
            return $credentials;
        }

        public function setDate($dateformat){
            switch ($dateformat){
                case 'Y-m-dTH:i:sZ':
                    $dateformat = 'Y-m-d H:i:s';
                    $today = date($dateformat);
                    $yesterday = date($dateformat, strtotime('-2 day'));
                    $startdate = urlencode(substr($yesterday, 0, 10).'T'.substr($yesterday, 11).'Z');
                    $enddate = urlencode(substr($today, 0, 10).'T'.substr($today, 11).'Z');
                    break;
                default:
                    $startdate = urlencode(date($dateformat, strtotime('-2 day')));
                    $enddate = urlencode(date($dateformat));
                    break;
            }
            $formatteddates = array($startdate, $enddate);
            return $formatteddates;
        }
        
        public function setAPIURL($credentials,$formatteddates,$apiname){
            $startdate = $formatteddates[0];
            $enddate = $formatteddates[1];
            $urls = array();
            $merchantnames = array();
            $campaignids = array ();
            $campaignbonus = array();
            $urlsandcampaignids = array();
            $options = array();
            foreach ($credentials as $account){
                $accountname = $account->accountname;
                $accountid = $account->accountid;
                $token = $account->token;
                $campaigns = $account->campaigns;
                foreach ($campaigns as $campaign){
                    $merchantid = $campaign->merchantid;
                    $merchantname = $campaign->merchantname;
                    $campaignid = $campaign->campaignid;
                    $fixedactioncodes = $campaign->fixedactioncodes;
                    $bonusaffs = $campaign->affsbonus;
                    switch($apiname){
                        case 'Awin':
                            $url = 'https://api.awin.com/publishers/'.$accountid.'/transactions/?startDate='.$startdate.'&endDate='.$enddate.'&advertiserId='.$merchantid.'&timezone=UTC&accessToken='.$token;
                            break;
                        case 'ImpactRadius':
                            if(strpos($merchantid, '_') != false){
                                $split = explode('_', $merchantid);
                                $merchantid = $split[0];    
                            }    
                            $url = 'https://'.$accountid.':'.$token.'@api.impactradius.com/Mediapartners/'.$accountid.'/Actions?PageSize=1000&CampaignId='.$merchantid.'&StartDate='.$startdate.'&EndDate='.$enddate;
                            break;
                        case 'CasaDelLibro':
                            $url = 'https://api-afiliadoscasadellibro.uintertool.com/api/transactionoverview?api_token='.$token.'&start_date='.$startdate.'&end_date='.$enddate.'&format=json';
                            break;
                        case 'Effiliation':
                            $url = 'http://apiv2.effiliation.com/apiv2/transaction.json?key='.$token.'&start='.$startdate.'&end='.$enddate.'&type=datetran&id_affilieur='.$merchantid;
                            break;
                        case 'PerformanceHorizon':
                            $url = 'https://'.$accountid.':'.$token.'@api.performancehorizon.com/reporting/report_publisher/publisher/'.$accountname.'/conversion.json?convert_currency=USD&start_date='.$startdate.'&multipivot[campaign][]='.$merchantid;
                            break;
                        case 'TradeTracker':
                            $url = 'http://ws.tradetracker.com/soap/affiliate?wsdl&'.$accountid.'&'.$token.'&'.$merchantid;
                            break;
                        case 'TradeDoubler':
                            $url = 'https://reports.tradedoubler.com/pan/aReport3Key.action?metric1.summaryType=NONE&metric1.lastOperator=/&metric1.columnName2=orderValue&metric1.operator1=/&metric1.columnName1=orderValue&metric1.midOperator=/&customKeyMetricCount=0&columns=orderValue&columns=pendingReason&columns=orderNR&columns=leadNR&columns=link&columns=affiliateCommission&columns=device&columns=vendor&columns=browser&columns=os&columns=deviceType&columns=voucher_code&columns=open_product_feeds_name&columns=open_product_feeds_id&columns=productValue&columns=productNrOf&columns=productName&columns=graphicalElementName&columns=siteName&columns=pendingStatus&columns=eventId&columns=eventName&columns=epi1&columns=lastModified&columns=timeInSession&columns=timeOfEvent&columns=timeOfVisit&columns=programId&columns=programName&includeWarningColumn=true&dateSelectionType=1&filterOnTimeHrsInterval=false&event_id=0&includeMobile=1&breakdownOption=1&sortBy=timeOfEvent&pending_status=1&currencyId=USD&latestDayToExecute=0&setColumns=true&reportTitleTextKey=REPORT3_SERVICE_REPORTS_AAFFILIATEEVENTBREAKDOWNREPORT_TITLE&reportName=aAffiliateEventBreakdownReport&organizationId='.$accountid.'&key='.$token.'&endDate='.$enddate.'&startDate='.$startdate.'&ProgramId='.$merchantid.'&format=XML';
                            break;
                        case 'Zooplus':
                            $url = 'https://export.net.zooplus.com/'.$token.'/reporttransactions_'.$accountid.'.xml?filter[currencycode]=EUR&filter[zeitraumvon]='.$startdate.'&filter[zeitraumbis]='.$enddate.'&filter[zeitraumAuswahl]=absolute&filter[a:status]=0';
                            break;
                        case 'Rakuten':
                            $credentials = explode(' ', $accountname);
                            $user = $credentials[0];
                            $password = $credentials[1];
                            $url = 'https://api.rakutenmarketing.com/token&'.$token.'&'.$accountid.'&'.$user.'&'.$password.'&'.'https://api.rakutenmarketing.com/events/1.0/transactions?limit=1000&process_date_start='.$startdate.'&'.$merchantid;
                            break;
                        case 'Admitads':
                            $url = 'https://api.admitad.com/token/&'.$token.'&'.$accountid.'&'.$accountname.'&'.'https://api.admitad.com/statistics/actions/?date_start='.$startdate.'&date_end='.$enddate.'&campaign='.$merchantid;
                            break;
                        case 'Optimise':
                            date_default_timezone_set("UTC");
                            $t = microtime(true);
                            $micro = sprintf("%03d",($t - floor($t)) * 1000);
                            $sig_data = gmdate('Y-m-d H:i:s.', $t).$micro;
                            $data = explode(' ', $token);
                            $api_key = $data[0];
                            $privatekey = $data[1];
                            $concatedata = $privatekey.$sig_data;
                            $sig = md5($concatedata);
                            $sig_data = urlencode($sig_data);
                            $url = 'https://api.omgpm.com/network/OMGNetworkApi.svc/v1.2.1/Reports/Affiliate/TransactionsOverview?AID='.$accountid.'&AgencyID=118&Status=-1&StartDate='.$startdate.'&EndDate='.$enddate.'&DateType=0&UIDRestrict=False&PaidOnly=False&Key='.$api_key.'&Sig='.$sig.'&SigData='.$sig_data.'&Mid='.$merchantid.'&Output=Json';
                            break;
                        case 'TimeOne':
                            $url = 'http://api.publicidees.com/subid.php5?p='.$accountid.'&k='.$token.'&dd='.$startdate.'&df='.$enddate.'&csv=1'; //&pg='.$merchantid;
                        break;
                    }
                    array_push($urls, $url);
                    $merchantid = $campaign->merchantid;
                    $merchantnames += array($merchantid => $merchantname);
                    $campaignbonus += array($campaignid => $bonusaffs);
                    $campaignids += array($campaignid => $fixedactioncodes);
                }
            }
            $urlsandcampaignids = array($urls, $campaignids, $merchantnames, $campaignbonus);
            // print_r($urlsandcampaignids); //-> To see all network URLs and campaigns for each URL. 
            return $urlsandcampaignids;
        }

        public function getTransactions($url, $requestformat, $startdate){
            $data = array();
            $firstpos = strpos($url, '*');
            $lastpos = strrpos($url, '*');
            $alloptions = substr($url, $firstpos, $lastpos);
            $alloptions = trim($alloptions, '*');
            $options = explode(',', $alloptions);
            switch($requestformat){
                case 'JSON':
                    $transactions = new Curl($url, $options);
                    $transactions->request("GET");
                    $data = json_decode($transactions->response, true);
                    break;
                case 'XML':
                    $transactions = new Curl($url, $options);
                    $transactions->request("GET");
                    $data = simplexml_load_string($transactions->response);
                    break;
                case 'TT':
                    $customerid = 146342;
                    $startdate = date('Y-m-d', strtotime('-2 day'));
                    $date = array (
                        'registrationDateFrom' => $startdate,
                    );
                    $url = explode('&', $url);
                    $accountid = (int) $url[1];
                    $token = $url[2];
                    $merchantid = $url[3];
                    $url = $url[0];
                    $client = new SoapClient($url, array('compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
                    $client->authenticate($customerid, $token);
                    $transactions = $client->getConversionTransactions($accountid, $date);
                    foreach($transactions as $transaction){
                        $ttcampaignid = $transaction->campaign->ID;
                        if($ttcampaignid == $merchantid){
                            array_push($data, $transaction);
                        }
                    }
                    break;
                case 'RK':
                    $url = explode('&', $url);
                    $authurl = $url[0];
                    $token = $url[1];
                    $accountid = $url[2];
                    $user = $url[3];
                    $password = $url[4];
                    $transactionsurl = $url[5].'&'.$url[6];
                    $startdate = $url[6];
                    $merchantid = $url[7];
                    $credentials = array("Authorization: ".$token);
                    $method = 'POST-PAYLOAD';
                    $accountdata = array(
                        "grant_type" => "password",
                        "username" => $user,
                        "password" => $password,
                        "scope" => $accountid //This id is located inside "My Account" section.
                    );
                    $r = new Curl($authurl, $credentials);
                    $r->request($method, $accountdata);
                    $bear = $r->obj->access_token;
                    $credentials = array("Authorization: Bearer ".$bear);
                    $t = new Curl($transactionsurl, $credentials);
                    $t->request("GET");
                    foreach($t->obj as $transaction){
                        $rkcampaignid = $transaction->advertiser_id;
                        $isevent = $transaction->is_event;
                        if($rkcampaignid == $merchantid && $isevent == 'Y'){
                            array_push($data, $transaction);
                        }
                    }
                    break;
                case 'AA':
                    $url = explode('&', $url);
                    $authurl = $url[0];
                    $token = $url[1];
                    $user = $url[2];
                    $password = $url[3];
                    $transurl = $url[4];
                    $enddate = $url[5];
                    $merchantid = $url[6];
                    $transactionsurl = $transurl.'&'.$enddate.'&'.$merchantid;
                    $credentials = array("Authorization: ".$token);
                    $method = 'POST';
                    $accountdata = array(
                        "grant_type" => "client_credentials",
                        "client_id" => $user,
                        "username" => $user,
                        "password" => $password,
                        "scope" => "statistics" //This id is located inside "My Account" section.
                    );
                    $r = new Curl($authurl, $credentials);
                    $r->request($method, $accountdata);
                    $bear = $r->obj->access_token;
                    $credentials = array("Authorization: Bearer ".$bear);
                    $t = new Curl($transactionsurl, $credentials);
                    $t->request("GET");
                    foreach($t->obj->results as $transaction){
                        array_push($data, $transaction);
                    }
                    break;
                case 'CSV':
                    $data = file_get_contents($url);
                    $data = str_replace('"', '', $data);
                    $data = explode(PHP_EOL, $data);
                    $count = count($data)-1;
                    unset($data[0]);
                    unset($data[$count]);    
                    break;
            }
            // print_r($data); //-> To see all data inside all the transactions.
            return $data;
        }

        public function setTrackingVariables($data, $apiname, $campaignid, $bonusaffs){
            $flag = 'false';
            $variables = array();
            $api = new API();
            $filter = 'false'; //Set true inside the campaign you want to measure all conversions.
            $productid = '';
            switch($apiname){
                case 'Awin':
                    foreach ($data as $transaction){
                        $totalcost = round(floatval($transaction['saleAmount']['amount']), 2);
                        $commission = round(floatval($transaction['commissionAmount']['amount']), 2);
                        $data = explode('_', $transaction['clickRefs']['clickRef']);
                        $refid = $data[0];
                        $extradata = $data[1];
                        $date = substr($transaction['transactionDate'], 0, 10);
                        $orderid = $transaction['id'];
                        $currency = $transaction['saleAmount']['currency'];
                        $country = $transaction['customerCountry'];
                        $commissiongroupid = $transaction['transactionParts'][0]['commissionGroupName'];
                        $variables = $api->collectTrackingVariables($totalcost, $commission, $refid, $extradata, $date, $orderid, $currency, $country, $commissiongroupid, $campaignid, $flag, $filter, $productid, $bonusaffs, $variables);
                }
                    break;
                case 'ImpactRadius':
                    foreach ($data->Actions->Action as $transaction) {
                        $orderid = (string)$transaction->Id;
                        $commission = $transaction->Payout;
                        $totalcost = $transaction->Amount;
                        $commission = str_replace(",", ".", $commission);
                        $totalcost = str_replace(",", ".", $totalcost);
                        $commission = round($commission,2);
                        $totalcost = round($totalcost,2);
                        $refid = (string)$transaction->SubId1;
                        $extradata = (string)$transaction->SubId2;
                        $firstdate = (string)$transaction->CreationDate;
                        $date = substr($firstdate, 0,10);
                        $currency = (string)$transaction->Currency;
                        $country = (string)$transaction->CustomerCountry;
                        $commissiongroupid = (string)$transaction->ActionTrackerId;
                        $variables = $api->collectTrackingVariables($totalcost, $commission, $refid, $extradata, $date, $orderid, $currency, $country, $commissiongroupid, $campaignid, $flag, $filter, $productid, $bonusaffs, $variables);
                    }
                    break;
                case 'CasaDelLibro':
                    foreach ($data['data']['report'] as $transaction) {
                        $orderid = $transaction['cid'];
                        $commission = round($transaction['total_ppubcom'], 2);
                        $totalcost = round($transaction['cval_total'], 2);
                        $data = explode('_', $transaction['ei1']);
                        $refid = $data[0];
                        $extradata = $data[1];
                        $date = substr($transaction['tim'], 0, 10);
                        $commissiongroupid = '';
                        $currency = 'EUR';
                        $country = 'ES';
                        $variables = $api->collectTrackingVariables($totalcost, $commission, $refid, $extradata, $date, $orderid, $currency, $country, $commissiongroupid, $campaignid, $flag, $filter, $productid, $bonusaffs, $variables);
                    }
                    break;
                case 'Effiliation':
                    foreach ($data['rapport_effi_id'] as $transaction) {
                        $totalcost = round(floatval($transaction['montant']), 2);
                        $commission = round(floatval($transaction['commission']), 2);
                        $refid = $transaction['effi_id'];
                        $extradata = $transaction['effi_id2'];
                        $programname = $transaction['nom_programme'];
                        $orderid = $transaction['ref'];
                        $date = str_replace("/","-",substr($transaction['datetran'], 0, 10));
                        $currency = 'EUR';
                        $commissiongroupid = '';
                        $country = '';
                        $variables = $api->collectTrackingVariables($totalcost, $commission, $refid, $extradata, $date, $orderid, $currency, $country, $commissiongroupid, $campaignid, $flag, $filter, $productid, $bonusaffs, $variables);
                    }
                    break;
                case 'PerformanceHorizon':
                    $counter = 0;
                    foreach ($data['conversions'] as $transaction) {
                        $route = $data['conversions'][$counter]['conversion_data'];
                        $orderid = $route['conversion_reference'];
                        $refid = $route['publisher_reference'];
                        $extradata = '';
                        if(strpos($refid, '_') != false){
                            $explode = explode("_", $refid);
                            $refid = $explode[0];
                            $extradata = $explode[1];
                        }
                        $totalcost = round($route['conversion_value']['value'], 2);
                        $commission = round($route['conversion_value']['publisher_commission'], 2);
                        $currency = 'USD';
                        $country = $route['country'];
                        $date = substr($route['conversion_time'], 0, 10);
                        $commissiongroupid = '';
                        $counter++;
                        $variables = $api->collectTrackingVariables($totalcost, $commission, $refid, $extradata, $date, $orderid, $currency, $country, $commissiongroupid, $campaignid, $flag, $filter, $productid, $bonusaffs, $variables);
                    }
                    break;
                case 'TradeTracker':
                    foreach ($data as $transaction) {
                        $orderid = $transaction->ID;
                        $commission = round($transaction->commission,2);
                        $totalcost = $transaction->orderAmount;
                        $refid = substr($transaction->reference, 0, 13);
                        $extradata = substr($transaction->reference, 14);            
                        // $refid = $transaction->reference;
                        // $extradata = '';
                        // if(strpos($refid, ',') != false){
                        //     $explode = explode(",", $refid);
                        //     $refid = $explode[0];
                        //     $extradata = $explode[1];
                        // }
                        $firstdate = $transaction->registrationDate;
                        $date = substr($firstdate, 0,10);
                        $currency = $transaction->currency;
                        $country = $transaction->countryCode;
                        $commissiongroupid = $transaction->transactionType;
                        $variables = $api->collectTrackingVariables($totalcost, $commission, $refid, $extradata, $date, $orderid, $currency, $country, $commissiongroupid, $campaignid, $flag, $filter, $productid, $bonusaffs, $variables);
                    }
                    break;
                case 'TradeDoubler':
                    foreach ($data->matrix->rows->row as $transaction){
                        $totalcost = round(floatval($transaction->orderValue), 2);
                        $date = substr($transaction->timeOfEvent, 0, 10);
                        $orderid = (string) $transaction->orderNR;
                        $commission = round(floatval($transaction->affiliateCommission), 2);
                        $refid = $transaction->epi1;
                        $extradata = '';
                        if(strpos($refid, '_') != false){
                            $explode = explode("_", $refid);
                            $refid = $explode[0];
                            $extradata = $explode[1];
                        }
                        $country = '';
                        $currency = 'USD';
                        $commissiongroupid = (string) $transaction->eventId;
                        $merchantid = $transaction->programId;
                        switch($merchantid){
                            case '224465':
                                $commissiongroupid = $merchantid;
                                break;
                            
                        }
                        $variables = $api->collectTrackingVariables($totalcost, $commission, $refid, $extradata, $date, $orderid, $currency, $country, $commissiongroupid, $campaignid, $flag, $filter, $productid, $bonusaffs, $variables);
                    }
                    break;
                case 'Zooplus':
                    foreach ($data->transaction as $transaction){
                        $country = strtoupper(substr($transaction->advertiser_label,8,2));
                        $totalcost = $transaction->conversion_order_value_eur;
                        $orderid = $transaction->conversion_id;
                        $commission = $transaction->conversion_commission_total_eur;
                        $refid = $transaction->click_subid;
                        $extradata = '';
                        if(strpos($refid, ' | ') != false){
                            $data = explode(' | ', $transaction->click_subid);
                            $refid = $data[0];
                            $extradata = $data[1];    
                        }
                        $date = substr($transaction->conversion_tracking_time, 0,10);
                        $currency = $transaction->conversion_currency_code;
                        $commissiongroupid = $transaction->conversion_product_category_id;
                        $variables = $api->collectTrackingVariables($totalcost, $commission, $refid, $extradata, $date, $orderid, $currency, $country, $commissiongroupid, $campaignid, $flag, $filter, $productid, $bonusaffs, $variables);
                    }
                    break;
                case 'Rakuten':
                    foreach ($data as $transaction){
                        $country = '';
                        $totalcost = $transaction->sale_amount;
                        $orderid = $transaction->etransaction_id;
                        $commission = round($transaction->commissions, 2);
                        $refid = $transaction->u1;
                        $extradata = '';
                        if(strpos($refid, '_') != false){
                            $explode = explode("_", $refid);
                            $refid = $explode[0];
                            $extradata = $explode[1];
                        }
                        $darr = date_parse($transaction->transaction_date);
                        $date  = $darr["year"] . "-" . $darr["month"] . "-" . $darr["day"];
                        $currency = $transaction->currency;
                        $commissiongroupid = $transaction->product_name;
                        $variables = $api->collectTrackingVariables($totalcost, $commission, $refid, $extradata, $date, $orderid, $currency, $country, $commissiongroupid, $campaignid, $flag, $filter, $productid, $bonusaffs, $variables);
                    }
                    break;
                case 'Admitads':
                    foreach ($data as $transaction){
                        $country = $transaction->click_country_code;
                        $totalcost = $transaction->cart;
                        $orderid = $transaction->order_id;
                        $commission = round($transaction->payment, 2);
                        $refid = trim($transaction->subid);
                        $extradata = '';
                        if ($refid == '{$refid}'){
                            $refid = 'NULL';
                        }
                        if(strpos($refid, '_') !== false){
                            $data = explode('_', $transaction->subid);
                            $refid = $data[0];
                            $extradata = $data[1];
                        }            
                        $date = substr($transaction->action_date, 0, 10);
                        $currency = $transaction->currency;
                        $commissiongroupid = $transaction->tariff_id;
                        $variables = $api->collectTrackingVariables($totalcost, $commission, $refid, $extradata, $date, $orderid, $currency, $country, $commissiongroupid, $campaignid, $flag, $filter, $productid, $bonusaffs, $variables);
                    }
                    break;
                case 'Optimise':
                    foreach ($data as $transaction){
                        $campaignid = $transaction['PID'];
                        $productid = explode(' ', $transaction['Product']);
                        $country = '';
                        $totalcost = $transaction['TransactionValue'];
                        $orderid = $transaction['MerchantRef'];
                        $commission = round($transaction['NVR'], 2);
                        $refid = $transaction['UID'];
                        $extradata = $transaction["UID2"];
                        $date = substr($transaction['TransactionTime'], 0,10);
                        $date = explode('/', $date);
                        $year = $date[2];
                        $month = $date[1];
                        $day = $date[0];                   
                        $date = $year."-".$month."-".$day;                 
                        $currency = trim($transaction['Currency']);
                        $commissiongroupid = $transaction['PID'];
                        foreach ($productid as $key => $item){
                            if (strlen($item) == 2){
                                $country = $productid[$key];
                            }
                        }
                        $productid = str_replace(' ', '', $transaction['Product']);
                        $variables = $api->collectTrackingVariables($totalcost, $commission, $refid, $extradata, $date, $orderid, $currency, $country, $commissiongroupid, $campaignid, $flag, $filter, $productid, $bonusaffs, $variables);
                    }
                    break;
                case 'TimeOne':
                    foreach ($data as $line){
                        $transaction = explode(';', $line);
                        $campaignid = $transaction[0];
                        $orderid = $transaction[2];
                        $refid = $transaction[3];
                        $extradata = '';
                        $flag = strpos($refid, '_');
                        if($flag != false){
                            $refs = explode('_', $refid);
                            $refid = $refs[0];
                            $extradata = $refs[1];
                        }
                        $date = substr($transaction[4], 0, 10);
                        $totalcost = $transaction[10];
                        $commission = $transaction[9];
                        $country = '';
                        $currency = 'USD';
                        $commissiongroupid = $transaction[12];
                        $filter = 'true';
                        $variables = $api->collectTrackingVariables($totalcost, $commission, $refid, $extradata, $date, $orderid, $currency, $country, $commissiongroupid, $campaignid, $flag, $filter, $productid, $bonusaffs, $variables);
                    }
                    break;
            }
            // print_r($variables); //-> Too see data for each transaction.
            return $variables;
        }

        public function collectTrackingVariables($totalcost, $commission, $refid, $extradata, $date, $orderid, $currency, $country, $commissiongroupid, $campaignid, $flag, $filter, $productid, $bonusaffs, $variables){
            if (in_array($refid, $bonusaffs)) {
                $flag = 'true';
            }
            $vars = array('totalcost' => $totalcost,'commission' => $commission,'refid' => $refid,'extradata' => $extradata,'date' => $date,'orderid' => $orderid, 'productid' => $productid, 'currency' => $currency,'country' => $country,'commissiongroupid' => $commissiongroupid,'campaignid' => $campaignid, 'flag' => $flag, 'filter' => $filter);
            array_push($variables, $vars);
            return $variables;
        }

        public function errorLog($error, $filter){
            $filedate = date("Ymd");
            $date = date("Y-m-d H:i:s");
            $filename = 'log/'.$filedate."_APIS_error.csv";
            $columns = 'DateTime;Message;Filter';
    
            if(file_exists($filename))
            {
                $message = "File '$filename' has been modified.";
            }
            else
            {
                $message = "File '$filename' has been created.";
                $file = fopen($filename, "a");
                fwrite($file, $columns.PHP_EOL);
                fclose($file);
            }
            // echo $message.PHP_EOL;
            $file = fopen($filename, "a");
            fwrite($file, $date.';'.$error.';'.$filter.PHP_EOL);
            fclose($file);
        }

        public function exportcsv($apiname, $merchantname, $totalcost, $orderid, $commission, $campaignid, $refid, $productid, $date, $currency, $url, $countrycode, $extradata, $actioncode){
            $filedate = date("Ymd");
            $filename = 'reports/'.$filedate."_APIS_conversions.csv";
            $columns = 'ApiName;CampaignID;CampaignName;OrderID;TotalCost;Commission;CountryCode;RefID;ProductID;Date;Currency;ExtraData;TrackingURL;';
    
            if(file_exists($filename))
            {
                $message = "File '$filename' has been modified.";
            }
            else
            {
                $message = "File '$filename' has been created.";
                $file = fopen($filename, "a");
                fwrite($file, $columns.PHP_EOL);
                fclose($file);
            }
            // echo $message.PHP_EOL;
            $file = fopen($filename, "a");
            if($actioncode != false){
                $commission = $actioncode;
            }
            fwrite($file, $apiname.';'.$campaignid.';'.$merchantname.';'.$orderid.';'.$totalcost.';'.$commission.';'.$countrycode.';'.$refid.';'.$productid.';'.$date.';'.$currency.';'.$extradata.';'.$url.';'.PHP_EOL);
            fclose($file);
        } 
    
        public function setTrackingURL($apiname, $merchantname, $variables, $actioncodes, $campaignid, $merchantid){
            $actioncodes = (array)$actioncodes;
            $transactioncounter = 0;
            $api = new API();
            foreach ($variables as $transaction){
                $transactioncampaignid = $transaction['campaignid'];
                $countrycode = $transaction['country'];
                if(strpos($campaignid, '_') != false){
                    $data = explode('_', $campaignid);
                    $campaignid = $data[0];
                    $countrycode = $data[1];
                    if(count($data) >= 3){
                        $productid = $data[2];
                    }
                }
                $totalcost = $transaction['totalcost'];
                $orderid = $transaction['orderid'];
                $productid = $transaction['productid'];
                $commission = $transaction['commission'];
                $refid = $transaction['refid'];
                $date = $transaction['date'];
                $currency = $transaction['currency'];
                $extradata = $transaction['extradata'];
                $commissiongroupid = $transaction['commissiongroupid'];
                $flag = $transaction['flag'];
                $filter = $transaction['filter'];
                $actioncode = array_search($commissiongroupid, $actioncodes);
                if($refid == 'NULL'){
                    $error = 'The refid is not set with a valid value for transaction nº '.$orderid.' inside campaign '.$merchantname.' from API '.$apiname.'. Default Pampa Test refid is set.';
                    $filter = 'Refid';
                    $api->errorLog($error, $filter);
                    $refid = '4f99af95c9ae7';
                }
                if($extradata == 'NULL'){
                    $error = 'The extra data is not set with a valid value for transaction nº '.$orderid.' inside campaign '.$merchantname.' from API '.$apiname.'. Default extra data value is set.';
                    $filter = 'ExtraData';
                    $api->errorLog($error, $filter);
                    $extradata = '';
                }
                if($flag == 'true'){
                    $actioncode = $actioncode.'bonus';
                }
                $transactioncounter = $api->insertCommission($filter, $merchantid, $apiname, $merchantname, $totalcost, $orderid, $commission, $campaignid, $refid, $date, $currency, $countrycode, $extradata, $actioncode, $transactioncounter, $transactioncampaignid, $productid);
            }
            echo PHP_EOL.'Number of transactions: '.$transactioncounter.PHP_EOL;
        }

        public function insertCommission($filter, $merchantid, $apiname, $merchantname, $totalcost, $orderid, $commission, $campaignid, $refid, $date, $currency, $countrycode, $extradata, $actioncode, $transactioncounter, $transactioncampaignid, $productid){
            if ($actioncode == false){
                $url = 'http://track.clickwise.net/pb?CountryCode='.$countrycode.'&TotalCost='.$totalcost.'&OrderId='.$orderid.'&Commission='.$commission.'&CampaignID='.$campaignid.'&RefId='.$refid.'&Date='.$date.'&Currency='.$currency.'&ProductID='.$productid.'&ExtraData1='.$extradata;
            }
            else{
                $url = 'http://track.clickwise.net/pb?CountryCode='.$countrycode.'&TotalCost='.$totalcost.'&OrderId='.$orderid.'&ActionCode='.$actioncode.'&CampaignID='.$campaignid.'&RefId='.$refid.'&Date='.$date.'&ExtraData=1'.$extradata.'&ProductID='.$productid;
            }
            if($filter == 'true'){
                if($transactioncampaignid == $merchantid){
                    echo $url.PHP_EOL;
                    $transactioncounter++;
                    // $s2s = new Curl($url);
                    // $s2s->request("GET");
                    // $api = new API();
                    // $api->exportcsv($apiname, $merchantname, $totalcost, $orderid, $commission, $campaignid, $refid, $date, $currency, $url, $countrycode, $extradata, $actioncode); //-> To see all the information inside a csv file.
                }
            }
            else{
                echo $url.PHP_EOL;
                $transactioncounter++;
                // $s2s = new Curl($url);
                // $s2s->request("GET");
                // $api = new API();
                // $api->exportcsv($apiname, $merchantname, $totalcost, $orderid, $commission, $campaignid, $refid, $date, $currency, $url, $countrycode, $extradata, $actioncode); //-> To see all the information inside a csv file.
            }
            return $transactioncounter;
        }
    }
?>