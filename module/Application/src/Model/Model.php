<?php
namespace Application\Model;

class Model
{
	private $urlApi;

	public $response;

    public function setUrlApi($urlApi)
    {
    	$this->urlApi = $urlApi;
    }

    public function getUrlApi()
    {
    	return $this->urlApi;
    }
}