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
