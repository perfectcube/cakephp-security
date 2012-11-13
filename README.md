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
* print()
* echo()
* eval()

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
* eval()

Additionally, the script tries to figure out if any echo you do, is wrapped in h() or not.
Anything you echo from a ViewHelper is assumed to be safe

# Workarounds

Calling die() / exit() is fairly normal in PHP, but it makes CakePHP code impossible to test.
If you really need to stop the processing, please use Cake's _stop() method, defined in Object.php