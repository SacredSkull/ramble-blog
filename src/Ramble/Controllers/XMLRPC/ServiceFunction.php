<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 06/08/2017
 * Time: 21:00
 */

namespace Ramble\Controllers\XMLRPC;


use function foo\func;

class ServiceFunction {
    /**
     * @var string
     */
    private $name = "";

    /**
     * @var callable
     */
    private $function = null;

    /**
     * @var string[]
     */
    private $signature = null;

    /**
     * @var string
     */
    private $docstring = "";

    public function __construct(string $name, callable $func, array $sig, string $docs) {
        $this->name = $name;
        $this->function = $func;
        $this->signature = $sig;
        $this->docstring = $docs;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return callable
     */
    public function getFunction() : callable {
        return $this->function ?? function(){};
    }

    /**
     * @return string[]
     */
    public function getSignature() : array {
        return $this->signature ?? [];
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->docstring;
    }

    public function toFunctionDescriptor(): array {
        return[
            $this->getName() => [
                "function" => $this->getFunction(),
                "signature" => $this->getSignature(),
                "docstring" => $this->getDescription() . $this->prettySignature()
            ]
        ];
    }

    public function prettySignature() : string {
        $pretty = "";
        $first = true;
        $index = 1;
        $size = count($this->getSignature()[0]) - 1;
        $type = $this->getSignature()[0][0];

        foreach ($this->getSignature()[0] as $signature) {
            if($first) {
                $first = false;
                $pretty = "Function returns '$type', accepts " . $size . " arguments (";
                $index++;
                continue;
            }

            if($index == $size){
                $pretty .= $signature . ")";
                break;
            }

            $pretty .= $signature . ", ";
            $index++;
        }

        return rtrim($pretty, " (");
    }
}