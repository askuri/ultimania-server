<?php

// Settings
define('API_URL', 'http://localhost:8000/api');

$action = $_POST['action'];
$uid = $_POST['uid'];

function doRequest($method, $endpoint, $payload = []) {
    $handle = curl_init();

    $url = API_URL . '/v5/' . $endpoint;
    $httpHeaders = ['Accept: application/json'];

    curl_setopt($handle, CURLOPT_USERAGENT, 'UltimaniaApiLegacyAdapter');
    curl_setopt($handle, CURLOPT_HEADER, false);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $method);
    if ($method == 'GET') {
        $url .= '?'.http_build_query($payload);
    } else {
        $httpHeaders[] = 'Content-Type: application/json';
        curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($payload));
    }
    curl_setopt($handle, CURLOPT_HTTPHEADER, $httpHeaders);
    curl_setopt($handle, CURLOPT_TIMEOUT, 3);
    curl_setopt($handle, CURLOPT_LOW_SPEED_TIME, 3);
    curl_setopt($handle, CURLOPT_URL, $url);

    $response = curl_exec($handle);

    curl_close($handle);

    return empty($response) ? null : json_decode($response, true);
}

switch ($action) {
    case 'playerfinish':
        $login = $_POST['login'];
        $score = $_POST['score'];

        doRequest(
            'PUT',
            'records',
            [
                'player_login' => $login,
                'map_uid' => $uid,
                'score' => $score,
            ]
        );
        break;

    case 'gettop':
        if ($_POST['limit'] == '0') {
            $limit = 1000;
        } else {
            $limit = 25;
        }

        $recordsFromNewApi = doRequest(
            'GET',
            'maps/' . $uid . '/records',
            ['limit' => $limit]
        );

        $recordsLegacy = array_map(fn($recordFromNewApi) => [
            'login' => $recordFromNewApi['player']['login'],
            'nick' => $recordFromNewApi['player']['nick'],
            'uid' => $recordFromNewApi['map_uid'],
            'score' => $recordFromNewApi['score'],
            'add_time' => $recordFromNewApi['updated_at'],
        ], $recordsFromNewApi);

        echo json_encode($recordsLegacy);
        break;

    case 'getbannedplayers':
        echo '[]'; // not implemented, the new server checks for banned players anyway
        break;
}
