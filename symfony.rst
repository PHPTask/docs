Symfony Bundle
==============
The symfony bundle integrates php-task into your symfony application.

Features
--------
Additional features which are implemented in this bundle.

* Handler discovery
* Automatic registration of frequent tasks
* Different run possibilities

Installation
------------

.. code-block:: bash

   composer require php-task/TaskBundle 1.0-x@dev

Usage
-----
There are currently two ways to run tasks.

kernel.terminate
^^^^^^^^^^^^^^^^
The tasks will automatically executed when symfony fire this event
which will be fired when the response is sen to the browser.

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
run time. This command can be called by a cronjob which enables frequent
tasks.

.. code-block:: bash

   app/console task:run

Configuration Reference
-----------------------

.. code-block:: yaml

   task:
      storage: array # One of "array"; "doctrine"
      run:
         mode: 'off' # One of "off"; "listener"

.. _fastcgi_finish_request: http://php.net/manual/en/function.fastcgi-finish-request.php
.. _PHP FPM: http://php.net/manual/en/install.fpm.php
