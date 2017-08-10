Symfony Bundle
==============
The symfony bundle integrates php-task into your symfony application.

Features
--------
Additional features which are implemented in this bundle.

* Handler discovery (over ``task.handler``-tag)
* Different run possibilities
* Different commands to manage and debug commands
* Persist tasks and executions in database
* Run statistics foreach execution of tasks
* Predefined system-tasks
* Locking mechanism to avoid concurrency problems

Installation
------------

.. code-block:: bash

   composer require php-task/task-bundle 1.0.x-dev

Usage
-----
There are currently two ways to run tasks.

Event: kernel.terminate
^^^^^^^^^^^^^^^^^^^^^^^
The tasks will automatically executed after sending the response.

.. note::

   Internally, the HttpKernel makes use of the fastcgi_finish_request_
   PHP function. This means that at the moment, only the `PHP FPM`_
   server API is able to send a response to the client while the
   server's PHP process still performs some tasks. With all other
   server APIs, listeners to kernel.terminate are still executed, but
   the response is not sent to the client until they are all completed.

Command
^^^^^^^
The bundle provides a command to run all taks which are scheduled before
run time. This command can be called by a cronjob which enables recurring
tasks.

.. code-block:: bash

   app/console task:run

.. note::

   This option only works if you enable the storage in doctrine which will
   persist your tasks in a table-structure.

System-Tasks
------------
System-tasks can be used to predefine tasks for deployment. The developer
can define which handler will be called (with an ``cron_expression`` and
a ``workload``). This tasks can be scheduled with the following command.

.. code-block:: yaml

    task:
        system_tasks:
            my-task:
                enabled:              true
                handler_class:        'AppBundle\Handler\TestHandler'
                cron_expression:      '@daily'

.. code-block:: bash

   bin/console task:schedule:system-tasks

Already scheduled system-tasks can be disabled in the configuration. But
bigger changes like changing the ``handler_class`` are currently not
supported.

After addition or changing in the config you have to run the command again
to be sure that the task-table will be updated.

Locking
-------
Locking is used to avoid concurrency problems when multiple task-runners run at
the same time (see :doc:`locking`). This feature has to be enabled and will have
multiple different storages in the future.

Currently only file storage is implemented and usable.

Executor
--------
The executor is a basic service which executes a handler with the workload of a
task. There are two ways: inline or process. One the  The ``InlineExecutor``
calls the handler directly and on the other hand the ``ProcessExecutor`` uses
an own process to isolate each run.

We recommend using the ``ProcessExecutor`` because there the tasks do not
influence each other.

Configuration Reference
-----------------------

.. code-block:: yaml

    task:
        storage:                doctrine # One of "array"; "doctrine"
        adapters:
            doctrine:
                clear:          true
        run:
            mode:               'off' # One of "off"; "listener"
        locking:
            enabled:            false
            storage:            file # One of "file"
            ttl:                600
            storages:
                file:
                    directory:  '%kernel.cache_dir%/tasks'
        executor:
            type:               inline # One of "inline"; "process"
            process:
                console:        '%kernel.root_dir%/../bin/console'
        system_tasks:

            # Prototype
            -
                enabled:        true
                handler_class:   ~
                workload:        null
                cron_expression: ~

.. _fastcgi_finish_request: http://php.net/manual/en/function.fastcgi-finish-request.php
.. _PHP FPM: http://php.net/manual/en/install.fpm.php
