<?php
/**
 * Created by PhpStorm.
 * User: Piggat
 * Date: 8/4/14
 * Time: 9:51 AM
 */

namespace SeuDo\Notification\Transporters;

use Seudo\Logger;


class GoogleCloudMessage implements ITransporter {

    public $api_key;
    public $gcm_endpoint;
    private $_providerName = 'Google';

    public function getProviderName()
    {
        return $this->_providerName;
    }

    public function sendNotification($from, $to, $body)
    {
        $apiKey = $this->api_key;
        $url = $this->gcm_endpoint;
        $ids = array();
        $message = array(
            'message' => $body,
            'title' => 'seudo notification'
        );

        if ($to instanceof \Users)
        {
            $devices = \ClientApi::findByUserId($to->getId());
            foreach ($devices as $device)
            {
                if ($device instanceof \ClientApi)
                {
                    $id = $device->getGcmRegId();
                    if (!empty($id))
                    {
                        $ids[] = $id;
                    }
                }
            }
        }
        else
        {
            return false;
        }
        //------------------------------
        // Set GCM post variables
        // (Device IDs and push payload)
        //------------------------------

        $post = array(
            'registration_ids'  => $ids,
            'data'              => $message,
        );

        //------------------------------
        // Set CURL request headers
        // (Authentication and type)
        //------------------------------

        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );

        //------------------------------
        // Initialize curl handle
        //------------------------------

        $ch = curl_init();

        //------------------------------
        // Set URL to GCM endpoint
        //------------------------------

        curl_setopt( $ch, CURLOPT_URL, $url );

        //------------------------------
        // Set request method to POST
        //------------------------------

        curl_setopt( $ch, CURLOPT_POST, true );

        //------------------------------
        // Set our custom headers
        //------------------------------

        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

        //------------------------------
        // Get the response back as
        // string instead of printing it
        //------------------------------

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        //------------------------------
        // Set post data as JSON
        //------------------------------

        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $post ) );

        //------------------------------
        // Actually send the push!
        //------------------------------

        $result = curl_exec( $ch );

        //------------------------------
        // Error? Display it!
        //------------------------------

        if ( curl_errno( $ch ) )
        {
            Logger::factory('gcm_notification')->warn('Could not send notification to Google Cloud Message!', curl_error( $ch ) , $to, $body);
            //echo 'GCM error: ' . curl_error( $ch );
        }

        //------------------------------
        // Close curl handle
        //------------------------------

        curl_close( $ch );

        //------------------------------
        // Debug GCM response
        //------------------------------

        Logger::factory('gcm')->addNotice('gcm send with result: '. $result);
        //echo $result;
        return true;
    }
}