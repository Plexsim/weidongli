<?php
class Downloader
{
  public function downloadFile($url, $path, $destinationFileName, $params = array(), $escapeShellCmd = true)
  {
    if (empty($path) || empty($url) || empty($destinationFileName)) {
      throw new Exception('Wrong arguments');
    }

    if ($this->pathShouldBeCreated($path)) {
      if ($this->pathCanBeCreated($path)) {
        mkdir($path, 0777, true);
      } else {
        throw new Exception('Path can not be created');
      }
    }

    if (is_writable(dirname($path))) {
      $outputFile = $path . '/' . $destinationFileName;

      echo $outputFile;
      echo "<br>";
      if(!file_exists($outputFile)) {
        $ret = file_put_contents($outputFile, fopen($url, 'r'));
      }

      if (0 == filesize($outputFile)) {
        throw new Exception('Void file downloaded');
      }

    } else {
      throw new Exception('Path [' . $path . '] is not writable');
    }
  }

  public function pathShouldBeCreated($path)
  {
    return !is_dir($path);
  }

  public function pathCanBeCreated($path)
  {
    if (is_dir(dirname($path)) && is_writable(dirname($path))) {
      return true;
    } else {
      if (!is_dir(dirname($path)) && $path != '/') {
        return $this->pathCanBeCreated(dirname($path));
      }
    }

    return false;
  }
}

