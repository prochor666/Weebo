<?php
/*
* File: SimpleImage.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 08/11/06
* Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details:
* http://www.gnu.org/licenses/gpl.html
*
*/
 
class SimpleImage {
 
	public $image, $image_type, $fileSource, $fileUrl, $fileInfo;

	public function __construct()
	{
		$this->imageInfo = null;
	}

	public function load($filename)
	{
		$this->imageInfo = @getimagesize($filename);
		$this->image_type = $this->imageInfo[2];
		
		if( $this->image_type == IMAGETYPE_JPEG ) {
			$this->image = imagecreatefromjpeg($filename);
		} elseif( $this->image_type == IMAGETYPE_GIF ) {
			$this->image = imagecreatefromgif($filename);
		} elseif( $this->image_type == IMAGETYPE_PNG ) {
			$this->image = imagecreatefrompng($filename);
		}
	}

	public function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) 
	{
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image,$filename,$compression);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->image,$filename);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->image,$filename);
		}
		
		if( $permissions != null ) {
			chmod($filename,$permissions);
		}
	}

	public function output($image_type=IMAGETYPE_JPEG)
	{
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->image);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->image);
		}
	}

	public function getWidth()
	{
		return imagesx($this->image);
	}

	public function getHeight(){
		return imagesy($this->image);
	}

	public function resizeToHeight($height)
	{
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width,$height);
	}
 
	public function resizeToWidth($width) 
	{
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize($width,$height);
	}
 
	public function scale($scale) 
	{
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100;
		$this->resize($width,$height);
	}
 
	public function resize($width,$height) 
	{
		
		$image_resized = imagecreatetruecolor($width, $height);
		
		if ( ($this->image_type == IMAGETYPE_GIF) || ($this->image_type == IMAGETYPE_PNG) )
		{
			$trnprt_indx = imagecolortransparent($this->image);
			
			$ti = (int)imagecolorstotal($this->image);
			
			if($ti == 0)
			{
				$trnprt_indx = imagetruecolortopalette($image_resized, false, $ti );
			}
			
			// If we have a specific transparent color
			if ($trnprt_indx === true)
			{
				// Get the original image's transparent color's RGB values
				$trnprt_color	= imagecolorsforindex($this->image, $trnprt_indx);

				// Allocate the same color in the new image resource
				$trnprt_indx	= imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);

				// Completely fill the background of the new image with allocated color.
				imagefill($image_resized, 0, 0, $trnprt_indx);

				// Set the background color for new image to transparent
				imagecolortransparent($image_resized, $trnprt_indx);


			}elseif ($this->image_type == IMAGETYPE_PNG)
			{
				// Always make a transparent background color for PNGs that don't have one allocated already\

				// Turn off transparency blending (temporarily)
				imagealphablending($image_resized, false);

				// Create a new transparent color for image
				$color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);

				// Completely fill the background of the new image with allocated color.
				imagefill($image_resized, 0, 0, $color);

				// Restore transparency blending
				imagesavealpha($image_resized, true);
			}
		}
		
		imagecopyresampled($image_resized, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->image = $image_resized;
	}	  
 
}
?>
