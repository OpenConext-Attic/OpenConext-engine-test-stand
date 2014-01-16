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
See **Installation**.


## Installation ##

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
````
[functional-testing : testing]

functionalTesting.engineBlockUrl = "https://engine-test.demo.openconext.org"
````

What this does is trigger EngineBlock to use a different DI Container that allows:

* Overriding of SuperGlobals
* Loading of ServiceRegistry data from a file on disk (fixture)
* Overriding of time (make EB think it's running at some earlier time)
* Overriding generation of IDs for SAML messages.


### Configuration ###

Configure the Engine Test Stand (ETS) with a config.json, an example for the OpenConext Demo environment:

    {
        "engineblock-url"       : "https://engine-test.demo.openconext.org",
        "engine-test-stand-url" : "https://engine-test-stand.demo.openconext.org",

        "#idps-config-url"       : "https://stats.surfconext.nl/sr2/html-ssp-viewer-acc/saml20-idp-remote.json",
        "idps-config-url"       : "/tmp/sr-dump/saml20-idp-remote.json",

        "#sps-config-url"        : "https://stats.surfconext.nl/sr2/html-ssp-viewer-acc/saml20-sp-remote.json",
        "sps-config-url"        : "/tmp/sr-dump/saml20-sp-remote.json",

        "serviceregistry-fixture-file"  : "/fixtures/db/serviceregistry.state.php.serialized",
        "idp-fixture-file"              : "/fixtures/db/idp.states.php.serialized",
        "sp-fixture-file"               : "/fixtures/db/sp.states.php.serialized"
    }


* **engineblock-url**
Base URL (protocol + '://' + hostname) where ETS can send SAML AuthnRequests to.

* **engine-test-stand-url**
Base URL (protocol + '://' + hostname) where ETS is hosted.
Used to tell EB where it can expect SAML AuthnRequests from.

* **idps-config-url**
URL (or file) containing a JSON export of the ServiceRegistry database.

* **sps-config-url**
URL (or file) containing a JSON export of the ServiceRegistry database.

* **idp-fixture-file**
Where the ETS Mock Idp may store it's state, relative to the ETS root directory.

* **sp-fixture-file**
Where the ETS Mock Sp may store it's state, relative to the ETS root directory.

### Usage ###

Running the normal functional tests:
````
./bin/behat.sh
````

Replaying a SAML Request / Response cycle:
````
./bin/behat-replay.sh
````

Dumping the internal state of the mock Service Registry
```
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
