# Usage:

### Step 1: Populate these with your info
```php
$cm_plugin = new cm_aws_plugin([
    'AWS_ACCESS_KEY_ID' => '',
    'AWS_SECRET_ACCESS_KEY' => '',
    'AWS_DEFAULT_REGION' => '',
    'AWS_BUCKET' => '',
]);
```

### Step 2: In your templates
```php
$file_name = 'videos/Unbuilt-Animation-540p.mp4';
$link_expiration = '+12 minutes';

do_action( 'setKeyAndExpiration', $file_name, $link_expiration);
do_action( 'thePresignedUrl');
```
