TYPO3 extension Extbase Realurl
===============================

	Automatic Realurl configuration for Extbase Plugins

[![Build Status](https://travis-ci.org/FluidTYPO3/extbase_realurl.png?branch=master)](https://travis-ci.org/FluidTYPO3/extbase_realurl)

# What does it do?

*Extbase Realurl* (from here on: *ER*) works in a deceptively simple way:

1. When Realurl writes the automatic configuration file, *ER* hooks onto
   the ruleset generation.
2. *ER* then analyzes every Extbase plugin used on the site by detecting
   the CType and list_type of plugins that have an Extbase base.
3. *ER* determines the topmost Extbase plugin on each page and mapes that
   page to the Extbase plugin being used.
4. *ER* selects the rule that was created for the default Controller and
   action as defined in the plugin.
5. Realurl calls a special *ER* UserFunction to translate arguments. This
   special UserFunction then processes each argument according to (by
   default) the ClassReflection of the Controller and its action with the
   option of overriding specific behaviors using doc comment annotations.

The result is that whenever Realurl writes the auomatic configuration file
which by default is *realurl_autoconf.php* then rules for all instances of
Extbase plugins are included.

The alternative to the above approach would be to either create a hardcoded
Realurl configuration for your current setup - which would lack the ability
to adjust to any plugin and page changes made by your editors - or to create
custom UserFunctions to be called when Realurl builds rules - which would be
very similar to the above approach but would require much manual work and
would not immediately apply to any Extbase plugin (unless you were to use
the exact same strategy of analyzing configured plugins, current instances
and ClassReflections of the Controllers).

*ER* does all this for you automatically. And you can control it in detail
when programming your own Extbase plugins - in a way that has zero impact
when *ER* is not installed; due to the use of doc comment annotations.

# What does it also do?

As if automatic rules for every plugin was not enough, *ER* also adds a way
to call any controller action from any plugin from any extension in the site.
The only requirement: _it only works for packages which use namespaces and
have vendor names_. This is necessary for consistency. The actions return
exactly the content returned by the controller, nothing else. And it supports
formats which makes it ideal for AJAX use or for services.

The URL syntax to call a specific action is:

```php
$base/$vendor/$extensionName/$pluginName/$optionalController/$optionalAction(.$optionalFormat)
```

Any parameter marked optional is just that: optional. Leaving it out simply
calls the default controller and action combination for that plugin; and if
none are set, the fallback `StandardController` and `indexAction` are used.

And to specify arguments:

```php
.../$action/$argumentOneName/$argumentOneValue/$argumentTwoName/$argumentTwoValue(.$optionalFormat)
// or the old-fashioned (way which also supports arrays in the usual `argument[subProperty]` syntax
.../$action.$format?$argumentOneName=$argumentOneValue&$argumentTwoName=$argumentTwoValue
```

Which makes the arguments:

```php
array(
	$argumentOneName => $argumentOneValue,
	$argumentTwoName => $argumentTwoValue,
	...
)
```

Some examples,
which hopefully should explain everything just by reading:

```
# EXT:fromage receipt in HTML format (template filename: receipt.html):
GET http://mydomain/FluidTYPO3/Fromage/Form/receipt.html

# EXT:fromage receipt in TXT format (template filename: receipt.txt):
GET http://mydomain/FluidTYPO3/Fromage/Form/receipt.txt

# EXT:mycoolext date-limited entry list in JSON format (template filename: list.json):
GET http://mydomain/MyVendorName/Mycoolext/Entries/Entry/list/from/2011/to/2013.json

# EXT:myauth user status in JSON format (returned directly by controller):
GET http://mydomain/MyVendorName/Myauth/User/status.json

# EXT:sendstuff upload action, handle input then respond in XML format (template: upload.xml)
POST http://mydomain/MyVendorName/Sendstuff/Upload/upload.xml

# EXT:ajaxsavestuff save action, handle input then respond in JSON format, some arguments as GET:
POST http://mydomain/MyVendorName/Ajaxsavestuff/Stuff/save/name/MrAndersson/location/TheMatrix.json

# EXT:showoff svg action, renders account history as SVG with graph:
GET http://mydomain/MyVendorName/Showoff/Account/history.svg
```

In short: you can call anything as long as it has a vendor name and is defined
as a plugin (note: it does not need to be _registered_, it only needs to be
_configured_ - and plugins which are _configured_ but not _registered_, are
live but cannot be inserted on pages as content. Great way to make plugins
which only deliver AJAX content and which should never be used in pages).

# Installing

Download and install as a TYPO3 extension, then enable "Automatic
configuration" in Realurl's extension configuration in the Extension Manager.
If an automatic configuration exists, remove the configuration file and allow
Realurl to rebuild it with the *ER* rulesets included.

# Usage

Basic usage of *ER* is simple - once installed, it works automatically to
translate the arguments it is given but in a slightly different way than what
is usually done by Realurl. The slighly modified behavior matches Extbase's
expected variable types better than the standard behavior.

Basic usage consists of simply installing *ER* and remembering to clear
Realurl's automatic configuration file if the plugin configuration is changed.

Most of the custom "clear Realurl caches" extensions on TER can do this for
you. A hook has not been included in *ER* (at the time of writing this) since
not all site operators would enjoy this behavior and a toggle would be the
only feature to require any sort of extension configuration - so this is
intentionally not included.

__Note that *ER* does not work on POST'ed arguments but does translate the
GET part of the REQUEST when responding to POST. For example you POST to the
URL /path/to/page/Controller/action.html and call the proper method because it
is read using GET - but must POST the data arguments in their original format.__

__Also note that *ER* will transform URLs you redirect() to from your Controller
but will not transform URLs that are constructed by submitting a form that uses
GET request mode (since this is URL is built by the browser).__

# Integration

An advanced integration is possible by using annotations on Controller classes
and its action methods. By using annotations you can control how *ER* creates
the rulesets for your pages. For example by configuring wether *ER* should
add path segments for the Controller and action parameters (pseudo code, only
the annotations matter here):

	/**
	 * @route NoMatch('bypass')
	 */
	class MyController {

		/**
		 * @route NoMatch('bypass')
		 * @route NoMatch(NULL) $object
		 */
		public function myAction(Tx_MyExt_Domain_Model_MyObject $object) {
			return;
		}

	}

The above example asks Realurl to "bypass" (i.e. ignore completely):

* The tx_myext_myplugin[controller] parameter because *@route NoMatch('bypass')*
  is set on the Controller class.
* The tx_myext_myplugin[action] parameter because *@route NoMatch('bypass')* is
  set on the Controller action.

And would make the tx_myext_myplugin[object] parameter required.

Such a configuration would make particular sense for a show action which takes a
human-readable URL parameter identifying $object (just like EXT:news suggest in
the Wiki concerning Realurl configuration)

See <http://forge.typo3.org/projects/extension-news/wiki/RealURL> for the general
idea behind the transformation.

By default both the Controller and action parameters are included, making URLs
look similar to /path/to/page/Controller/action/human-readable-title.html.

Future revisions will allow changing this behavior without using doc comment
annotations, making it easier to effect how *ER* processes plugins that don't
provide their own annotations to - for example as is very common - hide the
Controller for some plugins and Controller plus action for "detail view" actions
which are created as plugins that call only one action on one Controller - just
like EXT:news does (and coincidentally, *ER* makes perfect rules for EXT:news but
they do include the Controller and action even for detail view).

Future revisions may even be able to handle these cases automatically, scanning
the plugin configuration to determine wether it makes sense to include the two
"hardcoded" arguments, Controller and action. But until such a time these two
arguments will be included unless manually "bypass"-ed using annotations.

# About Domain Objects

*ER* will recognize if your Controller action accepts a Domain Object as argument
and if this is the case, *ER* will detect which field to use as human-readable
URL parameter (i.e. /detail/this-is-my-object-title.html). Instead of requiring
that you manually configure the label field, *ER* detects this from the "label"
property defined in the TCA of the Domain Object - along with the DB table.

Future revisions will allow greater control over particularly Domain Object
arguments and their behavior.
