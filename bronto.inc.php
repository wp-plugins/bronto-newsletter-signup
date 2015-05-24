<?php
/**
* @author Scottish Borders Design
*/

class brontoEmailSender
{
	function testConnection($token){
		$wsdl = "https://api.bronto.com/v4?wsdl";
		$url = "https://api.bronto.com/v4";
		 
		$client = new SoapClient($wsdl, array('trace' => 1, 'encoding' => 'UTF-8'));
		$client->__setLocation($url);

		$token = $token;
		$sessionId = $client->login(array("apiToken" => $token))->return;
		$client->__setSoapHeaders(array(new SoapHeader("http://api.bronto.com/v4", 
		                                               'sessionHeader',
		                                               array('sessionId' => $sessionId))));

		return $sessionId;
	}
    
    function addContact($email, $listid, $API_TOKEN){
        $client = new SoapClient('https://api.bronto.com/v4?wsdl', array(
            'trace' => 1,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS
        ));
        
        try {
            $token = $API_TOKEN;
            
            $sessionId = $client->login(array(
                'apiToken' => $token
            ))->return;
            
            $session_header = new SoapHeader("http://api.bronto.com/v4", 'sessionHeader', array(
                'sessionId' => $sessionId
            ));
            $client->__setSoapHeaders(array(
                $session_header
            ));
                       
            $contacts = array(
                'email' => $email,
                'listIds' => $listid
            );
            
            $write_result = $client->addOrUpdateContacts(array(
                $contacts
            ))->return;
            
            if ($write_result->errors) {
                print "There was an error adding you to the newsletter:\n";
                print_r($write_result->results);
            } elseif ($write_result->results[0]->isNew == true) {
                print "You have been added to the newsletter subscription!";
            } else {
                print "You're current subscription has been updated!";
            }
        }
        catch (Exception $e) {
            print "ERROR!\n";
            print_r($e);
        }
    }
    
    function checkEmail($email, $cemail){
        if (!preg_match("/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}/", $email)) {
            die("INVALID");
        }
        
        if ($email != $cemail) {
            die("EMAIL DONT MATCH!");
        }
    }
}