<?php
namespace Concrete\Package\CoreTranslation\Controller\SinglePage\Dashboard\System\Multilingual;

use Zend\Http\Client\Adapter\Curl as CurlAdapter;
use Zend\Http\Client;
use SimpleXMLElement;
use Punic\Language;
use Config;
use Core;

class CoreTranslation extends \Concrete\Core\Page\Controller\DashboardPageController
{
    const GITHUB_URL = 'https://raw.githubusercontent.com/concrete5/concrete5-translations/master/';
    const STATS_FILE = 'stats-current.xml';
    
    public function on_start()
    {
        parent::on_start();
        
        $fh = Core::make('helper/file');
        $this->stats = $fh->getContents(self::GITHUB_URL . self::STATS_FILE);
    }
    
    public function view()
    {
        $resource_array = array();
        
        if (!$this->stats) {
            $this->error->add(t('Unable to load stats.'));
        } else {
            $xml = new SimpleXMLElement($this->stats);
            $resources = $xml->xpath('/stats/resource');
            if (is_array($resources)) {
                foreach ($resources as $resource) {
                    $resource_string = (string) $resource['name'];
                    $resource_array[$resource_string] = $resource_string;
                }
            }
        }
        
        $resource_array = array_reverse($resource_array);
        
        $this->set('resources', $resource_array);
    }
    
    public function select_language()
    {
        if ($this->token->validate("select_language")) {
            $selected = $this->post('resources');
            if (empty($selected)) {
                $this->error->add(t('Please select a resource.'));
            }
            if (!$this->stats) {
                $this->error->add(t('Unable to load stats.'));
            }
            
            if (!$this->error->has()) {
                $language_array = array();
                
                $xml = new SimpleXMLElement($this->stats);
                $resources = $xml->xpath('/stats/resource');
                if (is_array($resources)) {
                    foreach ($resources as $resource) {
                        if ($resource['name'] == $selected) {
                            foreach ($resource->children() as $language) {
                                $name = (string) $language['name'];
                                $site_locale = Config::get('concrete.locale');
                                $label = Language::getName($name, $site_locale);
                                $language_array[$name] = $label;
                            }
                        }
                    }
                }
                
                $this->set('resource', $selected);
                $this->set('languages', $language_array);
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
    }
    
    public function update_translation()
    {
        if ($this->token->validate("update_translation")) {
            $sh = Core::make('helper/security');
            $selected_resource = $sh->sanitizeString($this->post('resource'));
            $selected_language = $sh->sanitizeString($this->post('languages'));
            if (empty($selected_resource) || empty($selected_language)) {
                $this->error->add(t('Please select a language.'));
            } else {
                if (Language::getName($selected_language) == $selected_language) {
                    $this->error->add(t('Please select a valid language.'));
                }
            }
            
            if (!is_dir(DIR_LANGUAGES) || !is_writable(DIR_LANGUAGES)) {
                $this->error->add(t('You must create the directory %s and make it writable before you may run this tool. Additionally, all files within this directory must be writable.', DIR_LANGUAGES));
            }
            
            $language_dir = DIR_LANGUAGES . "/$selected_language/LC_MESSAGES";
            if (!file_exists($language_dir)) {
                @mkdir($language_dir, Config::get('concrete.filesystem.permissions.directory'), true);
            }
            
            if (!$this->error->has()) {
                $url = self::GITHUB_URL . $selected_resource . '/' . $selected_language . '.mo';
                
                $fh = Core::make('helper/file');
                $response = $fh->getContents($url);
                
                if ($response) {
                    $fh->clear($language_dir.'/messages.mo');
                    $fh->append($language_dir.'/messages.mo', $response);
                    $this->set('message', t('Translation updated.'));
                } else {
                    $this->error->add(t('Unable to get translation file: %s', $url));
                }
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
        $this->view();
    }
}
