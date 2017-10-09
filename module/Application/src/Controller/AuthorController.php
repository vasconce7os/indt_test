<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Author;

class AuthorController extends AbstractActionController
{
    private $messagesFlash = array();
    
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

    public function importAction()
    {       
        $view = new ViewModel();
        $author = new Author();
        $author->setUrlApi($this->configHelp()-> urlApi);
        $form = new \Application\Form\FormAuthorImport('file-form');
        if ($this->getRequest()->isPost()) {
            $data = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            $form->setData($data);
            if ($form->isValid())
            {
                $data = $form->getData();
                $dataPrepared = $this->prepareAuthors($data);
                if($dataPrepared)
                {
                    $author = new Author();
                    $author->setUrlApi($this->configHelp()-> urlApi);
                    $persisted = $author->create($dataPrepared);
                    if(count($persisted) > 0)
                    { 
                        $this->messagesFlash[] = [
                                'text'=> count($persisted) ." new authors has been included",
                                'class'=> "success"
                            ];
                    } else
                    {
                        $this->getResponse()->setStatusCode(500);
                    }
                }
            }
        }
        $view-> setVariable('messagesFlash', $this->messagesFlash);
        $view-> setVariable('form', $form);
        return $view;
    }

    private function prepareAuthors($data = array())
    {
        if(!isset($data['file']['error']) && $data['file']['error'] != 0)
        {
            $this->messagesFlash[] = [
                    'text'=> "Failure to receive file",
                    'class'=> "danger"
                ];
            return false;
        }
        if($data['file']['type'] != "text/plain")
        {
            $this->messagesFlash[] = [
                    'text'=> "Allow only file of type text/plain, you sent a file " . $data['file']['type'],
                    'class'=> "danger"
                ];
            return false;
        }
        $lAuthors = [];
        $f = fopen($data['file']['tmp_name'], 'rb');
        $numberLine = 1;
        $invalidName = 0;
        $validName = 0;
        while($line = fgets($f, 1000))
        {
            $contentLine = trim($line, " \t\n\r\0\x0B");
            $contentLine = preg_replace('/\s+/', ' ', $contentLine);
            if(!empty($contentLine))
            {
                if(str_word_count($contentLine) >= 2)
                {
                    $firstName = explode(" ", $contentLine)[0];
                    $lastName = substr(strstr($contentLine," "), 1);
                    $lAuthors[] = ['firstName'=> $firstName, 'lastName'=> $lastName];
                    $validName++;
                } else
                {
                    $this->messagesFlash[] = [
                            'text'=> "Error in line $numberLine on your file. Names of authors must contain at least two words. The name ($contentLine) cannot be accepted!",
                            'class'=> "danger"
                        ];
                    $invalidName++;
                }
            }
           $numberLine++;
        }
        if(empty($lAuthors))
        {
            
        }
        if(!$invalidName)
        {
            return $lAuthors;
        }
    }
}
