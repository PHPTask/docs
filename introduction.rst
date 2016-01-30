Introduction
============
The php-task library provides a simple and easy to extend interface
to handle synchronous and asynchronous tasks in PHP.

What it does
------------
It allows to implement handler classes in PHP for tasks. These handler
can be implemented in your favorite environment. Over a simple
interface the developer can define and schedule long running tasks
without any overhead.

One typical usecase is generating thumbnails or rendering videos.
These tasks are to long to run them immediately and can be done after
generating the response to the user.

Quick Example
-------------
This example will assume you want to generate thumbnail images.

.. code-block:: php

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
    $registry->add('iapp.mage_resize', new ImageResizeHandler());

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

The example will generate two thumbnail images one for the jpg ``example-1.jpg``
and one for ``example-2.jpg`` both in the folder thumbnails.

.. note::

    You find the `complete source-code`_ for this example here

Integration
-----------
The library provides a integration into Symfony_ framework (see :doc:`symfony`).

.. _Gearman: http://gearman.org
.. _PHP Implementation: https://github.com/php-task/php
.. _Symfony: http://symfony.com/
.. _complete source-code: https://github.com/php-task/docs/tree/master/demo
