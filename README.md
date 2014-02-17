# OpenConext Engine Test Stand #


> An engine test stand is a facility used to develop, characterize and test engines.
> The facility, often offered as a product to automotive OEMs,
> allows engine operation in different operating regimes and offers measurement of
> several physical variables associated with the engine operation.

* [Wikipedia: Engine Test Stand](http://en.wikipedia.org/wiki/Engine_test_stand)

This project aims to make system level testing of OpenConext Engineblock possible and convenient.

It does this by:

* Using an low-friction functional testing library, tests are written in the Gherkin language and executed using Behat.
* Allowing replaying of SAML requests / responses from the logging.
* Offering 'mock' services that allow overriding of the following Engineblock services:
    * Identity Provider / Service Provider   
    * ServiceRegistry, federation metadata
    * Date and time
    * ID Generation

It requires that EngineBlock has been configured for the special 'functional-testing' environment.
See **Installation - Replaying**.

## Requirements ##
* PHP > 5.3

## Installation - Base ##
````bash
git clone git@github.com:OpenConext/OpenConext-engine-test-stand.git &&
cd OpenConext-engine-test-stand &&
wget -P bin https://getcomposer.org/composer.phar &&
php -d memory_limit=1G bin/composer.phar install
````

## Installation - Replaying ##

To make functional testing work do the following:

### Host EngineBlock on 'functional-testing' environment ###

Example:
````
    <VirtualHost *:443>
        DocumentRoot /opt/www/engineblock/www/authentication
        ServerName   engine-test.demo.openconext.org
        SetEnv ENGINEBLOCK_ENV functional-testing

        ...

    </VirtualHost>
````

````
192.168.56.101 engine-test.demo.openconext.org
````

Add the following to /etc/surfconext/engineblock.ini :
````ini
[functional-testing : testing]

functionalTesting.engineBlockUrl = "https://engine-test.demo.openconext.org"
````

What this does is trigger EngineBlock to use a different DI Container that allows:

* Overriding of SuperGlobals
* Loading of ServiceRegistry data from a file on disk (fixture)
* Overriding of time (make EB think it's running at some earlier time)
* Overriding generation of IDs for SAML messages.

### Usage Scenario: Functional Testing ###

Run the normal Functional Tests like so:
````bash
./bin/behat.sh
````

### Usage Scenario: SAML Replaying ###

#### Exporting 'flows' ####
````
replay
  replay:flow:export                    Export all flows to a directory
  replay:flow:filter                    Find all sessions that have an attached flow
  replay:sessions:find                  Find all sessions from log output on STDIN or for a given file
````

##### Step 1: Find sessions #####

````bash
[vagrant@localhost OpenConext-engine-test-stand]$ app/console replay:sessions:find --help
Usage:
 replay:sessions:find [file]

Arguments:
 file                  File to get sessions from.

Options:
 --help (-h)           Display this help message.
 --quiet (-q)          Do not output any message.
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version.
 --ansi                Force ANSI output.
 --no-ansi             Disable ANSI output.
 --no-interaction (-n) Do not ask any interactive question.
 --shell (-s)          Launch the shell.
 --process-isolation   Launch commands from shell as a separate process.
 --env (-e)            The Environment name. (default: "dev")
 --no-debug            Switches off debug mode.

Help:
 The replay:sessions:find command finds session identifiers in log output:
 
 grep "something" engineblock.log | php app/console replay:sessions:find
 
 The optional argument specifies to read from a file (by default it reads from the standard input):
 
 php app/console replay:sessions:find engineblock.log
````

##### Step 2: Filtering sessions for those with a flow #####

````bash
[vagrant@localhost OpenConext-engine-test-stand]$ app/console replay:flow:filter --help
Usage:
 replay:flow:filter logfile [sessionFile]

Arguments:
 logfile               File to get flows from
 sessionFile           File to get sessions from.

Options:
 --help (-h)           Display this help message.
 --quiet (-q)          Do not output any message.
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version.
 --ansi                Force ANSI output.
 --no-ansi             Disable ANSI output.
 --no-interaction (-n) Do not ask any interactive question.
 --shell (-s)          Launch the shell.
 --process-isolation   Launch commands from shell as a separate process.
 --env (-e)            The Environment name. (default: "dev")
 --no-debug            Switches off debug mode.

Help:
 The replay:flow:filter filters out the sessions with incomplete flows:
 
 grep "something" engineblock.log | app/console functional-testing:sessions:find | app/console replay:flow:filter engineblock.log
 
 The optional argument specifies to read from a file (by default it reads from the standard input):
 
 php app/console replay:flow:filter engineblock.log engineblock.log
````

##### Step 3: Exporting flows #####

````bash
[vagrant@localhost OpenConext-engine-test-stand]$ app/console replay:flow:export --help
Usage:
 replay:flow:export logfile [outputDir] [sessionFile]

Arguments:
 logfile               File to get flows from.
 outputDir             Directory to export flows to (defaults to the temporary directory). (default: "/tmp")
 sessionFile           File to get sessions from (defaults to STDIN).

Options:
 --help (-h)           Display this help message.
 --quiet (-q)          Do not output any message.
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version.
 --ansi                Force ANSI output.
 --no-ansi             Disable ANSI output.
 --no-interaction (-n) Do not ask any interactive question.
 --shell (-s)          Launch the shell.
 --process-isolation   Launch commands from shell as a separate process.
 --env (-e)            The Environment name. (default: "dev")
 --no-debug            Switches off debug mode.

Help:
 The replay:flow:export command exports flows to a directory, example:
 
 grep "something" engineblock.log | app/console fu:sessions:find | app/console fu:flow:filter | app/console replay:flow:export engineblock.log
 
 Find log lines with "something", from those get the sessions, for those sessions give only the sessions that have complete flows, for those sessions export all flows to /tmp.
````

#### Step 4: Selecting a flow ####
````bash
ln -s /dir/to/eb-flow-abcdef123 fixtures/replay
````

#### Step 5: Replaying a Flow ####
````bash
./bin/behat-replay.sh
````

### Usage: Other ###

Dumping the internal state of the mock Service Registry
```bash
php ./bin/dump_sr_state.php
```

## About OpenConext

OpenConext is an OpenSource technology stack for creating and running Collaboration platforms. It uses technologies from Federated Identity Management, as is available in Research and Educational Access Federations, Group management and OpenSocial Social Networking Technology. The aim of the software is to provide a middleware platform that can combine generic and specialized collaboration tools and services, within Research and Education, and beyond, and make these available for collaboration over institutional and national borders. The features section describes the current and planned features for the platform.

OpenConext was developed by SURFnet as part of the SURFworks programme. SURFnet runs an instance of the platform for research and education in The Netherlands as SURFconext


OpenConext: [http://www.openconext.org](http://www.openconext.org)

SURFconext: [http://www.surfconext.nl](http://www.surfconext.nl)


## License

See the LICENSE-2.0.txt file

## Disclaimer

See the NOTICE.txt file
