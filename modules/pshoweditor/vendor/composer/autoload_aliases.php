<?php

// Functions and constants

    if (!\function_exists('\IsModulesInPath')) {
        function IsModulesInPath(...$args)
        {
            return \pshoweditorscoped_IsModulesInPath(...\func_get_args());
        }
    }
    if (!\function_exists('\getModulePath')) {
        function getModulePath(...$args)
        {
            return \pshoweditorscoped_getModulePath(...\func_get_args());
        }
    }
    if (!\function_exists('\findRealFilePath')) {
        function findRealFilePath(...$args)
        {
            return \pshoweditorscoped_findRealFilePath(...\func_get_args());
        }
    }
    if (!\function_exists('\getModuleName')) {
        function getModuleName(...$args)
        {
            return \pshoweditorscoped_getModuleName(...\func_get_args());
        }
    }
    if (!\function_exists('\getDiskFreeSpace')) {
        function getDiskFreeSpace(...$args)
        {
            return \pshoweditorscoped_getDiskFreeSpace(...\func_get_args());
        }
    }
    if (!\function_exists('\findTranslationsInTplFile')) {
        function findTranslationsInTplFile(...$args)
        {
            return \pshoweditorscoped_findTranslationsInTplFile(...\func_get_args());
        }
    }
    if (!\function_exists('\findTranslationsInPhpFile')) {
        function findTranslationsInPhpFile(...$args)
        {
            return \pshoweditorscoped_findTranslationsInPhpFile(...\func_get_args());
        }
    }
    if (!\function_exists('\getmicrotime')) {
        function getmicrotime(...$args)
        {
            return \pshoweditorscoped_getmicrotime(...\func_get_args());
        }
    }
    if (!\function_exists('\__')) {
        function __(...$args)
        {
            return \pshoweditorscoped___(...\func_get_args());
        }
    }
}
namespace PShowEditorScoped {
    class AliasAutoloader
    {
        private string $includeFilePath;
        private array $autoloadAliases = array('Prestashow\PrestaBlockEditor\BlockEditor' => array('type' => 'class', 'classname' => 'BlockEditor', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaBlockEditor', 'extends' => 'PShowEditorScoped\Prestashow\PrestaBlockEditor\BlockEditor', 'implements' => array()), 'Prestashow\PrestaBlockEditor\Service\ImageService' => array('type' => 'class', 'classname' => 'ImageService', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaBlockEditor\Service', 'extends' => 'PShowEditorScoped\Prestashow\PrestaBlockEditor\Service\ImageService', 'implements' => array()), 'Prestashow\PrestaCore\Adapter\UpdateService' => array('type' => 'class', 'classname' => 'UpdateService', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Adapter', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Adapter\UpdateService', 'implements' => array()), 'Prestashow\PrestaCore\Adapter\ServiceAdapter' => array('type' => 'class', 'classname' => 'ServiceAdapter', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Adapter', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Adapter\ServiceAdapter', 'implements' => array()), 'Prestashow\PrestaCore\Adapter\UpdateServiceAdapter' => array('type' => 'class', 'classname' => 'UpdateServiceAdapter', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Adapter', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Adapter\UpdateServiceAdapter', 'implements' => array()), 'Prestashow\PrestaCore\Composer\ScopingPlugin' => array('type' => 'class', 'classname' => 'ScopingPlugin', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Composer', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Composer\ScopingPlugin', 'implements' => array(0 => 'Composer\Plugin\PluginInterface', 1 => 'Composer\EventDispatcher\EventSubscriberInterface')), 'Prestashow\PrestaCore\Controller\BackupController' => array('type' => 'class', 'classname' => 'BackupController', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Controller', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Controller\BackupController', 'implements' => array()), 'Prestashow\PrestaCore\Controller\HookController' => array('type' => 'class', 'classname' => 'HookController', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Controller', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Controller\HookController', 'implements' => array()), 'Prestashow\PrestaCore\Controller\SettingsController' => array('type' => 'class', 'classname' => 'SettingsController', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Controller', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Controller\SettingsController', 'implements' => array()), 'Prestashow\PrestaCore\Controller\UpdateController' => array('type' => 'class', 'classname' => 'UpdateController', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Controller', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Controller\UpdateController', 'implements' => array()), 'Prestashow\PrestaCore\Database\Migrations\AbstractMigration' => array('type' => 'class', 'classname' => 'AbstractMigration', 'isabstract' => \true, 'namespace' => 'Prestashow\PrestaCore\Database\Migrations', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Database\Migrations\AbstractMigration', 'implements' => array()), 'Prestashow\PrestaCore\Database\Migrations\MigrationCoreTool' => array('type' => 'class', 'classname' => 'MigrationCoreTool', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Database\Migrations', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Database\Migrations\MigrationCoreTool', 'implements' => array()), 'Prestashow\PrestaCore\Database\Migrations\MigrationTool' => array('type' => 'class', 'classname' => 'MigrationTool', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Database\Migrations', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Database\Migrations\MigrationTool', 'implements' => array()), 'Prestashow\PrestaCore\Database\Migrations\Version0' => array('type' => 'class', 'classname' => 'Version0', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Database\Migrations', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Database\Migrations\Version0', 'implements' => array()), 'Prestashow\PrestaCore\Database\Migrations\Version1' => array('type' => 'class', 'classname' => 'Version1', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Database\Migrations', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Database\Migrations\Version1', 'implements' => array()), 'Prestashow\PrestaCore\Entity\Hook' => array('type' => 'class', 'classname' => 'Hook', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Entity', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Entity\Hook', 'implements' => array()), 'Prestashow\PrestaCore\Entity\Notification' => array('type' => 'class', 'classname' => 'Notification', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Entity', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Entity\Notification', 'implements' => array()), 'Prestashow\PrestaCore\Entity\NotificationRead' => array('type' => 'class', 'classname' => 'NotificationRead', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Entity', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Entity\NotificationRead', 'implements' => array()), 'Prestashow\PrestaCore\Exception\PrestashowException' => array('type' => 'class', 'classname' => 'PrestashowException', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Exception', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Exception\PrestashowException', 'implements' => array()), 'Prestashow\PrestaCore\Exception\UpdateException' => array('type' => 'class', 'classname' => 'UpdateException', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Exception', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Exception\UpdateException', 'implements' => array()), 'Prestashow\PrestaCore\Model\AbstractAdminController' => array('type' => 'class', 'classname' => 'AbstractAdminController', 'isabstract' => \true, 'namespace' => 'Prestashow\PrestaCore\Model', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Model\AbstractAdminController', 'implements' => array()), 'Prestashow\PrestaCore\Model\AbstractDemoContent' => array('type' => 'class', 'classname' => 'AbstractDemoContent', 'isabstract' => \true, 'namespace' => 'Prestashow\PrestaCore\Model', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Model\AbstractDemoContent', 'implements' => array()), 'Prestashow\PrestaCore\Model\AbstractEntity' => array('type' => 'class', 'classname' => 'AbstractEntity', 'isabstract' => \true, 'namespace' => 'Prestashow\PrestaCore\Model', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Model\AbstractEntity', 'implements' => array()), 'Prestashow\PrestaCore\Model\AbstractModule' => array('type' => 'class', 'classname' => 'AbstractModule', 'isabstract' => \true, 'namespace' => 'Prestashow\PrestaCore\Model', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Model\AbstractModule', 'implements' => array()), 'Prestashow\PrestaCore\Model\AbstractRepository' => array('type' => 'class', 'classname' => 'AbstractRepository', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Model', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Model\AbstractRepository', 'implements' => array()), 'Prestashow\PrestaCore\Model\AbstractService' => array('type' => 'class', 'classname' => 'AbstractService', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Model', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Model\AbstractService', 'implements' => array()), 'Prestashow\PrestaCore\Model\DemoObjectModel' => array('type' => 'class', 'classname' => 'DemoObjectModel', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Model', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Model\DemoObjectModel', 'implements' => array()), 'Prestashow\PrestaCore\Model\ModuleSettings' => array('type' => 'class', 'classname' => 'ModuleSettings', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Model', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Model\ModuleSettings', 'implements' => array()), 'Prestashow\PrestaCore\Service\DatabaseService' => array('type' => 'class', 'classname' => 'DatabaseService', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Service', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Service\DatabaseService', 'implements' => array()), 'Prestashow\PrestaCore\Service\DemoContentService' => array('type' => 'class', 'classname' => 'DemoContentService', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Service', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Service\DemoContentService', 'implements' => array()), 'Prestashow\PrestaCore\Service\IniService' => array('type' => 'class', 'classname' => 'IniService', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Service', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Service\IniService', 'implements' => array()), 'Prestashow\PrestaCore\Service\OverrideService' => array('type' => 'class', 'classname' => 'OverrideService', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Service', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Service\OverrideService', 'implements' => array()), 'Prestashow\PrestaCore\Service\RecommendationService' => array('type' => 'class', 'classname' => 'RecommendationService', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Service', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Service\RecommendationService', 'implements' => array()), 'Prestashow\PrestaCore\Service\ToolsService' => array('type' => 'class', 'classname' => 'ToolsService', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Service', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Service\ToolsService', 'implements' => array()), 'Prestashow\PrestaCore\Service\TranslationService' => array('type' => 'class', 'classname' => 'TranslationService', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Service', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Service\TranslationService', 'implements' => array()), 'Prestashow\PrestaCore\Util\HookOverrideFix' => array('type' => 'class', 'classname' => 'HookOverrideFix', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaCore\Util', 'extends' => 'PShowEditorScoped\Prestashow\PrestaCore\Util\HookOverrideFix', 'implements' => array()), 'PrestashowAutoload' => array('type' => 'class', 'classname' => 'PrestashowAutoload', 'isabstract' => \false, 'namespace' => '\\', 'extends' => 'PShowEditorScoped_PrestashowAutoload', 'implements' => array()), 'Prestashow\PrestaUpdate\Model\License' => array('type' => 'class', 'classname' => 'License', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaUpdate\Model', 'extends' => 'PShowEditorScoped\Prestashow\PrestaUpdate\Model\License', 'implements' => array()), 'Prestashow\PrestaUpdate\Service\MultistoreService' => array('type' => 'class', 'classname' => 'MultistoreService', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaUpdate\Service', 'extends' => 'PShowEditorScoped\Prestashow\PrestaUpdate\Service\MultistoreService', 'implements' => array()), 'Prestashow\PrestaUpdate\Service\UpdateService' => array('type' => 'class', 'classname' => 'UpdateService', 'isabstract' => \false, 'namespace' => 'Prestashow\PrestaUpdate\Service', 'extends' => 'PShowEditorScoped\Prestashow\PrestaUpdate\Service\UpdateService', 'implements' => array()));
        public function __construct()
        {
            $this->includeFilePath = __DIR__ . '/autoload_alias.php';
        }
        public function autoload($class)
        {
            if (!isset($this->autoloadAliases[$class])) {
                return;
            }
            switch ($this->autoloadAliases[$class]['type']) {
                case 'class':
                    $this->load($this->classTemplate($this->autoloadAliases[$class]));
                    break;
                case 'interface':
                    $this->load($this->interfaceTemplate($this->autoloadAliases[$class]));
                    break;
                case 'trait':
                    $this->load($this->traitTemplate($this->autoloadAliases[$class]));
                    break;
                default:
                    // Never.
                    break;
            }
        }
        private function load(string $includeFile)
        {
            file_put_contents($this->includeFilePath, $includeFile);
            include $this->includeFilePath;
            file_exists($this->includeFilePath) && unlink($this->includeFilePath);
        }
        private function classTemplate(array $class): string
        {
            $abstract = $class['isabstract'] ? 'abstract ' : '';
            $classname = $class['classname'];
            if (isset($class['namespace'])) {
                $namespace = "namespace {$class['namespace']};";
                $extends = '\\' . $class['extends'];
                $implements = empty($class['implements']) ? '' : ' implements \\' . implode(', \\', $class['implements']);
            } else {
                $namespace = '';
                $extends = $class['extends'];
                $implements = !empty($class['implements']) ? '' : ' implements ' . implode(', ', $class['implements']);
            }
            return <<<EOD
<?php
{$namespace}
{$abstract} class {$classname} extends {$extends} {$implements} {}
EOD;
        }
        private function interfaceTemplate(array $interface): string
        {
            $interfacename = $interface['interfacename'];
            $namespace = isset($interface['namespace']) ? "namespace {$interface['namespace']};" : '';
            $extends = isset($interface['namespace']) ? '\\' . implode('\ ,', $interface['extends']) : implode(', ', $interface['extends']);
            return <<<EOD
<?php
{$namespace}
interface {$interfacename} extends {$extends} {}
EOD;
        }
        private function traitTemplate(array $trait): string
        {
            $traitname = $trait['traitname'];
            $namespace = isset($trait['namespace']) ? "namespace {$trait['namespace']};" : '';
            $uses = isset($trait['namespace']) ? '\\' . implode(';' . \PHP_EOL . '    use \\', $trait['use']) : implode(';' . \PHP_EOL . '    use ', $trait['use']);
            return <<<EOD
<?php
{$namespace}
trait {$traitname} { 
    use {$uses}; 
}
EOD;
        }
    }
    spl_autoload_register([new \PShowEditorScoped\AliasAutoloader(), 'autoload']);
