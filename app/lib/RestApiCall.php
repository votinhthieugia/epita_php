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
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
    $curl = curl_exec($curl);
    if ($curl === false) {
      $info = curl_getinfo($curl);
      curl_close($curl);
      return false;
    }
    curl_close($curl);
    return true;
  }

  public static function do_put($url, $data) {
    $curl = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
    $response = curl_exec($curl);
    if ($response === false) {
      $info = curl_getinfo($curl);
      curl_close($curl);
      return false;
    }
    curl_close($curl);
    return true;
  }

  public static function do_delete($url, $data) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($curl);
    if ($curl_response === false) {
      $info = curl_getinfo($curl);
      curl_close($curl);
      return false;
    }
    curl_close($curl);
    return true;
  }
}

?>
