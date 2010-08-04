<?
$hostname = trim(`hostname`);

switch ($hostname) {
	default: // Replace with production values
		$DEBUG                  = 0;
		$API_ENDPOINT           = 'http://api.hasoffers.com/Api/json';
	break;
}

/**
 * Application Debugging
 *
 */
Conf::write('DEBUG', $DEBUG);

/**
 * Api Configuraion
 *
 */
Conf::write('ApiClient.Endpoint',         	$API_ENDPOINT);
Conf::write('ApiClient.Version',          	2);
Conf::write('ApiClient.NetworkId',    		'demo');
Conf::write('ApiClient.NetworkToken',    	'NEThSQ1Ah92G8f7JYqQuO5hnsULkia');

/**
 * Api Configuraion
 *
 */
Conf::write('ENV.DefaultOfferId',    		1);
Conf::write('ENV.CompanyName',    		'HasOffers');

