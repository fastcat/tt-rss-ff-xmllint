Tiny Tiny RSS: ff_xmllint
=================

Tiny Tiny RSS plugin to run xmllint and/or tidy

Installation
------------

* Place the ff_xmllint directory in the plugins directory of your tt-rss installation.
* Go into your tt-rss preferences and enable the plugin
* (Optional) Go into your tt-rss preferences and choose which tools are run on feed contents.

By default, only xmllint is run.  Tidy can resolve more feed problems, but makes much larger changes to the feed content than xmllint, even when no changes are necessary.

To run the tools, they must be in your web server's $PATH.
