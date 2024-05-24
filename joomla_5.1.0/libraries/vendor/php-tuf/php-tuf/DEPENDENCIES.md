# PHP-TUF dependency information

## Production PHP dependencies

### Paragon IE sodium_compat
- **Repository:** https://github.com/paragonie/sodium_compat
- **Release cycle:** No formal policy documented. Follows semver. Old major
  and minor versions appear to receive support after new versions are released.
- **Security policies:**
  [Paragon security
  policy](https://github.com/paragonie/random_compat/security/policy)
  *(NB: **Full disclosure**)*
- **Security issue reporting:** `scott@paragonie.com`
- **Contacts:** ?
- **Additional dependencies:** [random_compat](https://github.com/paragonie/random_compat)
  (Same policies.)

### Guzzle PHP HTTP client
- **Repository:** https://github.com/guzzle/guzzle
- **Release cycle:** https://github.com/guzzle/guzzle/releases
- **Security policies:** https://github.com/guzzle/guzzle/security/policy
- **Security issue reporting:** security@guzzlephp.org
- **Contacts:** N/A
- **Additional dependencies:** Guzzle dependencies include php, ext-json, psr/http-client. It has additional dev dependencies.

### Symfony Validator
- **Repository:** https://github.com/symfony/validator
- **Release cycle:** https://github.com/symfony/validator/releases
- **Security policies:** https://github.com/symfony/validator/security/policy
- **Security issue reporting:** security@symfony.com
- **Contacts:** N/A
- **Additional dependencies:** The majority of dependencies are other Symfony packages. Dev dependencies include doctrine/annotations, doctrine/cache, and egulias/email-validator.

### DeepCopy
- **Repository:** https://github.com/myclabs/DeepCopy
- **Release cycle:** https://github.com/myclabs/DeepCopy/releases
- **Security policies:** There's no security policy on the Git repo.
- **Security issue reporting:** ?
- **Contacts:** N/A
- **Additional dependencies:** Only php. There are additional dev dependencies.

## Development PHP dependencies

### PHPUnit
- **Repository:** https://github.com/sebastianbergmann/phpunit
- **Release cycle:** [Supported versions of
  PHPUnit](https://phpunit.de/supported-versions.html)
- **Security policies:** PHPUnit maintainers consider the package a
  development tool that should not be used in production; therefore, they do
  not have a security release process.
- **Security issue reporting:** N/A
- **Contacts:** N/A
- **Additional dependencies:** PHPUnit adds numerous additional dependencies
  to dev builds. The majority are other packages maintained by PHPUnit or its
  author.

### Symfony PHPUnit Bridge
- **Repository:** https://github.com/symfony/phpunit-bridge
- **Release cycle:** [Symfony releases](https://symfony.com/releases)
  (Scheduled releases, continuous upgrade path, overlapping major and minor
  support, and long-term support versions.)
- **Security policies:** [Symfony security
  policy](https://symfony.com/doc/master/contributing/code/security.html)
- **Security issue reporting:** `security [at] symfony.com`
- **Contacts:** fabpot, michaelcullum
- **Additional dependencies:** None

## Development Python dependencies
@todo Document dependencies here. https://github.com/php-tuf/php-tuf/issues/159

