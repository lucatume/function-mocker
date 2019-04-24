---
name: Bug report
about: You found a bug, damn.
title: "[BUG]"
labels: bug
assignees: lucatume

---

**Environment**
OS: [e.g. Windows, Mac, Linux]  
PHP version: [e.g. 7.1, 5.6]  
Installed PHPUnit/Codeception/PHPSpec/other version: [e.g. 2.5.0]  
Installed function-mocker version: [e.g. 2.2.1]  
Local development environment: [e.g. PHP built-in server, Valet, MAMP, Local by Flywheel, Docker]  

**function-mocker configuration and location**  
Paste, in a fenced PHP block, the content of the bootstrap file you're using to initialize Function Mocker; remove any sensible data!  

```php
require_once __DIR__ . '/../vendor/autoload.php';

\tad\FunctionMocker\FunctionMocker::init( [
	'whitelist'             => [ __DIR__ . '/some/source/files', __DIR__ . '/more/source/files' ],
	'blacklist'             => [ __DIR__ . '/do-not-wrap-this' ],
	'redefinable-internals' => [ 'time' ],
] );
```

**Code example**
If you're encountering issue while running a test please provide a meaningful example of what you're trying to do.  
Pleas use a fenced PHP block.

**Describe the bug**
A clear and concise description of what the bug is.

**Output**
If applicable paste here the output of the command that's causing the issue

**To Reproduce**
Steps to reproduce the behavior.

**Expected behavior**
A clear and concise description of what you expected to happen.

**Screenshots**
If applicable, add screenshots to help explain your problem.

**Additional context**
Add any other context about the problem here.
