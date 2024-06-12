<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\HttpClient\HttpClient;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
use MailPoetVendor\Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use MailPoetVendor\Symfony\Contracts\HttpClient\HttpClientInterface;
class NotCompromisedPasswordValidator extends ConstraintValidator
{
 private const DEFAULT_API_ENDPOINT = 'https://api.pwnedpasswords.com/range/%s';
 private $httpClient;
 private $charset;
 private $enabled;
 private $endpoint;
 public function __construct(HttpClientInterface $httpClient = null, string $charset = 'UTF-8', bool $enabled = \true, string $endpoint = null)
 {
 if (null === $httpClient && !\class_exists(HttpClient::class)) {
 throw new \LogicException(\sprintf('The "%s" class requires the "HttpClient" component. Try running "composer require symfony/http-client".', self::class));
 }
 $this->httpClient = $httpClient ?? HttpClient::create();
 $this->charset = $charset;
 $this->enabled = $enabled;
 $this->endpoint = $endpoint ?? self::DEFAULT_API_ENDPOINT;
 }
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof NotCompromisedPassword) {
 throw new UnexpectedTypeException($constraint, NotCompromisedPassword::class);
 }
 if (!$this->enabled) {
 return;
 }
 if (null !== $value && !\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
 throw new UnexpectedValueException($value, 'string');
 }
 $value = (string) $value;
 if ('' === $value) {
 return;
 }
 if ('UTF-8' !== $this->charset) {
 $value = \mb_convert_encoding($value, 'UTF-8', $this->charset);
 }
 $hash = \strtoupper(\sha1($value));
 $hashPrefix = \substr($hash, 0, 5);
 $url = \sprintf($this->endpoint, $hashPrefix);
 try {
 $result = $this->httpClient->request('GET', $url)->getContent();
 } catch (ExceptionInterface $e) {
 if ($constraint->skipOnError) {
 return;
 }
 throw $e;
 }
 foreach (\explode("\r\n", $result) as $line) {
 [$hashSuffix, $count] = \explode(':', $line);
 if ($hashPrefix . $hashSuffix === $hash && $constraint->threshold <= (int) $count) {
 $this->context->buildViolation($constraint->message)->setCode(NotCompromisedPassword::COMPROMISED_PASSWORD_ERROR)->addViolation();
 return;
 }
 }
 }
}
