<?php 
namespace App\Security;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $slugger;

    /**
     * contructeur
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * 
     */
    public function upload(UploadedFile $file, $target)
    {
        //dd($target);
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'.'.$file->getClientOriginalExtension();
  
        try{
            $file->move($target, $fileName);
            return $fileName;
        }catch(FileException $e){
            return $e;
        }
    }
}