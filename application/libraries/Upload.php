<?
class Upload
{
    function img_to_png($srcFile, $maxSize = 1000, $smoothness=10)
    {  
        list($width_orig, $height_orig, $type) = getimagesize($srcFile);    

        // Temporarily increase the memory limit to allow for larger images
        ini_set('memory_limit', '64M'); 

        switch ($type) 
        {
            case IMAGETYPE_GIF: 
                $image = imagecreatefromgif($srcFile); 
                break;   
            case IMAGETYPE_JPEG:  
                $image = imagecreatefromjpeg($srcFile); 
                break;   
            case IMAGETYPE_PNG:  
                $image = imagecreatefrompng($srcFile);
                break; 
            default:
                throw new Exception('Unrecognized image type ' . $type);
        }    
        // Get the aspect ratio
        $ratio_orig = $width_orig / $height_orig;

        $width  = $maxSize > $width_orig ? $width_orig : $maxSize; 
        $height = $maxSize > $height_orig ? $height_orig : $maxSize;

        // resize to height (orig is portrait) 
        if ($ratio_orig < 1) {
            $width = $height * $ratio_orig;
        } 
        // resize to width (orig is landscape)
        else {
            $height = $width / $ratio_orig;
        }

        // create a new blank image
        $newImage = imagecreatetruecolor($width, $height);
//        imageantialias($newImage, true);
        imagealphablending( $newImage, false );
        imagesavealpha( $newImage, true );

        // Copy the old image to the new image
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
        //imagefilter($newImage, IMG_FILTER_SMOOTH, floor(min(max(($width+$height)/300,2),6)));
//var_dump(($width+$height)/200);die;
        imagefilter($newImage, IMG_FILTER_SMOOTH, $smoothness);

        // Output to a temp file
        $destFile = $srcFile;
        imagepng($newImage, $destFile, 9);  

        // Free memory                           
        imagedestroy($newImage);
    }
}
?>