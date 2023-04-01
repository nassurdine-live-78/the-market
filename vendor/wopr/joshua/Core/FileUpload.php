<?php
    declare(strict_types=1);

    namespace Joshua\Core;

    class FileUpload
    {

        private string  $targetUploadDir    = "/";
        private array   $allowedMIME        = [];
        private int     $maxFileSize;

        public function __construct(string $uploadDir)
        {
            $this->targetUploadDir  = $uploadDir;
            $this->maxFileSize      = min($this->convertPHPSizeToBytes(ini_get('post_max_size')), $this->convertPHPSizeToBytes(ini_get('upload_max_filesize')));
        }

        public function setAllowedMIME(array $mimes) :void
        {
            $this->allowedMIME = $mimes;
        }

        public function setMaxFileSize(int $max) :void
        {
            $serverMax = min($this->convertPHPSizeToBytes(ini_get('post_max_size')), $this->convertPHPSizeToBytes(ini_get('upload_max_filesize')));

            if($max > $serverMax) 
            {
                throw new \Exception("Max file size higher than server settings");
            }
            else
            {
                $this->maxFileSize = $max;
            }

        }

        private function isAllowedMIME(string $tmpFile, string $srcMIME) :bool
        {
            $allowed = FALSE;

            if($srcMIME === mime_content_type($tmpFile))
            {
                foreach($this->allowedMIME as $mime)
                {
                    if($mime === $srcMIME)
                    {
                        $allowed = TRUE;
                        break;
                    }

                }
            }
            else
            {
                $allowed = FALSE;
            }
            return $allowed;
        }

        private function isAllowedFileSize(int $size) :bool
        {
            return $this->maxFileSize >= $size;
        }

        public function process(array $files) :?array
        {
            if(isset($files["name"]) && isset($files["type"]) && isset($files["tmp_name"]) && isset($files["error"]) && isset($files["size"]))
            {
                $targetFilename = NULL;
                switch(gettype($files["name"]))
                {
                    case "string":
                        $targetFilename = "";
                        do
                        {
                            $id = uniqid() . "0";
                            $targetFilename = $id . "-". date("Y-m-d") . "." . pathinfo($files["name"], PATHINFO_EXTENSION);
                        } while(file_exists($this->targetUploadDir . "/" . $targetFilename));

                        break;
                    case "array":
                        $targetFilename = [];
                        $i = 0;
                        foreach($files["name"] as $name)
                        {
                            $tmpName = "";
                            do
                            {
                                $id = uniqid() . "" . $i;
                                $tmpName = $id . "-". date("Y-m-d") . "." . pathinfo($name, PATHINFO_EXTENSION);
                            } while(file_exists($this->targetUploadDir . "/" . $tmpName));
                            array_push($targetFilename, $tmpName);
                            $i++;
                        }
                        break;
                }

                $allowedMIME    = TRUE;
                switch(gettype($files["type"]))
                {
                    case "string":
                        if(!$this->isAllowedMIME($files["tmp_name"], $files["type"]))
                        {
                            $allowedMIME = FALSE;
                        }
                        break;
                    case "array":
                        for($i = 0; $i < count($files["type"]); $i++)
                        {
                            if(!$this->isAllowedMIME($files["tmp_name"][$i], $files["type"][$i]))
                            {
                                $allowedMIME = FALSE;
                                break;
                            }
                        }
                        break;
                }

                $allowedSize    = TRUE;
                switch(gettype($files["size"]))
                {
                    case "string":
                        if(!$this->isAllowedFileSize($files["size"]))
                        {
                            $allowedSize = FALSE;
                        }
                        break;
                    case "array":
                        foreach($files["size"] as $size)
                        {
                            if(!$this->isAllowedFileSize($size))
                            {
                                $allowedSize = FALSE;
                                break;
                            }
                        }
                        break;
                }

                $fileNames = [];

                if($allowedMIME && $allowedSize) 
                {
                    switch(gettype($files["tmp_name"]))
                    {
                        case "string":
                            if(move_uploaded_file($files["tmp_name"], $this->targetUploadDir . "/" . $targetFilename))
                            {
                                array_push($fileNames, $this->targetUploadDir . "/" . $targetFilename);
                            }
                            break;
                        case "array":
                            $success = TRUE;
                            for($i = 0; $i < count($files["tmp_name"]); $i++)
                            {
                                if(move_uploaded_file($files["tmp_name"][$i], $this->targetUploadDir . "/" . $targetFilename[$i]))
                                {
                                    array_push($fileNames, $this->targetUploadDir . "/" . $targetFilename[$i]);
                                }
                                else
                                {
                                    $success = FALSE;
                                    break;
                                }
                            }

                            if(!$success)
                            {
                                foreach($fileNames as $fileName)
                                {
                                    unlink($fileName);
                                }
                                
                            }
                            break;
                    }
                }
            }
            
            return $fileNames;
        }

         /**
        * This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
        * https://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size
        * @param string $sSize
        * @return integer The value in bytes
        */
        private function convertPHPSizeToBytes($sSize)
        {
            //
            $sSuffix = strtoupper(substr($sSize, -1));
            if (!in_array($sSuffix,array('P','T','G','M','K'))){
                return (int)$sSize;  
            } 
            $iValue = substr($sSize, 0, -1);
            switch ($sSuffix) {
                case 'P':
                    $iValue *= 1024;
                    // Fallthrough intended
                case 'T':
                    $iValue *= 1024;
                    // Fallthrough intended
                case 'G':
                    $iValue *= 1024;
                    // Fallthrough intended
                case 'M':
                    $iValue *= 1024;
                    // Fallthrough intended
                case 'K':
                    $iValue *= 1024;
                    break;
            }
            return (int)$iValue;
        }  
    }