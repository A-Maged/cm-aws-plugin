<?php
/*
Plugin Name: cm aws
Description: help automate aws tasks
Author: cmaged
Version: 1.0.0
*/

// prevent direct path access
if (!defined('WPINC')){die;}

require_once('aws/aws-autoloader.php');

class cm_aws_plugin
{
    private $key;
    private $expiration;
    private $AWS_ACCESS_KEY_ID;
    private $AWS_SECRET_ACCESS_KEY;
    private $AWS_DEFAULT_REGION;
    private $AWS_BUCKET;
    private $s3Client;

    function __construct($bucketInfo)
    {
        $this->AWS_ACCESS_KEY_ID     = $bucketInfo['AWS_ACCESS_KEY_ID'];
        $this->AWS_SECRET_ACCESS_KEY = $bucketInfo['AWS_SECRET_ACCESS_KEY'];
        $this->AWS_DEFAULT_REGION    = $bucketInfo['AWS_DEFAULT_REGION'];
        $this->AWS_BUCKET            = $bucketInfo['AWS_BUCKET'];

        $this->s3Client = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region'  => $this->AWS_DEFAULT_REGION,
            'credentials' => [
                'key'    => $this->AWS_ACCESS_KEY_ID,
                'secret' => $this->AWS_SECRET_ACCESS_KEY,
            ]
        ]);

        add_action('admin_menu', [$this, 'setupMenu']);
        add_action( 'setKeyAndExpiration', [$this, 'setKeyAndExpiration'], 10, 2 );
        add_action( 'thePresignedUrl', [$this, 'thePresignedUrl'] );
    }


    public function setupMenu(){
        add_menu_page( 'cm aws Plugin Page', 'cm aws', 'manage_options', 'cm-aws-plugin', [$this, 'pageContent']);
    }
    function pageContent(){
        echo "<h1>Hello World!</h1>";
    }
    
    // outputs the Presigned url of the file
    public function thePresignedUrl()
    {
        echo $this->getPresignedUrl();
    }


    // returns the Presigned url of the file
    public function getPresignedUrl()
    {
        $cmd = $this->s3Client->getCommand('GetObject', [
            'Bucket' => $this->AWS_BUCKET,
            'Key'    => $this->key,
        ]);
        $req = $this->s3Client->createPresignedRequest($cmd, $this->expiration);
        $presignedUrl = (string) $req->getUri();
        return $presignedUrl;
    }


    // sets the filename/key and expiration
    public function setKeyAndExpiration($key, $expiration){
        $this->key = $key;
        $this->expiration = $expiration;
    }

}



/***************************************************************************/
/* USAGE: */
/************/

/* populate these with your info */
$cm_plugin = new cm_aws_plugin([
    'AWS_ACCESS_KEY_ID' => '',
    'AWS_SECRET_ACCESS_KEY' => '',
    'AWS_DEFAULT_REGION' => '',
    'AWS_BUCKET' => '',
]);
$file_name = 'videos/Unbuilt-Animation-540p.mp4';
$link_expiration = '+12 minutes';

// in your templates
do_action( 'setKeyAndExpiration', $file_name, $link_expiration);
do_action( 'thePresignedUrl');
