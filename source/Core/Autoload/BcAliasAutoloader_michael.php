<?php

/**
 * This is an autoloader that performs several tricks to provide class aliases
 * for the new namespaced classes.
 *
 * The aliases are provided by a class map provider. But it is not sufficient
 * to
 */
class BcAliasAutoloader
{

    private $classMapProvider;
    private $classMap;
    private $reverseClassMap; // real class name => lowercase(old class name)
    private $virtualClassMap; // virtual class name => real class name

    private $skipClasses = [];

    public function autoload($class)
    {

        if ($this->isSkipClass($class)) {
            return;
        }

        if ( $this->isBcAliasRequest($class)) {
            $this->createBcAlias($class);
            return;
        }

        if ($this->isRealClassRequest($class)) {
            $this->createAliasForRealClass($class);
            return;
        }

        if ($this->isVirtualClassRequest($class)) {
            $realClass = $this->getRealClassForVirtualClass($class);
            if ($this->isRealClassRequest($class)) {
                $this->createAliasForRealClass($realClass);
            }
        }

    }

    private function isBcAliasRequest($class)
    {

        $classMap = $this->getClassMap();

        return key_exists(strtolower($class), $classMap);

    }

    private function createBcAlias($class) {

        $classMap = $this->getClassMap();
        $realClass = $classMap[strtolower($class)];
        $this->forceClassLoading($realClass);
        class_alias($realClass, $class);

    }

    private function isRealClassRequest($class) {

        $reverseClassMap = $this->getReverseClassMap();

        return key_exists($class, $reverseClassMap);

    }

    private function createAliasForRealClass($class)
    {
        $classMap = $this->getReverseClassMap();
        $alias = $classMap[$class];
        $this->forceClassLoading($class);
        class_alias($class, $alias);

    }

    private function isVirtualClassRequest($class) {

        $virtualClassMap = $this->getVirtualClassMap();

        return key_exists($class, $virtualClassMap);

    }

    private function getRealClassForVirtualClass($class) {

        $virtualClassMap = $this->getVirtualClassMap();

        return $virtualClassMap[$class];

    }

    private function forceClassLoading($class)
    {

        if (!class_exists($class) and !interface_exists($class)) {
            $this->addSkipClass($class);
            spl_autoload_call($class);
            $this->removeSkipClass($class);
        }
        if (!class_exists($class) and !interface_exists($class)) {
            throw new Exception("Could not load class $class");
        }
    }

    private function getClassMapProvider()
    {
        if (!$this->classMapProvider) {
            $editionSelector = new \OxidEsales\EshopCommunity\Core\Edition\EditionSelector();
            $this->classMapProvider = new \OxidEsales\EshopCommunity\Core\ClassMapProvider($editionSelector);
        }
        return $this->classMapProvider;
    }

    private function getClassMap()
    {

        if (!$this->classMap) {
            $this->classMap = array_merge($this->getClassMapProvider()->getOverridableClassMap(),
                $this->getClassMapProvider()->getNotOverridableClassMap());
        }
        return $this->classMap;

    }

    private function getReverseClassMap()
    {

        if (!$this->reverseClassMap) {
            $this->reverseClassMap = array_flip($this->getClassMap());
        }
        return $this->reverseClassMap;

    }

    private function getVirtualClassMap()
    {

        if (!$this->virtualClassMap) {
            $this->virtualClassMap = $this->getClassMapProvider()->getOverridableVirtualNamespaceClassMap();
        }
        return $this->virtualClassMap;

    }

    private function isSkipClass($class)
    {

        return in_array($class, $this->skipClasses);

    }

    private function addSkipClass($class)
    {

        $this->skipClasses[] = $class;

    }

    private function removeSkipClass($class)
    {

        unset($this->skipClasses[array_search($class, $this->skipClasses)]);

    }
}

spl_autoload_register([new BcAliasAutoloader(), 'autoload']);
