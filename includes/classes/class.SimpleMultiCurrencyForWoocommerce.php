<?php
class SimpleMultiCurrencyForWoocommerce
{
    public $settings = [];
    public $licenseValidator;
    public $updateChecker;
    public $SimpleMultiCurrencyForWoocommerceProduct;

    public function __construct()
    {
        $this->settings['plugin-slug'] = 'simple-multi-currency-for-woocommerce';
        $this->settings['old-plugin-slug'] = 'simple-multi-currency-for-woocommerce';
        $this->settings['plugin-path'] = SMCFW_DIR_PATH;
        $this->settings['plugin-url'] = SMCFW_DIR_URL;
        $this->settings['plugin-name'] = 'Invelity WOO Advanced';
        $this->settings['plugin-license-version'] = '1.x.x';
        $this->initialize();
    }

    private function initialize()
    {
    	new InvelityPluginsAdmin($this);
        $this->SimpleMultiCurrencyForWoocommerceProduct = new SimpleMultiCurrencyForWoocommerceProduct($this);
    }

    public function getPluginSlug()
    {
        return $this->settings['plugin-slug'];
    }

    public function getPluginPath()
    {
        return $this->settings['plugin-path'];
    }

    public function getPluginUrl()
    {
        return $this->settings['plugin-url'];
    }
    public function getPluginName()
    {
        return $this->settings['plugin-name'];
    }
    public function getPluginLicenseVersion(){
        return $this->settings['plugin-license-version'];
    }
    public function getOldPluginSlug(){
        return $this->settings['old-plugin-slug'];
    }

}