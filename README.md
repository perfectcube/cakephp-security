# CakePHP Security plugin

This plugin contains some basic security and best practices for CakePHP apps

# Installation

```git
git submodule add git://github.com/nodesagency/cakephp-security.git app/Plugin/Security
```

and add the following to your app/Config/bootstrap.php file
```php
CakePlugin::load('Security');
```

# Usage

## Validating Controllers 

```
app/Console/cake Security.check controller $path
```

### Controller rules

The following is not allowed in Controllers

* die()
* exit()
* require()
* require_once()
* include()
* include_once()
* print
* echo

## Validating Views

```
app/Console/cake Security.check view $path
```

### View rules

The following is not allowed in Views

* die()
* exit()
* require()
* require_once()
* include()
* include_once()

Additionally, the script tries to figure out if any echo you do, is wrapped in h() or not.
Anything you echo from a ViewHelper is assumed to be safe