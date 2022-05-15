# <img valign="middle" src="https://www.fepe.org.br/portal/wp-content/uploads/2021/03/cloud-icone-downloads.png" width="30" height="30" /> PHP-Downloader (Alpha)
Download any file from web via PHP, with Instagram support (via command line Interface)

## Usage:

```
php dl.php --url [url] --path=[path] --filename=[filename]
```
```--url```: The direct URL to file or Instagram media

```--path```: The path where file will be saved (defautl: current folder)

```--filename```: The file name (default: original file name)

## Dependencies:
```
sudo apt install php7.4-cli
sudo apt install php7.4-curl 
```



## Known issues:
- Colored strings not work in Windows 8.1
- Downloads only the first media from carousels
