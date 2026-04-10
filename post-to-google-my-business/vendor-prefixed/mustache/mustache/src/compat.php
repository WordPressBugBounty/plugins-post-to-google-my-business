<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2010-2025 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class_alias(\PGMB\Vendor\Mustache\Cache::class, \Mustache_Cache::class);
class_alias(\PGMB\Vendor\Mustache\Cache\AbstractCache::class, \Mustache_Cache_AbstractCache::class);
class_alias(\PGMB\Vendor\Mustache\Cache\FilesystemCache::class, \Mustache_Cache_FilesystemCache::class);
class_alias(\PGMB\Vendor\Mustache\Cache\NoopCache::class, \Mustache_Cache_NoopCache::class);
class_alias(\PGMB\Vendor\Mustache\Compiler::class, \Mustache_Compiler::class);
class_alias(\PGMB\Vendor\Mustache\Context::class, \Mustache_Context::class);
class_alias(\PGMB\Vendor\Mustache\Engine::class, \Mustache_Engine::class);
class_alias(\PGMB\Vendor\Mustache\Exception::class, \Mustache_Exception::class);
class_alias(\PGMB\Vendor\Mustache\Exception\InvalidArgumentException::class, \Mustache_Exception_InvalidArgumentException::class);
class_alias(\PGMB\Vendor\Mustache\Exception\LogicException::class, \Mustache_Exception_LogicException::class);
class_alias(\PGMB\Vendor\Mustache\Exception\RuntimeException::class, \Mustache_Exception_RuntimeException::class);
class_alias(\PGMB\Vendor\Mustache\Exception\SyntaxException::class, \Mustache_Exception_SyntaxException::class);
class_alias(\PGMB\Vendor\Mustache\Exception\UnknownFilterException::class, \Mustache_Exception_UnknownFilterException::class);
class_alias(\PGMB\Vendor\Mustache\Exception\UnknownHelperException::class, \Mustache_Exception_UnknownHelperException::class);
class_alias(\PGMB\Vendor\Mustache\Exception\UnknownTemplateException::class, \Mustache_Exception_UnknownTemplateException::class);
class_alias(\PGMB\Vendor\Mustache\HelperCollection::class, \Mustache_HelperCollection::class);
class_alias(\PGMB\Vendor\Mustache\LambdaHelper::class, \Mustache_LambdaHelper::class);
class_alias(\PGMB\Vendor\Mustache\Loader::class, \Mustache_Loader::class);
class_alias(\PGMB\Vendor\Mustache\Loader\ArrayLoader::class, \Mustache_Loader_ArrayLoader::class);
class_alias(\PGMB\Vendor\Mustache\Loader\CascadingLoader::class, \Mustache_Loader_CascadingLoader::class);
class_alias(\PGMB\Vendor\Mustache\Loader\FilesystemLoader::class, \Mustache_Loader_FilesystemLoader::class);
class_alias(\PGMB\Vendor\Mustache\Loader\InlineLoader::class, \Mustache_Loader_InlineLoader::class);
class_alias(\PGMB\Vendor\Mustache\Loader\MutableLoader::class, \Mustache_Loader_MutableLoader::class);
class_alias(\PGMB\Vendor\Mustache\Loader\ProductionFilesystemLoader::class, \Mustache_Loader_ProductionFilesystemLoader::class);
class_alias(\PGMB\Vendor\Mustache\Loader\StringLoader::class, \Mustache_Loader_StringLoader::class);
class_alias(\PGMB\Vendor\Mustache\Logger::class, \Mustache_Logger::class);
class_alias(\PGMB\Vendor\Mustache\Logger\AbstractLogger::class, \Mustache_Logger_AbstractLogger::class);
class_alias(\PGMB\Vendor\Mustache\Logger\StreamLogger::class, \Mustache_Logger_StreamLogger::class);
class_alias(\PGMB\Vendor\Mustache\Parser::class, \Mustache_Parser::class);
class_alias(\PGMB\Vendor\Mustache\Source::class, \Mustache_Source::class);
class_alias(\PGMB\Vendor\Mustache\Source\FilesystemSource::class, \Mustache_Source_FilesystemSource::class);
class_alias(\PGMB\Vendor\Mustache\Template::class, \Mustache_Template::class);
class_alias(\PGMB\Vendor\Mustache\Tokenizer::class, \Mustache_Tokenizer::class);

