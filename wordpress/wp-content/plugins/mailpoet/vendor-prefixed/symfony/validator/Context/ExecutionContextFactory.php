<?php
namespace MailPoetVendor\Symfony\Component\Validator\Context;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Validator\ValidatorInterface;
use MailPoetVendor\Symfony\Contracts\Translation\TranslatorInterface;
class ExecutionContextFactory implements ExecutionContextFactoryInterface
{
 private $translator;
 private $translationDomain;
 public function __construct(TranslatorInterface $translator, string $translationDomain = null)
 {
 $this->translator = $translator;
 $this->translationDomain = $translationDomain;
 }
 public function createContext(ValidatorInterface $validator, $root)
 {
 return new ExecutionContext($validator, $root, $this->translator, $this->translationDomain);
 }
}
