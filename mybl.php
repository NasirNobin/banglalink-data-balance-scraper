<?php

require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Sunra\PhpSimple\HtmlDomParser;
use GuzzleHttp\Cookie\FileCookieJar;

class MyBL
{
	const BASE_URL = 'https://www.onlineservice.banglalink.net';

	public $client;
	public $response;
	public $headers;
	public $html;
	public $login_try = 0;
	public $login = [];

	function __construct()
	{
		$this->login = [
			'mobile'   => '019XXXXXXX',
			'password' => 'YOUR_PASS',
		];

		$this->headers = [
			'Accept-Charset' 	=> 'utf-8',
			'Accept-Language' 	=> 'en-us,en;q=0.7,bn-bd;q=0.3',
			'Accpet' 			=> 'text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5',
			'User-Agent' 		=> 'Mozilla/5.0 (Windows NT 5.2; rv:2.0.1) Gecko/20100101 Firefox/4.0.1',
	    ];

		$cookieJar = new FileCookieJar(__DIR__ . '/mybl.txt');

		$this->client = new Client([
			'base_uri' 		=> self::BASE_URL,
			'cookies' 		=> $cookieJar,
			'headers'		=> $this->headers,
		]);

		$this->show_balance();
	}

	function show_balance(){

		$this->response = $this->client->request('GET', 'https://www.onlineservice.banglalink.net/Customer/ConnectionInfo.aspx?menu=main' );

		$this->html = HtmlDomParser::str_get_html( $this->response->getBody() );

		if(!$this->html) return;

		$action = $this->html->find('.LogInPage #form1',0);
		$action = isset($action->action) ? $action->action : null;

		if( $action ){ // login required

			if( $this->login_try < 1 ){

				$this->login( $this->html );
				$this->show_balance();
			}

			return;
		}

		$text = $this->html->find('#ctl00_ContentPlaceHolderMain_divDataBalance > table > tbody > tr:nth-child(2)', 0);

		preg_match("/Your internet balace is (?<mb>[0-9]+) MB which is valid till (?<date>.+) for 24 Hour/", $text->plaintext, $output);

		if ( isset( $output['mb'] ) ) {

			echo 'MB: ' . $output['mb'] . PHP_EOL;
			echo 'Valid: ' . $output['date'] . PHP_EOL;

		} else {

			echo $text->plaintext;
		}
	}


	function login( $html ) {

		$this->login_try++;

		$action = "https://www.onlineservice.banglalink.net/UserManagement/LoginIE.aspx?Source=BLWebSiteDirectLogInBOS";

		$params          = [];

		foreach ($html->find('input') as $key => $input) {
			if(isset($input->name) && isset($input->value)){
				$params[$input->name] = $input->value;
			}
		}

		$params['txtSubNo']      = $this->login['mobile'];
		$params['txtPassword']   = $this->login['password'];
		$params['chkRememberMe'] = 'on';
		$params['__EVENTTARGET'] = 'btnLogin';

		$this->response = $this->client->request('POST', $action , ['form_params' => $params ]);
	}
}

new MyBL();