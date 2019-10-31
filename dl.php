<?php

/*
 * Simple PHP file Downloader by @pedropamn.
 *
 * (c) Pedro Antônio <pedropamn@gmail.com>
 *     <https://github.com/pedropamn>
 *
 * Licensed under MIT license.
 */
 
error_reporting(0);
	//=================================================================================//

class Colors {
		private $foreground_colors = array();
		private $background_colors = array();

		public function __construct() {
			// Set up shell colors
			$this->foreground_colors['black'] = '0;30';
			$this->foreground_colors['dark_gray'] = '1;30';
			$this->foreground_colors['blue'] = '0;34';
			$this->foreground_colors['light_blue'] = '1;34';
			$this->foreground_colors['green'] = '0;32';
			$this->foreground_colors['light_green'] = '1;32';
			$this->foreground_colors['cyan'] = '0;36';
			$this->foreground_colors['light_cyan'] = '1;36';
			$this->foreground_colors['red'] = '0;31';
			$this->foreground_colors['light_red'] = '1;31';
			$this->foreground_colors['purple'] = '0;35';
			$this->foreground_colors['light_purple'] = '1;35';
			$this->foreground_colors['brown'] = '0;33';
			$this->foreground_colors['yellow'] = '1;33';
			$this->foreground_colors['light_gray'] = '0;37';
			$this->foreground_colors['white'] = '1;37';

			$this->background_colors['black'] = '40';
			$this->background_colors['red'] = '41';
			$this->background_colors['green'] = '42';
			$this->background_colors['yellow'] = '43';
			$this->background_colors['blue'] = '44';
			$this->background_colors['magenta'] = '45';
			$this->background_colors['cyan'] = '46';
			$this->background_colors['light_gray'] = '47';
		}

		// Returns colored string
		public function getColoredString($string, $foreground_color = null, $background_color = null) {
			$colored_string = "";

			// Check if given foreground color found
			if (isset($this->foreground_colors[$foreground_color])) {
				$colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
			}
			// Check if given background color found
			if (isset($this->background_colors[$background_color])) {
				$colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
			}

			// Add string and end coloring
			$colored_string .=  $string . "\033[0m";

			return $colored_string;
		}

		// Returns all foreground color names
		public function getForegroundColors() {
			return array_keys($this->foreground_colors);
		}

		// Returns all background color names
		public function getBackgroundColors() {
			return array_keys($this->background_colors);
		}
	}
$colors = new Colors();

/**
     * Render Blank spaces.
     *
	 * @param integer
	 *
     * @return string
*/

function render_blank($missing){
	$blank = "";
	for($i = 0;$i < $missing;$i++){
		$blank .= " "; 
	}
	return $blank;
}



/**
     * Render progress bar.
	 *
	 * @param float
	 *
     * @return string
*/

function render_bar($percent){
	$bar = "";
	for($i=0;$i<$percent;$i++){
		$bar .= "=";
	}
	
	return $bar;
}




/**
     * Generate the progress bar.
     *
	 * @param float
	 *
     * @return string
*/

function gen_bar($percent){
	
	if($percent < 0 || $percent > 100){
		die("Porcentagem inválida");
	}
	//Inicialização. $percent é a porcentagem a gerar, por exemplo, 10%
	$bar = "";
	$blank = "";
	$close = "]";
		
	//Quantos espaços em branco devem ter?
	$missing = 100 - $percent; 
	
	//Chama a função que renderiza esses espaços em branco
	$blank = render_blank($missing);
	
	//Gera 1 elemento da barra a cada porcentagem
	$bar = render_bar($percent);
	
	//Exibe a barra + espaços em branco + caracter de fechamento
	echo "[{$bar}{$blank}{$close} {$percent} %\r";
	
	//Obs: Se apenas renderizar a barra, o caracter de fechamento vai sempre se afastar, por isso é necessário calcular os espaços em branco para a barra ter largura fixa

}



/**
     * Get the progress.
     *
	 * @param float
	 *
     * @return float
*/

function progress($resource,$download_total_size, $downloaded_so_far, $upload_size, $uploaded){
		if($download_total_size > 0){			
			echo round($downloaded_so_far / 1024,2)." Kb";
			$percentage = round(($downloaded_so_far / $download_total_size)  * 100);
			gen_bar($percentage);			
	}

}



/**
     * Download the file.
     *
	 * @param string $remote_file
	 *
	 * @param string $path
	 *
     * @return string
*/

function download($remote_file, $path, $colors){
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $remote_file);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progress');
	curl_setopt($ch, CURLOPT_NOPROGRESS, false); // needed to make progress function work
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$file = curl_exec($ch);	
	if(!$file){
		echo $colors->getColoredString("Error. Stopping... Check if the URL is valid or if you are trying download a file","light_red", "white");
		die();
	}
	 $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

	curl_close($ch);
	
	
	
	
	//Save
	$fp = fopen($path, 'w');
    fwrite($fp, $file);
    fclose($fp);
	echo "\n".$colors->getColoredString("Done","light_green", "white");;
}

