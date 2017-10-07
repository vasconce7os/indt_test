<?php
namespace Application\Model;

use Application\Model\Model;

class Author extends Model
{
    public function list()
    {
    	$client = new \GuzzleHttp\Client();
    	try
    	{
			$this-> response = $client->request('GET', $this->getUrlApi());
    	} catch (\Exception $e) 
    	{
    		return false;
    	}
		if($this-> response->getStatusCode() == 200)
		{
			return json_decode($this-> response->getBody());
		} else
		{
			return false;
		}
    }
}