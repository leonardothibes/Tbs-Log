Tbs\Log [![Build Status](https://secure.travis-ci.org/leonardothibes/Tbs-Log.png)](http://travis-ci.org/leonardothibes/Tbs-Log)
=======

Amazing log component for PHP 5.3+

Log Levels
----------

Monolog supports the logging levels described by [PSR-3](http://www.php-fig.org/psr/psr-3).

- **DEBUG** (100): Detailed debug information.

- **INFO** (200): Interesting events. Examples: User logs in, SQL logs.

- **NOTICE** (250): Normal but significant events.

- **WARNING** (300): Exceptional occurrences that are not errors. Examples:
  Use of deprecated APIs, poor use of an API, undesirable things that are not
  necessarily wrong.

- **ERROR** (400): Runtime errors that do not require immediate action but
  should typically be logged and monitored.

- **CRITICAL** (500): Critical conditions. Example: Application component
  unavailable, unexpected exception.

- **ALERT** (550): Action must be taken immediately. Example: Entire website
  down, database unavailable, etc. This should trigger the SMS alerts and wake
  you up.

- **EMERGENCY** (600): Emergency: system is unusable.

LICENSE
-------

Copyright (c) 2013-2014 Leonardo Thibes

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is furnished
to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
