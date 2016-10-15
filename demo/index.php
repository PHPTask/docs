<?php

include __DIR__ . '/vendor/autoload.php';

class ImageResizeHandler implements Task\Handler\TaskHandlerInterface
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

// storage
$taskRepository = new Task\Storage\ArrayStorage\ArrayTaskRepository();
$taskExecutionRepository = new Task\Storage\ArrayStorage\ArrayTaskExecutionRepository();

// utility
$eventDispatcher = new Symfony\Component\EventDispatcher\EventDispatcher();
$taskHandlerFactory = new Task\Handler\TaskHandlerFactory();
$factory = new Task\Builder\TaskBuilderFactory();

// core components
$scheduler = new Task\Scheduler\TaskScheduler($factory, $taskRepository, $taskExecutionRepository, $eventDispatcher);
$runner = new Task\Runner\TaskRunner($taskExecutionRepository, $taskHandlerFactory, $eventDispatcher);

// schedule task one
$scheduler->createTask(
    ImageResizeHandler::class,
    [__DIR__ . '/images/example-1.jpg', __DIR__ . '/images/thumbnails/example-1.jpg', 100]
)->schedule();

// scheduel task twos
$scheduler->createTask(
    ImageResizeHandler::class,
    [__DIR__ . '/images/example-2.jpg', __DIR__ . '/images/thumbnails/example-2.jpg', 100]
)->schedule();

// run tasks
$runner->runTasks();
