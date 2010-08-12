<?php
/**
 * TeiDisplay_File class
 *
 * @copyright  Scholars' Lab 2010
 * @license    
 * @version    $Id:$
 * @author Ethan Gruber
 * 
 * Used to get listing of xslt files in libraries folder
 **/
class TeiDisplay_File 
{
	protected $_fileName;

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