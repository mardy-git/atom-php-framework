<?php
$url = $_REQUEST['url'];
$method = $_REQUEST['method'];
$content = $_REQUEST['content'];

$curlCon = curl_init($url);
curl_setopt($curlCon,CURLOPT_HEADER,true);
curl_setopt($curlCon,CURLOPT_NOBODY,false);
curl_setopt($curlCon, CURLOPT_CUSTOMREQUEST,$method);
$headers  =  array("Content-type: application/atom+xml");
curl_setopt($curlCon,CURLOPT_HTTPHEADER,$headers);
curl_setopt($curlCon, CURLINFO_HEADER_OUT, true);
curl_setopt($curlCon, CURLOPT_RETURNTRANSFER,true);
curl_setopt($curlCon, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curlCon, CURLOPT_POSTFIELDS, $content);
$pageContent = curl_exec($curlCon);
$request = curl_getinfo($curlCon,CURLINFO_HEADER_OUT);
echo "<h2>Response</h2>";
echo nl2br(htmlspecialchars($pageContent));
echo "<hr>";
echo "<h2>Request Header</h2>";
echo nl2br($request);
?>