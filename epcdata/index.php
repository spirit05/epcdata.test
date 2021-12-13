<?php
function writeLog($txt) 
{
    $date = (new Datetime)->format('Y-m-d_H-m');
    $fp = fopen("log". $date .".txt", "w+");
    fwrite($fp, $txt);
    fclose($fp);
}

function googleBot($url)
{
    $header   = [];
    $header[] = 'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5';
    $header[] = 'Cache-Control: max-age=0';
    $header[] = 'Content-Type: text/html; charset=utf-8';
    $header[] = 'Transfer-Encoding: chunked';
    $header[] = 'Connection: keep-alive';
    $header[] = 'Keep-Alive: 300';
    $header[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
    $header[] = 'Accept-Language: en-us,en;q=0.5';
    $header[] = 'Pragma:';

    $agents = [
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1',
        'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1.9) Gecko/20100508 SeaMonkey/2.0.4',
        'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)',
        'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; da-dk) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1',

    ];

    $cookie = tempnam("/tmp", "epcdata");

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, $agents[array_rand($agents)]);
    //curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');
    //curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    //curl_setopt($curl, CURLOPT_REFERER, 'https://www.google.com');
    //curl_setopt($curl, CURLOPT_ENCODING, 'gzip, deflate');
    //curl_setopt($curl, CURLOPT_AUTOREFERER, true);
    curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    //curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
    //curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    //curl_setopt($curl, CURLOPT_MAXREDIRS, 15);
    //curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);

    curl_setopt($curl, CURLOPT_VERBOSE, true);
    /*
            $streamVerboseHandle = fopen('php://temp', 'w+');
            curl_setopt($curl, CURLOPT_STDERR, $streamVerboseHandle);

    */

    $result = curl_exec($curl);
    curl_close($curl);

    /*
            if ($result === false) {
                printf("cUrl error (#%d): %s<br>\n", curl_errno($curl), htmlspecialchars(curl_error($curl)));
            }

            rewind($streamVerboseHandle);
            $verboseLog = stream_get_contents($streamVerboseHandle);

            echo "cUrl verbose information:\n", "<pre>", htmlspecialchars($verboseLog), "</pre>\n";
    */
    $temp = str_replace('<script type="text/javascript" src="https://vkontakte.ru/js/api/share.js?9" charset="windows-1251"></script>', '', $result);
    $temp = str_replace('https://counter.yadro.ru/logo;epcdata?44.1', '', $temp);
    $temp = str_replace('https://counter.yadro.ru/hit;epcdata?r', '', $temp);
    $temp = str_replace('<!--LiveInternet logo--><a href="http://test-catalog/epcdata/http://www.liveinternet.ru/click;epcdata" target="_blank"><img src="" title="LiveInternet" alt="" border="0" width="31" height="31"></a><!--/LiveInternet-->', '', $temp);
    $temp = str_replace("//www.google-analytics.com/analytics.js", '', $temp);

    return $temp;
}

$url = 'https://epcdata.ru/';
if (!empty($_GET['url'])) {
    $url = str_replace(["https:/", "@", "|"], ["https://", '?', '&'], $_GET['url']);
    var_dump($url);
}

$url_info = parse_url($url);
$path = $url_info['path'];

$base_url = $url_info['scheme'] . '://' . $url_info['host'] . $path;
$response = googleBot($url.'/');
var_dump($base_url);

$response = str_replace('display:inline;clear:both;float:left;width:80%;font-size:85%;margin:20px 0 0 50px;', 'display:none;', $response);

$response = preg_replace('#<a.*?>Р‘С‹СЃС‚СЂС‹Р№ РїРѕРёСЃРє СЂР°СЃС…РѕРґРЅРёРєРѕРІ(.*?)</a>#i', '', $response);

$st = '<style>.body{font-size:75%;}.path+h4{display:none;}
.parts-in-stock-widget_parts-table th:nth-child(7),.parts-in-stock-widget_parts-table th:nth-child(8),
.parts-in-stock-widget_parts-table td:nth-child(7),.parts-in-stock-widget_parts-table td:nth-child(8),
.parts-in-stock-widget_part-header-cell, .priceRangeContainer, .priceRangeContainer{display:none;}
.parts-in-stock-widget_part-oem{padding: 3px 5px;background-color: yellow; cursor:pointer;}
</style>';

$html = '<base href="' . $base_url . '">' . $st . $response;
$sc = "<script>
         
            for (let i=0; CurLink=document.links[i]; i++){
                
                CurLink.href= 'http://'+location.hostname+'/epcdata/' + CurLink.href
            }    
           
            for (let i=0; CurLink=document.forms[i]; i++){
                
                let action  = document.forms[i].action;
                console.log('action: ', action);

                document.forms[i].action = 'http://'+location.hostname+'/epcdata/' + document.forms[i].action; 
            }
            

            for (let i=0;CurLink=document.getElementsByClassName('parts-in-stock-widget_part-row')[i]; i++) { 
                let link = CurLink.getElementsByTagName('b')[0];
                console.log(link);
                link.innerHTML = '<a target=\"_blank\" href=\"link\">'+link.innerHTML+'</a>';
            }

    </script>";

$html .= $sc;

writeLog($html);

            $page = fopen("log.txt", "w");
            fwrite($page, $base_url);
            fclose($page);
 echo $html;
?>