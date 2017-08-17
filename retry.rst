Retry
=====
Some of the tasks which will be executed by a handler are risky and could fail
(e.g. long running, i/o ...). To allow retry of this tasks the handler is able
to implement the interface ``RetryTaskHandlerInterface`` and specify a maximum
amount of attempts to pass the task.

The retries will be scheduled as soon as possible and the following tasks will
be scheduled after this retry later. This prevent the following tasks to fail
because of bad starting conditions because of the previous task.

Example
*******

.. code-block:: php

    <?php

    include __DIR__ . '/vendor/autoload.php';

    class ImageResizeHandler implements Task\Handler\TaskHandlerInterface, Task\Executor\RetryTaskHandlerInterface
    {
        public function handle($workload)
        {
            try {
                $this->doSomething();
            } catch (SpecificException $exception) {
                throw new FailedException($exception);
            }

            // other exceptions will be propagated to the runner
            // the runner will retry the execution until the max-attempts are reached
        }

        public function getMaximumAttempts()
        {
            return 3;
        }
    }


