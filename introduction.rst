Introduction
============
The php-task library provides a simple and easy to extend interface
to handle synchronous and asynchronous tasks in PHP.

What it does
------------
It allows to implement worker classes in PHP for tasks. These worker
can be implemented in your favorite environment. Over a simple
interface the developer can define and schedule long running tasks
without any overhead.

One typical usecase is generating thumbnails or rendering videos.
These tasks are to long to run them immediately. These task can be
done after generating the response to the user.

How it works
------------
The php-task library provides two ways to schedule tasks:

* Gearman_: A generic application framework for farming out work
  to multiple machines or processes.
* `PHP Implementation`_: A implementation of the php-task library
  in raw PHP.

Quick Example
-------------
This example will assume you want to generate thumbnail images.

.. note::

    The example uses the raw PHP implementation for php-task.

.. code-block:: php

    <?php

    use Task\Scheduler\TaskInterface;

    class ImageResizeWorker implements Task\TaskRunner\WorkerInterface
    {
        /**
         * {@inheritdoc}
         */
        public function run(Task\Scheduler\TaskInterface $task)
        {
            list($sourceImagePath, $destinationImagePath, $desiredWidth) = $task->getWorkload();

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
        }
    }

    // bootstrap
    $taskStorage = new Task\PHP\ArrayStorage\TaskStorage();
    $taskRunner = new Task\PHP\TaskRunner($taskStorage);
    $scheduler = new Task\PHP\Scheduler($taskStorage, $taskRunner);

    // add worker instances
    $taskRunner->addWorker('app', 'image_resize', new ImageResizeWorker());

    // schedule task
    $scheduler->schedule('app.image_resize', new Task\Scheduler\Task(['example-1.jpg', 'thumbnails/example-1.jpg', 100]));
    $scheduler->schedule('app.image_resize', new Task\Scheduler\Task(['example-2.jpg', 'thumbnails/example-2.jpg', 100]));

    // run task
    $taskRunner->run();

The example will generate two thumbnail image one for the jpg ``example-1.jpg``
and one for ``example-2.jpg`` both in the folder thumbnails.

Integration
-----------
The library provides a integration into Symfony_ framework (see :doc:`symfony`).

.. _Gearman: http://gearman.org
.. _PHP Implementation: https://github.com/php-task/php
.. _Symfony: http://symfony.com/
