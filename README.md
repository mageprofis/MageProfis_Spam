MageProfis_Spam
===================

Info
-----------

Since April 2017 we recognise many newsletter subscription and account registration requests in Magento 1.x Systems

So we build up this Extension, to prevent the registrations from theses bots.

There are also an Option to add an XBL Service.
The Service have to result on Blocked IPs with "BLOCKED" as string.
Information in the [Source Code](https://github.com/mageprofis/MageProfis_Spam/blob/4082eac6ee5966b3043df0625c96a777dad1e103/src/code/Helper/Data.php#L54-L55)

to get some informations about the requests
```cat /var/log/nginx/*-access.log* | grep '/newsletter/subscriber/new/' | grep POST | less```

Requirements
------------
- PHP >= 5.3.0

Compatibility
-------------
- Magento >= 1.7.0.2

Support
-------
If you encounter any problems or bugs, please create an issue on [GitHub](https://github.com/mageprofis/MageProfis_Spam/issues).

Contribution
------------
Any contribution to the development is highly welcome. The best possibility to provide any code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
* Mathis Klooss

Licence
-------
[Open Software License (OSL-3)](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2015 Mage-Profis GmbH