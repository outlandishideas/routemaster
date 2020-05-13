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
