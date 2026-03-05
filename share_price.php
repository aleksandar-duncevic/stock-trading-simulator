<?php
    function getSharePrice($symbol) {
        $url = "https://query1.finance.yahoo.com/v8/finance/chart/$symbol?region=US&lang=en-US&includePrePost=false&interval=1h&useYfid=true&range=1d";

        // Inicijalizacija cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        // Podesavanje hedera da simuliraju web pregledac
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36',
            'Accept: application/json, text/plain, */*',
            'Connection: keep-alive',
        ]);

        // Izvrsavanje zahteva
        $response = curl_exec($ch);

        // Provera cURL gresaka
        if (curl_errno($ch)) {
            error_log('cURL Error: ' . curl_error($ch));
            curl_close($ch);
            return NULL;
        }

        // Provera HTTP statusa
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 200) {
            error_log("HTTP Error Code: $http_code for symbol $symbol");
            return NULL;
        }

        $stock_data = json_decode($response, true);

        // Vracamo cenu deonice
        return $stock_data['chart']['result'][0]['meta']['regularMarketPrice'] ?? NULL;
    }

    function getCompanyName($symbol) {
        $url = "https://query1.finance.yahoo.com/v1/finance/lookup?formatted=true&lang=en-US&region=US&query=$symbol&type=equity&count=3000&start=0";

        // Inicijalizacija cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Podesavanje hedera da simuliraju web pregledac
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: Mozilla/5.0',
        ]);

        $response = curl_exec($ch);

        // Provera cURL gresaka
        if (curl_errno($ch)) {
            error_log('cURL Error: ' . curl_error($ch));
            curl_close($ch);
            return NULL; // Close early if error
        }

        // Provera HTTP statusa
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 200) {
            error_log("HTTP Error Code: $http_code for symbol $symbol");
            return NULL;
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decoding error for symbol $symbol: " . json_last_error_msg());
            return NULL;
        }

        // Vracamo ime kompanije
        return $data['finance']['result'][0]['documents'][0]['shortName'] ?? NULL;
    }

    /*
    function getSharePrice($symbol) {
        $url = "https://query1.finance.yahoo.com/v8/finance/chart/$symbol?region=US&lang=en-US&includePrePost=false&interval=1h&useYfid=true&range=1d";
        $stock_data = json_decode(@file_get_contents($url), true);
        return $stock_data['chart']['result'][0]['meta']['regularMarketPrice'] ?? NULL;
    }
    

    function getCompanyName($symbol) {
        $url = "https://query1.finance.yahoo.com/v1/finance/lookup?formatted=true&lang=en-US&region=US&query=$symbol&type=equity&count=3000&start=0";
        $data = json_decode(@file_get_contents($url), true);
        return $data['finance']['result'][0]['documents'][0]['shortName'] ?? NULL;
    }
    */
?>
