<?php

namespace Application\Form;

use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;

class FormAuthorImport extends Form
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->addElements();
        $this->setInputFilter($this->createInputFilter());
    }

    public function addElements()
    {
        $file = new Element\File('file');
        $file->setLabel('File Input')
            ->setAttributes(array(
                'id' => 'file',
            ));
        $this->add($file);
    }

    public function createInputFilter()
    {
        $inputFilter = new InputFilter\InputFilter();

        $file = new InputFilter\FileInput('file');
        $file->setRequired(true);
        $file->getFilterChain()->attachByName(
            'filerenameupload',
            array(
                'target'          => "./data/tmpuploads/temp_file_".uniqid().".txt",
                'overwrite'       => true,
                'use_upload_name' => false,
            )
        );
        $inputFilter->add($file);

        return $inputFilter;
    }
}