<?php

class RestApiCall {
  public static function do_get($url) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    if ($response === false) {
      $info = curl_getinfo($curl);
      curl_close($curl);
      return false;
    }
    curl_close($curl);
    $decoded = json_decode($response, true);
    return $decoded;
  }

  public static function do_post($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($response === false) {
      $info = curl_getinfo($ch);
      curl_close($ch);
      return false;
    }
    curl_close($ch);
    return $response;
  }

  public static function do_put($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
    $response = curl_exec($ch);
    if ($response === false) {
      $info = curl_getinfo($ch);
      curl_close($ch);
      return false;
    }
    curl_close($ch);
    return true;
  }

  public static function do_delete($url, $data) {
    print $data;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($ch);
    print $response;
    if ($response === false) {
      $info = curl_getinfo($ch);
      curl_close($ch);
      return false;
    }
    curl_close($ch);
    return true;
  }
}

?>
