Symfony Bundle
==============
The symfony bundle integrates php-task into your symfony application.

Features
--------
Additional features which are implemented in this bundle.

* Handler discovery
* Different run possibilities
* Different commands to manage and debug commands
* Persist tasks and executions in database
* Run statistics foreach execution of tasks

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

Configuration Reference
-----------------------

.. code-block:: yaml

   task:
      storage: array # One of "array" or "doctrine"
      run:
         mode: 'off' # One of "off" or "listener"

.. _fastcgi_finish_request: http://php.net/manual/en/function.fastcgi-finish-request.php
.. _PHP FPM: http://php.net/manual/en/install.fpm.php