/**
     * Converts a Youtube URL in a downloadable file URL.
     *
	 * @param string $link
	 *
     * @return string
*/
function get_direct_youtube_url($link){
	
	//Create a temporary folder to store files
	if(mkdir("tmp_php_files")){
		//Download the PHP files inside this folder to do the task, via https://github.com/Athlon1600/youtube-downloader
		$content = file_get_contents("https://raw.githubusercontent.com/Athlon1600/youtube-downloader/master/src/YouTubeDownloader.php");
		$fp = fopen("./tmp_php_files/YouTubeDownloader.php",'w');
		fwrite($fp,$content);
		fclose($fp);
		
		//Get the link
		require('tmp_php_files/YouTubeDownloader.php');
		$yt = new YouTubeDownloader();
		$links = $yt->getDownloadLinks($link);
		foreach ($links as $link){
			$yt_link = $link['url'];
		}
		
		//Delete the folder with the files
		deleteDir("tmp_php_files");
	
		return $yt_link;

	}
	else{
		die("Failed to create temporary folder in current location. Maybe the folder already exists or there is a permission issue");
	}
	
	
}

/**
     * Delete files and folders.
	 *
     * @return void
*/
function deleteDir($path) {
    if (empty($path)) { 
        return false;
    }
    return is_file($path) ?
            @unlink($path) :
            array_map(__FUNCTION__, glob($path.'/*')) == @rmdir($path);
}



	//SCRIPT EXECUTION START HERE
	


	$help_text = "	\n\n	".$colors->getColoredString("PHP file Downloader 1.0, by ","yellow", "white").$colors->getColoredString("@pedropamn","white", "yellow").$colors->getColoredString(" - https://github.com/pedropamn ","white", "white")."\n		
	".$colors->getColoredString("Usage: php ".basename(__FILE__, '.php').".php --url [url] --path=[path] --filename=[filename]\n","yellow", "white")."	
	".$colors->getColoredString("Options:
	[--url]			The link to file to download (Mandatory)
	[--path]		Custom path to save (Optional)
	[--filename]		Custom filename to save (with extension) (Optional)\n","light_cyan", "white")."	
	
	".$colors->getColoredString("-> Also supports Instagram and Youtube links!","yellow", "white")."
";

	//Check Options
	$longopts  = array(
		"url:",     // Required value
		"path::",     // Optional value
		"filename::"     // Optional value
	);
	$options = getopt("",$longopts);
	
	//Get specific options
	$path_opt = $options['path'];	
	$filename_opt = $options['filename'];
	$url = $options['url'];
		
	//Check informations about the file and store in $info (using to get filename)
	$info = pathinfo($url);	
	$current_dir = dirname(__FILE__);
	
	//Get the keys passed, for example, script.php --url google.com, returns an array with "url" key (just the key, not value)
	$keys = array_keys($options);


	//Check URL param
	if(!in_array("url",$keys) || $url == ""){
		//echo $colors->getColoredString("::::::::::::: Error. Check the parameters ::::::::::::: ","light_red", "white");
		die($help_text);
	}
	echo "Starting download...\n";

	// Check filename param
	if(in_array("filename",$keys)){
		$filename = $filename_opt;
	}
	else{
		$filename = $info['basename']; //file.extension
	}
	
	//Check if is an Instagram URL
	if(substr($url,0,25) == "https://www.instagram.com"){
			
		//Check if content end with a bar (/)
		if(substr($url, -1) != "/"){
			$url = $url."/?__a=1";
		}
		else{
			$url = $url."?__a=1";
		}

		//Get the content
		$json = file_get_contents($url);
		
		//Decode
		$decode = json_decode($json,true);
		
		//Check if content is an image or video
		if($decode['graphql']['shortcode_media']['is_video'] == true){
			$ext = ".mp4";
			$url = $decode['graphql']['shortcode_media']['video_url'];
		}
		else{
			$ext = ".jpg";
			$url = $decode['graphql']['shortcode_media']['display_url'];
		}

	}
	
 	//Youtube
	else if(substr($url,0,23) == "https://www.youtube.com"){
		$url = get_direct_youtube_url($url);
		$ext = ".mp4";
	}
	
	//Using the extension of the file
	else{
		$ext = "";
	} 
	
	
	
	
	//Check path param	
	if(in_array("path",$keys)){
		$path = $path_opt.DIRECTORY_SEPARATOR.$filename.$ext;
	}
	else{
		$path = $current_dir.DIRECTORY_SEPARATOR.$filename.$ext;
	}
	
	
	//Run
	download($url, $path, $colors);



?>