if (!class_exists(\Mustache_Engine::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Engine */
    class PGMB_Vendor_Mustache_Engine extends \PGMB\Vendor\Mustache\Engine
    {
    }
}

if (!interface_exists(\Mustache_Cache::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Cache */
    interface Mustache_Cache extends \PGMB\Vendor\Mustache\Cache
    {
    }
}

if (!class_exists(\Mustache_Cache_AbstractCache::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Cache\AbstractCache */
    abstract class PGMB_Vendor_Mustache_Cache_AbstractCache extends \PGMB\Vendor\Mustache\Cache\AbstractCache
    {
    }
}

if (!class_exists(\Mustache_Cache_FilesystemCache::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Cache\FilesystemCache */
    class PGMB_Vendor_Mustache_Cache_FilesystemCache extends \PGMB\Vendor\Mustache\Cache\FilesystemCache
    {
    }
}

if (!class_exists(\Mustache_Cache_NoopCache::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Cache\NoopCache */
    class PGMB_Vendor_Mustache_Cache_NoopCache extends \PGMB\Vendor\Mustache\Cache\NoopCache
    {
    }
}

if (!class_exists(\Mustache_Compiler::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Compiler */
    class PGMB_Vendor_Mustache_Compiler extends \PGMB\Vendor\Mustache\Compiler
    {
    }
}

if (!class_exists(\Mustache_Context::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Context */
    class PGMB_Vendor_Mustache_Context extends \PGMB\Vendor\Mustache\Context
    {
    }
}

if (!class_exists(\Mustache_Engine::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Engine */
    class PGMB_Vendor_Mustache_Engine extends \PGMB\Vendor\Mustache\Engine
    {
    }
}

if (!interface_exists(\Mustache_Exception::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Exception */
    interface Mustache_Exception extends \PGMB\Vendor\Mustache\Exception
    {
    }
}

if (!class_exists(\Mustache_Exception_InvalidArgumentException::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Exception\InvalidArgumentException */
    class PGMB_Vendor_Mustache_Exception_InvalidArgumentException extends \PGMB\Vendor\Mustache\Exception\InvalidArgumentException
    {
    }
}

if (!class_exists(\Mustache_Exception_LogicException::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Exception\LogicException */
    class PGMB_Vendor_Mustache_Exception_LogicException extends \PGMB\Vendor\Mustache\Exception\LogicException
    {
    }
}

if (!class_exists(\Mustache_Exception_RuntimeException::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Exception\RuntimeException */
    class PGMB_Vendor_Mustache_Exception_RuntimeException extends \PGMB\Vendor\Mustache\Exception\RuntimeException
    {
    }
}

if (!class_exists(\Mustache_Exception_SyntaxException::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Exception\SyntaxException */
    class PGMB_Vendor_Mustache_Exception_SyntaxException extends \PGMB\Vendor\Mustache\Exception\SyntaxException
    {
    }
}

if (!class_exists(\Mustache_Exception_UnknownFilterException::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Exception\UnknownFilterException */
    class PGMB_Vendor_Mustache_Exception_UnknownFilterException extends \PGMB\Vendor\Mustache\Exception\UnknownFilterException
    {
    }
}

if (!class_exists(\Mustache_Exception_UnknownHelperException::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Exception\UnknownHelperException */
    class PGMB_Vendor_Mustache_Exception_UnknownHelperException extends \PGMB\Vendor\Mustache\Exception\UnknownHelperException
    {
    }
}

if (!class_exists(\Mustache_Exception_UnknownTemplateException::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Exception\UnknownTemplateException */
    class PGMB_Vendor_Mustache_Exception_UnknownTemplateException extends \PGMB\Vendor\Mustache\Exception\UnknownTemplateException
    {
    }
}

if (!class_exists(\Mustache_HelperCollection::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\HelperCollection */
    class PGMB_Vendor_Mustache_HelperCollection extends \PGMB\Vendor\Mustache\HelperCollection
    {
    }
}

if (!class_exists(\Mustache_LambdaHelper::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\LambdaHelper */
    class PGMB_Vendor_Mustache_LambdaHelper extends \PGMB\Vendor\Mustache\LambdaHelper
    {
    }
}

if (!interface_exists(\Mustache_Loader::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Loader */
    interface Mustache_Loader extends \PGMB\Vendor\Mustache\Loader
    {
    }
}

if (!class_exists(\Mustache_Loader_ArrayLoader::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Loader\ArrayLoader */
    class PGMB_Vendor_Mustache_Loader_ArrayLoader extends \PGMB\Vendor\Mustache\Loader\ArrayLoader
    {
    }
}

if (!class_exists(\Mustache_Loader_CascadingLoader::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Loader\CascadingLoader */
    class PGMB_Vendor_Mustache_Loader_CascadingLoader extends \PGMB\Vendor\Mustache\Loader\CascadingLoader
    {
    }
}

if (!class_exists(\Mustache_Loader_FilesystemLoader::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Loader\FilesystemLoader */
    class PGMB_Vendor_Mustache_Loader_FilesystemLoader extends \PGMB\Vendor\Mustache\Loader\FilesystemLoader
    {
    }
}

if (!class_exists(\Mustache_Loader_InlineLoader::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Loader\InlineLoader */
    class PGMB_Vendor_Mustache_Loader_InlineLoader extends \PGMB\Vendor\Mustache\Loader\InlineLoader
    {
    }
}

if (!interface_exists(\Mustache_Loader_MutableLoader::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Loader\MutableLoader */
    interface Mustache_Loader_MutableLoader extends \PGMB\Vendor\Mustache\Loader\MutableLoader
    {
    }
}

if (!class_exists(\Mustache_Loader_ProductionFilesystemLoader::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Loader\ProductionFilesystemLoader */
    class PGMB_Vendor_Mustache_Loader_ProductionFilesystemLoader extends \PGMB\Vendor\Mustache\Loader\ProductionFilesystemLoader
    {
    }
}

if (!class_exists(\Mustache_Loader_StringLoader::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Loader\StringLoader */
    class PGMB_Vendor_Mustache_Loader_StringLoader extends \PGMB\Vendor\Mustache\Loader\StringLoader
    {
    }
}

if (!interface_exists(\Mustache_Logger::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Logger */
    interface Mustache_Logger extends \PGMB\Vendor\Mustache\Logger
    {
    }
}

if (!class_exists(\Mustache_Logger_AbstractLogger::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Logger\AbstractLogger */
    abstract class PGMB_Vendor_Mustache_Logger_AbstractLogger extends \PGMB\Vendor\Mustache\Logger\AbstractLogger
    {
    }
}

if (!class_exists(\Mustache_Logger_StreamLogger::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Logger\StreamLogger */
    class PGMB_Vendor_Mustache_Logger_StreamLogger extends \PGMB\Vendor\Mustache\Logger\StreamLogger
    {
    }
}

if (!class_exists(\Mustache_Parser::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Parser */
    class PGMB_Vendor_Mustache_Parser extends \PGMB\Vendor\Mustache\Parser
    {
    }
}

if (!interface_exists(\Mustache_Source::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Source */
    interface Mustache_Source extends \PGMB\Vendor\Mustache\Source
    {
    }
}

if (!class_exists(\Mustache_Source_FilesystemSource::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Source\FilesystemSource */
    class PGMB_Vendor_Mustache_Source_FilesystemSource extends \PGMB\Vendor\Mustache\Source\FilesystemSource
    {
    }
}

if (!class_exists(\Mustache_Template::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Template */
    abstract class PGMB_Vendor_Mustache_Template extends \PGMB\Vendor\Mustache\Template
    {
    }
}

if (!class_exists(\Mustache_Tokenizer::class)) {
    /** @deprecated use PGMB\Vendor\Mustache\Tokenizer */
    class PGMB_Vendor_Mustache_Tokenizer extends \PGMB\Vendor\Mustache\Tokenizer
    {
    }
}
