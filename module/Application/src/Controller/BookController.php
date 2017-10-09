<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Book;

class BookController extends AbstractActionController
{
    private $messagesFlash = array();

    public function indexAction()
    {
    	$book = new Book();
    	$book-> setUrlApi($this->configHelp()-> urlApi);
    	$books = $book->list();
    	if($books)
    	{
        	return new ViewModel(array('lBooks'=> $books));
        } else
        {
        	$this->getResponse()->setStatusCode(501);
        	return;
        }
    }

    public function importAction()
    {       
        $view = new ViewModel();
        $book = new Book();
        $book->setUrlApi($this->configHelp()-> urlApi);
        $form = new \Application\Form\FormBookImport('file-form');
        if ($this->getRequest()->isPost())
        {
            $data = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            $form->setData($data);
            if ($form->isValid())
            {
                $data = $form->getData();
                $dataPrepared = $this->prepareBooks($data);
                if($dataPrepared)
                {
                    $book = new Book();
                    $book->setUrlApi($this->configHelp()-> urlApi);
                    $persisted = $book->create($dataPrepared);
                    if($persisted && count($persisted) > 0)
                    { 
                        $this->messagesFlash[] = [
                                'text'=> count($persisted) ." new books has been saved",
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

    private function prepareBooks($data = array())
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
        $lBooks = [];
        $f = fopen($data['file']['tmp_name'], 'rb');
        $numberLine = 1;
        $lastMetaInfo = null;
        $lastTitle = null;
        while($line = fgets($f, 1000))
        {
            $contentLine = trim($line, " \t\n\r\0\x0B");
            $contentLine = preg_replace('/\s+/', ' ', $contentLine);
            if(!empty($contentLine))
            {
                if($lastMetaInfo == null || $lastMetaInfo == "authorId")
                {
                    if(preg_match("/[a-z]/i", $contentLine))
                    {
                        $lastTitle = $contentLine;
                        $lastMetaInfo = "title";
                    } else
                    {
                        $this->messagesFlash[] = [
                            'text'=> "Error in line $numberLine on your file. It was expected a value corresponding to a book's title. \"$contentLine\" is not a valid title",
                            'class'=> "danger"
                        ];
                        return null;
                    }
                } else  // authorId
                {
                    if(ctype_digit($contentLine))
                    {
                        $lastAuthorId = $contentLine;
                        $lastMetaInfo = "authorId";
                    } else
                    {
                        $this->messagesFlash[] = [
                            'text'=> "Error in line $numberLine on your file. It was expected a value corresponding to a author's code. \"$contentLine\" is not a valid code author",
                            'class'=> "danger"
                        ];
                        return null;
                    }
                    $lBooks[] = ['title'=> $lastTitle, 'authorId'=> $lastAuthorId];
                }
            }
           $numberLine++;
        }
        if($lastMetaInfo == "title")
        {
            $this->messagesFlash[] = [
                'text'=> "The he last line must a code of author",
                'class'=> "danger"
            ];
            return null;
        }else if(empty($lBooks))
        {
            $this->messagesFlash[] = [
                'text'=> "No record was found in the file received",
                'class'=> "danger"
            ];
            return null;
        }
        return $lBooks;
    }
}
