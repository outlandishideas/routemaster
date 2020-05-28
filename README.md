# Routemaster WordPress plugin

Replaces the built-in WordPress routing logic with one defined by URL patterns.

## Installation

- Install and activate plugin
- Create Router subclass that implements abstract `routes` function
- add the following to your theme:

~~~~
$router = MyRouter::getInstance();
$router->setup();
~~~~

### Use with [OOWP](https://github.com/outlandishideas/oowp)

To successfully extend `OowpRouter` and gain router awareness of post objects,
you should also install OOWP. This is optional if you avoid the
`Outlandish\Wordpress\Routemaster\Oowp` namespace.

To install it:

    composer require outlandish/oowp

### Wordpress.org

Unfortunately for _new_ projects Wordpress [are not accepting](https://make.wordpress.org/plugins/2016/03/01/please-do-not-submit-frameworks/)
libraries as plugins which they host. The GitHub Actions support for this
is now deleted since we tried and failed to have the project added there.

See [this PR](https://github.com/outlandishideas/routemaster/pull/14/files#diff-2b7bfbec6c9ddad9e63030b179d67ece) if you'd like to refer back to the
GitHub Actions setup for Wordpress.org publishing, for another plugin which meets the
current guidelines.
