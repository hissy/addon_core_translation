<?php
namespace Concrete\Package\CoreTranslation;

use Concrete\Core\Backup\ContentImporter;

class Controller extends \Concrete\Core\Package\Package
{
    protected $pkgHandle = 'core_translation';
    protected $appVersionRequired = '5.7.3';
    protected $pkgVersion = '0.1.1';
    
    public function getPackageName()
    {
        return t('Core Translation Updater');
    }
    
    public function getPackageDescription()
    {
        return t('Download latest translation file from official concrete5 GitHub repository.');
    }

    public function install()
    {
        $pkg = parent::install();
        $ci = new ContentImporter();
        $ci->importContentFile($pkg->getPackagePath() . '/config/install.xml');
    }
}
