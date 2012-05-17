<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * TeiDisplay_File class
 *
 * Used to get listing of xslt files in libraries folder
 */
class TeiDisplay_File
{
    private $_fileName;

    /**
     * Get the files
     *
     * @return array Array of file names
     */
    public static function getFiles()
    {
        $fileNames = array();
        $paths = new DirectoryIterator(TEI_DISPLAY_STYLESHEET_FOLDER);
        foreach ($paths as $file) {
            if (!$file->isDot() && !$file->isDir()) {
                if (strrchr($file, '.') == '.xsl') {
                    $fileNames[] = $file->getFilename();
                }
            }
        }

        // sort the files by filenames
        natsort($fileNames);
        return $fileNames;
    }	

    /**
     * Get the file name for the file
     * 
     * @return string
     */	
    public function getFileName()
    {
        return $this->_fileName;
    }
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
