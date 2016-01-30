<?php

include __DIR__ . '/vendor/autoload.php';

class ImageResizeHandler implements Task\Handler\HandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($workload)
    {
        list($sourceImagePath, $destinationImagePath, $desiredWidth) = $workload;

        /* read the source image */
        $sourceImage = imagecreatefromjpeg($sourceImagePath);
        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);

        /* find the "desired height" of this thumbnail, relative to the desired width  */
        $desiredHeight = floor($height * ($desiredWidth / $width));

        /* create a new, "virtual" image */
        $virtualImage = imagecreatetruecolor($desiredWidth, $desiredHeight);

        /* copy source image at a resized size */
        imagecopyresampled($virtualImage, $sourceImage, 0, 0, 0, 0, $desiredWidth, $desiredHeight, $width, $height);

        /* create the physical thumbnail image to its destination */
        imagejpeg($virtualImage, $destinationImagePath);

        return $destinationImagePath;
    }
}

// bootstrap
$storage = new Task\Storage\ArrayStorage();
$registry = new Task\Handler\Registry();
$taskBuilderFactory = new Task\TaskBuilderFactory();
$eventDispatcher = new Symfony\Component\EventDispatcher\EventDispatcher();
$scheduler = new Task\Scheduler($storage, $registry, $taskBuilderFactory, $eventDispatcher);

// register handler
$registry->add('app.image_resize', new ImageResizeHandler());

// schedule task one
$scheduler->createTask(
    'app.image_resize',
    [__DIR__ . '/images/example-1.jpg', __DIR__ . '/images/thumbnails/example-1.jpg', 100]
)->schedule();

// scheduel task twos
$scheduler->createTask(
    'app.image_resize',
    [__DIR__ . '/images/example-2.jpg', __DIR__ . '/images/thumbnails/example-2.jpg', 100]
)->schedule();

// run tasks
$scheduler->run();
