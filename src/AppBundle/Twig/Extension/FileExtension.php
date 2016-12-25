<?php

namespace AppBundle\Twig\Extension;

class FileExtension extends \Twig_Extension
{
    /**
     * Return the functions registered as twig extensions
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            //new \Twig_Function('checkUrl', array($this, 'checkUrl')),
            new \Twig_SimpleFunction('file_exists', 'file_exists'),
        );
    }

    public function file_exists($file) {
        return file_exists($file);
    }

    public function getName()
    {
        return 'app_file';
    }
}
