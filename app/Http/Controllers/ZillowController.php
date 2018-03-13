<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

class ZillowController extends Controller
{
    public function getComps(Request $request)
    {
        if (!empty($request->addressListing)) {

            $addressListing = $request->addressListing;
            $addressListing = str_replace(' ', '-',$addressListing);
        }

        if (!empty($request->zipListing)) {
            $zipListing = $request->zipListing;
        }

        $zillowID = self::getSearchResult($zipListing, $addressListing);
        if (!empty($request->numberComps)) {
            $compCount = $request->numberComps;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.zillow.com/webservice/GetDeepComps.htm?zws-id=X1-ZWz18q1nwk0cuj_5w3uy&zpid=$zillowID&count=$compCount");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);


        $xml = simplexml_load_string($response);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);




        $data = [
            "compData" => $array
        ];


        return view('compfinder')->with($data);
    }

    protected function getSearchResult($zipListing,$addressListing)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.zillow.com/webservice/GetSearchResults.htm?zws-id=X1-ZWz18q1nwk0cuj_5w3uy&address=$addressListing&citystatezip=$zipListing");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);


        $xml = simplexml_load_string($response);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);


        return $array['response']['results']['result']['zpid'];
    }



}
