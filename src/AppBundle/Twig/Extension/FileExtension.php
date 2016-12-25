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
        return [
            new \Twig_SimpleFunction('file_exists', 'file_exists'),
        ];
    }

    /**
     * @param $file
     * @return bool
     */
    public function file_exists($file)
    {
        return file_exists($file);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_file';
    }
}
