<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Author;

class AuthorController extends AbstractActionController
{
    public function indexAction()
    {        	
    	$author = new Author();
    	$author-> setUrlApi($this->configHelp()-> urlApi);
    	$authors = $author->list();
    	if($authors)
    	{
        	return new ViewModel(array('lAuthors'=> $authors));
        } else
        {
        	$this->getResponse()->setStatusCode(501);
        	return;
        }
    }

}
