<?php
	include_once 'main.php';
	include_once '../libs/Curl.php';
	$confname = 'confs.json';
	$filename = 'apikeys.csv';
	$affiliates = setConf($confname);
	foreach($affiliates as $affiliate){
		$refid = $affiliate->refid;
		$path = $affiliate->path;
		$dateformat = $affiliate->dateformat;
		$credentials = setConf($path);
		$formattedates = setDate($dateformat);
		$startdate = $formattedates[0];
		$enddate = $formattedates[1];
		foreach($credentials as $aff){
			$user = $aff->user;
			$campaigns = $aff->campaigns;
			$url = $aff->url;
			foreach($campaigns as $campaign){
				$campaignid = $campaign->campaignid;
				$merchantname = $campaign->merchantname;
				$urlparams = $campaign->urlparams;
				$token = authenticate($filename, $refid);
				$token = trim($token);
				$cwurl = 'https://api.clickwise.net/transactions?from='.$startdate.'&to='.$enddate.'&format=json&limit=1000';
				$options = array('X-ApiKey: '.$token);
				$postbackdate = date('Y-m-d H:i:s');
				$finalurl = $url.$urlparams;
				export($startdate, $enddate, $cwurl, $options, $refid, $postbackdate, $finalurl, $campaignid, $merchantname, $user);
			}
		}
	}
	deletefile($postbackdate);
?>
