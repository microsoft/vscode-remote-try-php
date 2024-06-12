<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\Exception;
if (!defined('ABSPATH')) exit;
class InvalidParameterTypeException extends InvalidArgumentException
{
 public function __construct(string $serviceId, string $type, \ReflectionParameter $parameter)
 {
 $acceptedType = $parameter->getType();
 $acceptedType = $acceptedType instanceof \ReflectionNamedType ? $acceptedType->getName() : (string) $acceptedType;
 $this->code = $type;
 $function = $parameter->getDeclaringFunction();
 $functionName = $function instanceof \ReflectionMethod ? \sprintf('%s::%s', $function->getDeclaringClass()->getName(), $function->getName()) : $function->getName();
 parent::__construct(\sprintf('Invalid definition for service "%s": argument %d of "%s()" accepts "%s", "%s" passed.', $serviceId, 1 + $parameter->getPosition(), $functionName, $acceptedType, $type));
 }
}
