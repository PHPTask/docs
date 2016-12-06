Components
==========
The library consists of three main parts.

TaskScheduler
-------------
The ``TaskScheduler`` stores tasks and schedules executions. For each task
there exists exactly one execution in the status ``planned`` or ``running``. If
the task will only one time it only get one execution at all. For recurring
tasks a new execution will be generated when the old one is ``completed`` or
``failed``.

Each task consists of:

- ``uuid``: unique identifier
- ``handlerClass``: class-name of the handler which will be executed for this
  task
- ``workload``: the workload will be passed to the handler
- ``interval``: for recurring tasks this fields contains a cron-expression
- ``firstExecution``: the earliest possible execution
- ``lastExecution``: the latest possible execution

TaskRunner
----------
The ``TaskRunner`` takes the ``planned`` executions and executes the ``Handler``
with the workload of the task.

Handler
-------
The ``Handler`` implements the domain-logic for a task. The library implements
a Factory which uses Reflection to create a new instance for the
``handlerClass`` of the task.

.. note::

    The :doc:`symfony` uses tagged-services to find available ``Handler``.
