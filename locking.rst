Locking
=======
To avoid concurrency issues, the library is able to lock other executions which
would run at the same time. This could happen if multiple workers (e.g. cronjobs)
run at the same time.

Each ``TaskHandler`` decides if and how many other Tasks will be blocked while a
task with this handler is running. If the ``TaskHandler`` implements the
``LockingTaskHandlerInterface`` the Lock-Component is enabled for this handler.
The interface consists of a single method ``getLockKey($workload)`` which
returns a locking-key. Tasks with the same locking-key will not be executed at
the same time.

Example
*******

.. code-block:: php

    <?php

    include __DIR__ . '/vendor/autoload.php';

    class ImageResizeHandler implements Task\Lock\LockingTaskHandlerInterface
    {
        public function handle($workload)
        {
            ...
        }

        public function getLockKey($workload)
        {
            return self::class;

            // or append workload data
            return self::class . '-' . $workload['id'];
        }
    }
