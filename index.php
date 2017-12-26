<?php declare(strict_types=1);
/**
 * Created by paulbolhaskih@yahoo.com.
 * User: github.com/gfijrb
 * Date: 25.12.2017
 * Time: 00:00
 */
namespace eherscanparser;


/**
 * Class EtherScan
 * @package eherscanparser
 */
class EtherScan {

    private $max_pages = 400;

    function __construct()
    {
        // style output
        echo "<pre>";
        for($x = 1; $x <= $this->max_pages; $x++) {
            $data = $this->getEtherscan($x);
            $this->TextStrip( $data );
            time_nanosleep(0, (int) 1e8);
        }
    }

    /**
     * Function getEtherscan: curl php http post connecting to resource etherscan.io
     *
     * @param int $num
     * @return string
     */
    private function getEtherscan(int $num ):string
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://etherscan.io/accounts/a/' . (string) $num,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            )
        ]);
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp;
    }

    /**
     * Function TextStrip: parsing web pages data
     *
     * @param string $text
     * @return array
     */
    private function TextStrip(string $text ):array
    {
        $arr = [];
        preg_match_all("/accounts\/a\/400/i", $text, $sentance_match, PREG_OFFSET_CAPTURE); //for all utf-8 format texts
        $new_text = substr($text, 0, $sentance_match[0][1][1]);
        preg_match_all("/0x[a-zA-Z0-9]*/i", $new_text, $sentance_match); //for all utf-8 format texts
        $cou = $sentance_match[0];
        $i = 1;
        foreach ($cou as $key => $value) {
            $i++;
            if(strlen($value) == 42 && $i%2 && $value != '0x71c7656ec7ab88b098defb751b7401b5f6d8976f') {
                $arr[] = $value;
            };
        }
        // console log
        var_dump($arr);
        // write in file
        $this->writeFile($arr);
        return $arr;
    }

    /**
     * Function WriteFile: create and write data in resourse.txt file
     *
     * @param array $array
     */
    private function writeFile(array $array ):void
    {
        $file = fopen(__DIR__ . "\\resource.txt", "a+");
        $arr = implode("\", \"", $array);
        //var_dump($arr);
        $data = '"' . $arr . '"';
        fwrite($file, $data);
        fclose($file);
    }
}

$app = new EtherScan();

?>
