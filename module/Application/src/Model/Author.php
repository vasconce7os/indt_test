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
			$this-> response = $client->request('GET', $this->getUrlApi(). "/authors");
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

    public function create($data = array())
    { 
        $client = new \GuzzleHttp\Client();
        try
        {
            $this-> response = $client->request('POST', $this->getUrlApi(). "/authors", ['json' => $data]);
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